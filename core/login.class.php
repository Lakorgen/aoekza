<?
/**
 * Class for authorization of users in admin panel.
 * Also contains methods for auto login and recovering admin's password.
 * Creates CSRF token for login and remind password forms.
 */
class Login
{
	//Table with admins 'users'
	private $table;
	
	//Setting singleton object
	private $registry;
	
	//Database manager object
	private $db;
	
	//Internationlization manager object
	private $i18n;

	//Login attemps befor showing captcha
	static public $attempts = 3;

	public function __construct()
	{
		$this -> registry = Registry :: instance(); //Langs and settings
		$this -> db = DataBase :: instance(); //Manages database
		$this -> i18n = I18n :: instance();
		$this -> table = "users"; //Table with users data
		
		$time_zone = $this -> registry -> getSetting('TimeZone');
		
		if($time_zone)
			date_default_timezone_set($time_zone);

		if(!Service :: sessionIsStarted())
			session_start();

		if(!isset($_SESSION['login']['token']))
		{
			$_SESSION['login']['token'] = Service :: strongRandomString(50);
			$_SESSION['login']['ip'] = $_SERVER["REMOTE_ADDR"];
			$_SESSION['login']['browser'] = Debug :: browser();
		}
		
		unset($_SESSION['mv']); //Delete old session data
	}
	
	static public function getTokenCSRF()
	{
		$string = $_SERVER["REMOTE_ADDR"].$_SERVER["HTTP_USER_AGENT"].$_SESSION["login"]["token"];

		return Service :: createHash($string, "random");
	}

	static public function getAjaxInitialToken()
	{
		$string = Debug :: browser().$_SESSION["login"]["token"].$_SERVER["HTTP_USER_AGENT"];

		return Service :: createHash($string, "random");
	}

	static public function getJavaScriptToken()
	{
		$string = self :: getTokenCSRF().self :: getAjaxInitialToken();
		$string .= $_SESSION['login']['token'];

		return preg_replace("/\D/", "", $string);
	}	
	
	static public function getLogoutToken()
	{
		$string = $_SERVER["REMOTE_ADDR"].session_id().$_SERVER["HTTP_USER_AGENT"];

	    return Service :: createHash($string, "sha224");
	}
	
