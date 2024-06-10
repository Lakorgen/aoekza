<?
/**
 * Special static methods to use anywhere inside a project.
 */
class Service
{
	static public function addFileRoot($path)
	{
		//Adds root part of path to file according to current host
		return Registry :: instance() -> getSetting("IncludePath").$path;
	}
	
	static public function removeFileRoot($path)
	{
		if(!$path) return;

		//Makes file suitable for html tag, which should not have the root server part of url
		$root = str_replace("/", "\/", Registry :: instance() -> getSetting("IncludePath"));
		return preg_replace("/^".$root."/", "", $path);
	}
	
	static public function removeDocumentRoot($path)
	{
		if(!$path) return;

		$root = str_replace("/", "\/", Registry :: instance() -> getSetting("DocumentRoot"));
		return preg_replace("/^".$root."/", "", $path);
	}
	
	static public function addRootPath($path)
	{
		return Registry :: instance() -> getSetting("MainPath").self :: removeFileRoot($path);
	}
	
	static public function removeRootPath($path)
	{
		if(!$path) return;
		
		$root = str_replace("/", "\/", Registry :: instance() -> getSetting("MainPath"));
		return preg_replace("/^".$root."/", "", $path);
	}
	
	static public function setFullHttpPath($path)
	{
		$domain = Registry :: instance() -> getSetting("DomainName");
		return preg_replace("/\/$/", "", $domain).self :: addRootPath($path);
	}
	
	static public function getPermissions($file)
    {
    	//Takes the UNIX rights for the file (like 0774).
	   $permissions = decoct(fileperms($file));
	   return substr($permissions, strlen($permissions) - 3, strlen($permissions));
    }
    
	static public function getExtension($file)
	{	
		//Finds extention of the file.
		return strtolower(substr($file, strrpos($file, '.') + 1));
	}

	static public function removeExtension($file)
	{
		//Returns the file name wihout the extension.
		return substr($file, 0, strrpos($file, '.'));
	}
	
	static public function translateFileName($file_name)
	{
		$file_name = I18n :: translitUrl(self :: removeExtension(trim($file_name)));
		
		if(!$file_name)
			return "";

		//Clean transformed name of file
		$file_name = str_replace("_", "-", $file_name);
		$file_name = preg_replace("/[^a-z0-9-]/ui", "", $file_name);		
		$file_name = preg_replace("/-+/", "-", $file_name);
		$file_name = preg_replace("/^-+/", "", $file_name);
		$file_name = preg_replace("/-+$/", "", $file_name);
		
		return $file_name;
	}
	
	static public function prepareFilePath($file_path)
	{
		$file_dir = dirname($file_path)."/";
		$extension = self :: getExtension(basename($file_path));
		$extension = ($extension == "jpeg") ? ".jpg" : ".".$extension;
		$file_name = self :: translateFileName(basename($file_path));
		
		if(!$file_name || file_exists($file_dir.$file_name.$extension))
		{
			$registry = Registry :: instance();
			$counter = intval($registry -> getDatabaseSetting('files_counter')) + 1;
			$registry -> setDatabaseSetting('files_counter', $counter);

			if(!$file_name) //File name is empty after transformation
				return $file_dir."f".$counter.$extension;
			else //File with such name was already uploaded
				return $file_dir.$file_name."-f".$counter.$extension;
		}
		else //File name wsa transformed successfully
			return $file_dir.$file_name.$extension;
	}
	
	static public function randomString($length)
	{
		//Available symbols
		$chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m',
					   'n','p','r','t','u','v','w','x','y','z',1,2,3,4,6,7,8,9);
		
		$arguments = func_get_args();
		
		if(isset($arguments[1]) && $arguments[1] == "only-letters")
			array_splice($chars, -8); //Only letters go into random array
		
		$number = count($chars) - 1;		
		$string = ""; //Result string

		for($i = 0; $i < $length; $i ++)
			$string .= $chars[mt_rand(0, $number)]; //Adds next random symbol
		
