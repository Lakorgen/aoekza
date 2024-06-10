<?
class Accounts extends Model
{
	protected $name = "Аккаунты";
	
	protected $model_elements = array(
        array("Активировать", "bool", "active", array("on_create" => true)),
        array("Дата регистрации", "date_time", "date_registration"),
        array("Дата последнего посещения", "date_time", "date_last_visit", array("now_on_create" => false)),
        array("Имя", "char", "name", array("required" => true)),
        array("Email", "email", "email", array("required" => true)),
        array("Пароль", "password", "password", array("required" => true, "letters_required" => true,
                                                      "digits_required" => true)),
        array("Телефон", "phone", "phone")
    );

	protected function beforeCreate($fields)
    {
        $salt = $this -> createPasswordSalt();
        return array("password" => Service :: makeHash($fields["password"].$salt));
    }
 
    protected function beforeUpdate($id, $old_fields, $new_fields)
    {
        if($new_fields["password"] != $old_fields["password"])
        {
            $salt = $this -> createPasswordSalt();
            return array("password" => Service :: makeHash($new_fields["password"].$salt));
        }
    }
 
    public function createPasswordSalt()
    {
        $salt = $this -> registry -> getSetting("SecretCode");
        $salt .= "hg3HgLi826gfvd)jsh";
        return $salt;
    }

	public function generateSessionToken($account)
	{
		$token = $account -> id.$this -> registry -> getSetting('SecretCode');
		$token .= Debug :: browser().session_id();
			
		return md5($token);
	}
	
	public function login($email, $password)
	{
		$account = $this -> findRecord(array("email" => $email, "active" => 1));
		$salt = $this -> createPasswordSalt();
			
		if($account && Service :: checkHash($password.$salt, $account -> password))
		{
			$_SESSION["account"]["id"] = $account -> id;
			$_SESSION["account"]["password"] = md5($account -> password);
			$_SESSION["account"]["token"] = $this -> generateSessionToken($account);
				
			$account -> date_last_visit = I18n :: getCurrentDateTime();
			$account -> update();         
				
			return $account;
		}
	}
		
	public function checkAuthorization()
	{
		if(isset($_SESSION["account"]["id"], $_SESSION["account"]["password"], $_SESSION["account"]["token"]))
		{
			$account = $this -> findRecord(array("id" => $_SESSION["account"]["id"], "active" => 1));
				
			if($account && $_SESSION["account"]["password"] == md5($account -> password))
				if($_SESSION["account"]["token"] == $this -> generateSessionToken($account))
					return $account;
		}
	}
}
?>