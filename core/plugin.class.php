<?
abstract class Plugin extends Model_Initial
{
	//URL path from root of the host (domain)
    public $root_path;
	
	public function __construct()
	{
		$this -> registry = Registry :: instance(); 
		$this -> db = DataBase :: instance();
		$this -> table = strtolower(get_class($this));
		
		$this -> root_path = $this -> registry -> getSetting("MainPath");
	}
	
	public function getTable() { return $this -> table; }
	
	public function getId() { return $this -> id; }
	
	public function runPager($total, $limit, $current_page)
	{
		$this -> pager = new Pager($total, $limit);
		
		if($current_page)
			$this -> pager -> definePage($current_page);
	}
}
?>