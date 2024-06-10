<?
/**
 * Logs messages class for admin panel and common system text log.
 * Log files of framework are located at the /log/ folder
 */
class Log extends Model
{
	protected $name = "{users-operations}";
	
	protected $model_elements = array(
		array("{module}", "enum", "module"),
		array("{row_id}", "int", "row_id"),
		array("{date}", "date_time", "date"),
		array("{record}", "char", "name"),
		array("{user}", "enum", "user_id", array("foreign_key" => "Users")),
		array("{operation}", "enum", "operation", array("values_list" => array(
														"create" => "{creating}",
														"update" => "{editing}",
														"delete" => "{deleting}",
														"restore" => "{restoring}"
														)))				
	);
			
	protected $model_display_params = array(
		"hidden_fields" => array('row_id'),
		"create_actions" => false,
		"update_actions" => false,
		"delete_actions" => false
	);
						
	private static $log_file_max_size = 3072000;
	
	private static $max_log_files = 20;
	
	public function __construct()
	{
		$values_list = array();
		$db = Database :: instance();
		$registry = Registry :: instance();
		$values = $db -> getColumn("SELECT DISTINCT `module` FROM `log`");
		
		foreach($values as $model_class)
			if($registry -> checkModel($model_class))
				{	
					$object = new $model_class();
					$values_list[$model_class] = $object -> getName();
				}
		
		natcasesort($values_list);
		
		$this -> model_elements[0][] = array('values_list' => $values_list);
		
		parent :: __construct();
		
		$this -> elements['operation'] -> defineValuesList();
		$this -> elements['user_id'] -> defineValuesList();
	}
	
	static public function write($model, $row_id, $name, $user_id, $operation)
	{
		$db = Database :: instance();
		$db -> query("INSERT INTO `log`(`module`,`row_id`,`name`,`user_id`,`operation`,`date`) 
					  VALUES('".$model."','".$row_id."','".$name."','".$user_id."',
					  		 '".$operation."',".$db -> now('with-seconds').")");
	}
	
	static public function clean($user_id)
	{
		$db = Database :: instance();
		$db -> query("DELETE FROM `log` WHERE `user_id`='".$user_id."'");
	}
	
	static public function add($message, $file_name = false)
	{
		$registry = Registry :: instance();
		$i18n = I18n :: instance();		
		$message = I18n :: getCurrentDateTime("SQL")." ".$message;
		
		$folder = $registry -> getSetting("IncludePath")."log/";
		
		if(!is_dir($folder))
			return;
			
		if(!$file_name)
		{
			$file_name = isset($_SERVER["SERVER_NAME"]) ? strtolower($_SERVER["SERVER_NAME"]) : "errors";
			$file_name = $folder.preg_replace("/^www\./", "", $file_name);
		}
		else
			$file_name = $folder.$file_name;
		
		if(is_file($file_name.".log") && filesize($file_name.".log") >= self :: $log_file_max_size)
			for($i = 1; $i < self :: $max_log_files; $i ++)
				if((is_file($file_name.$i.".log") && filesize($file_name.$i.".log") < self :: $log_file_max_size) ||  
					!is_file($file_name.$i.".log"))
				{
					$file_name .= $i;
					break;
				}
		
		if(is_file($file_name.".log") && filesize($file_name.".log") >= self :: $log_file_max_size)
			return;
		
		$file = $file_name.".log";
		
	   	if($handle = @fopen($file, "at"))
	   	{
	   		$content = file_get_contents($file);	   		
	   		
	   		if(strpos($content, $message) === false)
  				fwrite($handle, $message."\r\n");
  			
   			fclose($handle);
   		}		
	}
}
?>