	public function loginUser($login, $password)
	{
		if(!isset($_SERVER["HTTP_USER_AGENT"]))
			return false;
		
		$row = $this -> db -> getRow("SELECT * FROM `".$this -> table."` 
							  	      WHERE `login`=".$this -> db -> secure($login));
		
		$arguments = func_get_args();
		$autologin = (isset($arguments[2]) && $arguments[2] == "autologin");
		
		//Compares the data came from user and status of user. If the user in blocked we don't let in
		if($row && $row['login'] == $login && ($row['active'] || $row['id'] == 1) && 
		  (Service :: checkHash($password, $row['password']) || ($autologin && $row['password'] == $password)))
		{
			$_SESSION['mv']['user']['id'] = $row['id'];
			$_SESSION['mv']['user']['password'] = md5($row['password']);
			$_SESSION['mv']['user']['token'] = Service :: strongRandomString(50);
			
			$data = "`date_last_visit`=".$this -> db -> now('with-seconds');
			
			if(!$row["date_registered"] || $row["date_registered"] == "0000-00-00 00:00:00")
				$data .= ", `date_registered`=".$this -> db -> now('with-seconds');
			
			//Updates the last visit of user
			$this -> db -> query("UPDATE `".$this -> table."` SET ".$data." WHERE `id`='".$row['id']."'");
						
			$session = new UserSession($row['id']); //Start new session for this user
			$session -> startSession();
			
			return $row['id'];
		}
		else
			return false;
	}
	
	public function reload($path)
	{
		header("Location: ".$this -> registry -> getSetting("AdminPanelPath").$path);
		exit();		
	}
	
	public function sendUserPassword($user_data)
	{		
		$code = Service :: strongRandomString(30); //Code to confirm the changes from email
		$key = Service :: strongRandomString(32);
		$token = Service :: makeHash($user_data['email'].$user_data['id'].$key);
		$token = str_replace("$2y$10$", "", $token);
		
		$this -> addPasswordToConfirm($user_data['id'], $key, $code);
		
		//Link for confirmation
		$link = $this -> registry -> getSetting("HttpAdminPanelPath")."login/recover.php?code=".$code;
		$link .= "&token=".$token;
		
		$time = floor($this -> registry -> getSetting("NewPasswordLifeTime") / 3600);
   		$arguments = array("number" => $time, "in-hour" => "*number");
   		
   		//Message text
		$message = "<p>".$user_data['name'].",<br />\n";
		$message .= $this -> i18n -> locale("change-password")."</p>\n";
		$message .= "<p>".$this -> i18n -> locale("confirm-time", $arguments)."</p>\n";
   		$message .= "<p><a href=\"".$link."\">".$link."</a></p>\n";
   		
   		$subject = $this -> i18n -> locale("password-restore");
		
		return Email :: send($user_data['name']." <".$user_data['email'].">", $subject, $message);
	}
	
	public function checkBrowserOldIE()
	{
		$agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : "";
		
		//Checks if the browser is old MSIE we deny the access.
		if(preg_match('/MSIE\s\d\.\d/', $agent))
		{
			$version = preg_replace("/.*MSIE\s(\d\.\d).*/", "$1", $agent);
			
			if($version < 9)
			{
				header("Location: ".$this -> registry -> getSetting("AdminPanelPath")."login/error.php?reason=ie");
				exit();
			}
		}
	}
	
	public function displayLoginErrors($errors)
	{	
		if(!is_array($errors) || !count($errors))
			return "";
		
		$html = "";
		
		foreach($errors as $error)
			$html .= "<p>".$error."</p>\n";
			
		return "<div class=\"errors\">".$html."</div>\n";
	}
	
	public function checkUserEmail($email)
	{	
		if($this -> db -> getCount($this -> table, "`email`=".$this -> db -> secure($email)) == 1)
			return $this -> db -> getRow("SELECT * FROM `".$this -> table."` 
										  WHERE `email`=".$this -> db -> secure($email)." 
										  AND `active`='1'");
		return false;
	}
	
	public function addPasswordToConfirm($user_id, $key, $code)
	{
		//Adds new password into DB wait list to confirm from email.
		$table = "users_passwords"; //Table with passwords for confirmation
		$time = $this -> registry -> getSetting("NewPasswordLifeTime");
		
		$this -> db -> query("DELETE FROM `".$table."` WHERE (".$this -> db -> unixTimeStamp('now')."-
							  ".$this -> db -> unixTimeStamp('date').") > ".$time." 
							  OR `user_id`='".$user_id."'"); //Deletes old not valid passwords from list
		
		//Adds new password to wait for the confirmation
		$this -> db -> query("INSERT INTO `".$table."`(`user_id`,`date`,`password`,`code`)
		                      VALUES('".$user_id."', ".$this -> db -> now().",'".$key."', '".$code."')");		
	}
	
	public function checkNewPasswordParams($code, $token)
	{
		$table = "users_passwords"; //Table with passwords for confirmation
		$time = $this -> registry -> getSetting("NewPasswordLifeTime");

		 //Checks if the password exist according to special code and it has valid time
		$row = $this -> db -> getRow("SELECT * FROM `".$table."`
		                              WHERE (".$this -> db -> unixTimeStamp('now')."-".
									  $this -> db -> unixTimeStamp('date').") < ".$time." 
									  AND `code`=".$this -> db -> secure($code));
		
		if($row && isset($row['user_id']))
		{
			if($user = $this -> db -> getRow("SELECT * FROM `".$this -> table."` WHERE `id`='".$row['user_id']."'"))
			{
				$string = $user['email'].$user['id'].$row['password'];
				
				if($this -> registry -> getInitialVersion() >= 2.2)
				    $token = "$2y$10$".$token;
				
				if(Service :: checkHash($string, $token))
					$_SESSION['login']['change-password'] = $row['user_id'];
			}			
			
			//Deletes data from list
			$this -> db -> query("DELETE FROM `".$table."` WHERE `user_id`='".$row['user_id']."'");
			
			return true;
		}
		
		return false;
	}
	
	public function saveNewPassword($user_id, $new_password)
	{
		$this -> db -> query("UPDATE `".$this -> table."` 
							  SET `password`='".Service :: makeHash($new_password)."'
							  WHERE `id`='".$user_id."'");
	}

	public function addNewLoginAttempt($login)
	{
		$login = $this -> db -> secure($login);
		
		$this -> db -> query("INSERT INTO users_logins (`login`,`date`,`ip_address`,`user_agent`) 
		                      VALUES(".$login.",".$this -> db -> now().",'".ip2long($_SERVER['REMOTE_ADDR'])."',
							  '".md5($_SERVER['HTTP_USER_AGENT'])."')");
	}

	public function checkAllAttemptsFromIp()
	{
		$time = (int) $this -> registry -> getSetting("LoginCaptchaLifeTime");
		$time = $time ? $time : 3600;
		$table = "users_logins";
		
		//Deletes all old data form table after required period of time
		$this -> db -> query("DELETE FROM `".$table."` 
							  WHERE (".$this -> db -> unixTimeStamp('now')."-".
							  $this -> db -> unixTimeStamp('date').") > ".$time);
							   
		//Checks all attempts to login from current ip address
		return $this -> db -> getCount("users_logins", "`ip_address`='".ip2long($_SERVER['REMOTE_ADDR'])."' 
										AND ((".$this -> db -> unixTimeStamp('now')."-".
							   			$this -> db -> unixTimeStamp('date').") < ".$time.")");
	}
	
	public function makeAutologinParams($id)
	{
	    $user = $this -> db -> getRow("SELECT * FROM `".$this -> table."` WHERE `id`='".intval($id)."'");
	    $code = $this -> registry -> getSetting("SecretCode");
	    
	    $key = md5($user["email"].$code.$user["id"].$user["login"]);
	    $code = $code.$user["id"].$user['login'].$user['password'].Debug :: browser();
        
	    return ["key" => $key, "code" => $code];
	}
	
	public function rememberUser($id)
	{
	    $params = $this -> makeAutologinParams($id);
	    
	    $params["code"] = password_hash($params["code"], PASSWORD_DEFAULT, array("cost" => 12));
	    $params["code"] = str_replace("$2y$12$", "", $params["code"]);
	    
		$time = $this -> registry -> getSetting("AutoLoginLifeTime");
		$time = $time ? time() + $time : time() + 3600 * 24 * 31;
		$http_only = $this -> registry -> getSetting("HttpOnlyCookie");
		$https = Router :: isHttps();
		
		$path = $this -> registry -> getSetting("AdminPanelPath");
		
		setcookie("remember_key", $params["key"], $time, $path, "", $https, $http_only);
		setcookie("remember_code", $params["code"], $time, $path, "", $https, $http_only);
	}
	
	public function cancelRemember()
	{
		$time = $this -> registry -> getSetting("AutoLoginLifeTime");
		$time = $time ? time() + $time : time() + 3600 * 24 * 31;
		$http_only = $this -> registry -> getSetting("HttpOnlyCookie");
		$https = Router :: isHttps();
		
		setcookie("remember_key", "", $time, $this -> registry -> getSetting("AdminPanelPath"), "", $https, $http_only);
		setcookie("remember_code", "", $time, $this -> registry -> getSetting("AdminPanelPath"), "", $https, $http_only);

		return $this;
	}
	
	public function autoLogin($key, $code)
	{
		$rows = $this -> db -> getAll("SELECT * FROM `".$this -> table."` WHERE `active`='1'");
		
		foreach($rows as $row)
		{
		    $params = $this -> makeAutologinParams($row["id"]);
		    
		    if($params["key"] == $key && password_verify($params["code"], "$2y$12$".$code))
		        return $this -> loginUser($row['login'], $row['password'], 'autologin');
		}
		
		return false;
	}
}
?>