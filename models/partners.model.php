<?
class Partners extends Model
{
    protected $name = "Партнеры и клиенты";

    protected $model_elements = array(
        array("Название", "char", "name", array("required" => true)),
        array("Тип", "enum", "type", array("values_list" => array("Акционер" => "Акционер", "Партнер" => "Партнер")))
    );

    public function findAll()
    {
        $rows = $this -> select();
        return $rows;
    }

    public function findShareholders()
    {
        $rows = $this -> select(array('type' => 'Акционер'));
        return $rows;
    }
    
    public function findpart()
    {
        $rows = $this -> select(array('type' => 'Партнер'));

        return $rows;
    }
}
?>