		return $string; 		
	}
	
	static public function strongRandomString($length)
	{
		$chars = array('a','b','c','d','e','f','g','h','i','j','k','l','m',
					   'n','o','p','q','r','s','t','u','v','w','x','y','z',
					   'A','B','C','D','E','F','G','H','I','J','K','L','M',
					   'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
					   0,1,2,3,4,6,7,8,9);
		
		$number = count($chars) - 1;
		$string = "";
			
		for($i = 0; $i < $length; $i ++)
			$string .= $chars[mt_rand(0, $number)];
				
		return $string;
	}
	
	static public function serializeArray($arr)
	{
		//Serialization (packing) of array (usually POST) into string
		$mass = array();
		
		foreach($arr as $key => $val)
			$mass[$key] = htmlspecialchars($val);

		return base64_encode(serialize($mass));
	}
	
	static public function unserializeArray($var)
	{
		//Unpacks the serialized data into normal array
		$mass = unserialize(base64_decode($var));
		
		if(!is_array($mass))
			return array();
		
		foreach($mass as $key => $val)
			$mass[$key] = htmlspecialchars_decode($val);
				
		return $mass;
	}	
	
	static public function roundTo05($number)
	{
		//Rounds given number to 0.5
		$int = floor($number); //Integer part
		$float = $number - $int; //Float part
		
		if($float < 0.25) //Rounding
			$float = 0;
		else if($float >= 0.25 && $float < 0.75)
			$float = 0.5;
		else if($float >= 0.75)
			$float = 1;
		
		return $int + $float; //Final number with int and float parts
	}
	
	static public function cutText($text, $length)
	{
		//Gets rid of html tags and cuts the tail of text to fixed number of symbols according to words
		$text = strip_tags($text);
		
		if($text === "")
			return "&nbsp;"; //Empty string after tags deleting

		if(mb_strlen($text, 'utf-8') <= $length) //If the text fit to needed length
			return $text;
		
		$text = mb_substr($text, 0, $length, 'utf-8'); //Cut of text
		
		$arguments = func_get_args();
		$end = isset($arguments[2]) ? $arguments[2] : "";
		
		 //Cut the possible part of last word
		return mb_substr($text, 0, mb_strrpos($text, " ", 0, 'utf-8'), 'utf-8').$end;
	}
	
	static public function cleanHtmlSpecialChars($string)
	{
		//To avoid double effect from htmlspecialchars() in setValue() methods
		$search = array("&amp;amp;","&amp;quot;","&amp;#039;","&amp;gt;","&amp;lt;","&amp;#92;");
		$replace = array("&amp;","&quot;","&#039;","&gt;","&lt;","&#92;");
		$string = is_null($string) ? "" : $string;

		return str_replace($search, $replace, $string);
	}
	
	static public function displayOrderedFormTable($data, $columns, $checked, $name)
	{
		$columns_number = intval($columns);
		$rows_number = ceil(count($data) / $columns_number);
		$arguments = func_get_args();
		$radio_buttons = (isset($arguments[4]) && $arguments[4] == "radio");
		$current_row = $current_column = 1;
		$table_data = [];
				
		foreach($data as $key => $title)
		{
			if($current_row > $rows_number)
			{
				$current_row = 1;
				$current_column ++;
			}
			
			$table_data[$current_row][$current_column] = array($key, $title);
			$current_row ++;
		}
		
		$css_class = $radio_buttons ? "enum-radio-choice" : "enum-multiple-choice";
		$html = "<table id=\"".$name."-ordered-table\" class=\"".$css_class."\">\n";
		
		for($i = 1; $i <= $rows_number; $i ++)
		{
			$html .= "<tr>\n";
			
			for($j = 1; $j <= $columns_number; $j ++)
				if(isset($table_data[$i][$j]))
				{
					$html .= "<td>\n<input id=\"".$name."-".$table_data[$i][$j][0]."\" ";
					$html .= "type=\"".($radio_buttons ? "radio" : "checkbox")."\" ";
					$html .= "name=\"".$name.($radio_buttons ? "" : "-".$table_data[$i][$j][0])."\"";
					
					if($radio_buttons && !is_array($checked) && $checked == $table_data[$i][$j][0])
						$html .= " checked=\"checked\"";
					else if(!$radio_buttons && in_array($table_data[$i][$j][0], $checked))
						$html .= " checked=\"checked\"";
						
					$html .= " value=\"".$table_data[$i][$j][0]."\" />\n";
					$html .= "<label for=\"".$name."-".$table_data[$i][$j][0]."\">".$table_data[$i][$j][1]."</label></td>\n";					
				}
			
			$html .= "</tr>\n";
		}
		
		return $html."</table>\n";
	}
	
	static public function prepareRegularExpression($string)
	{
		$search = array("+", ".", "*", "?", "(", ")", "^", "$", "[", "]", "/", "{", "}");
		$replace = array("\+", "\.", "\*", "\?", "\(", "\)", "\^", "\\$", "\[", "\]", "\/", "\{", "\}");
		return str_replace($search, $replace, $string);
	}
	
	static public function sessionIsStarted()
	{
		if(version_compare(phpversion(), '5.4.0', '>='))			
			return session_status() === PHP_SESSION_ACTIVE;
		else
			return session_id() === '' ? false : true;
	}
	
	static public function makeHash($string, $cost = 10)
	{
		if(Registry :: instance() -> getInitialVersion() < 2.2)
			return md5($string);
		
		$options = array("cost" => $cost);
		return password_hash($string, PASSWORD_DEFAULT, $options);
	}
	
	static public function checkHash($string, $hash)
	{
		if(Registry :: instance() -> getInitialVersion() < 2.2)
			return (md5($string) == $hash);
		
		return password_verify($string, $hash);
	}

	static public function createHash($string, $algo = false)
	{
		$allowed = ["sha224", "sha256", "sha384", "sha512/224", "sha512/256", "sha512", "sha3-224", "sha3-256", "sha3-384",
					"sha3-512", "ripemd160", "ripemd256", "ripemd320", "whirlpool", "tiger160,3", "tiger192,3",
					"tiger160,4", "tiger192,4", "snefru", "snefru256", "gost", "gost-crypto", "haval160,3", "haval192,3", 
					"haval224,3", "haval256,3", "haval160,4", "haval192,4", "haval224,4", "haval256,4", "haval160,5", 
					"haval192,5", "haval224,5", "haval256,5"];

		$system_algos = hash_algos();

		if($algo && in_array($algo, $allowed) && in_array($algo, $system_algos))
			return hash($algo, $string);

		$code = Registry :: instance() -> getSetting("SecretCode");
		$code = (int) preg_replace("/\D/", "", $code);

		if($algo == "random" && $code)
		{
			$index = (int) $code % count($allowed);

			if(isset($allowed[$index]) && in_array($allowed[$index], $system_algos))
				return hash($allowed[$index], $string);
			else
				return hash("sha256", $string);
		}

		return hash("sha256", $string);
	}
}
?>