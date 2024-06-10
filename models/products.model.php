<?
class Products extends Model
{
    protected $name = "Товары";

    protected $model_elements = array(
        array("Название", "char", "name", array("required" => true)),
        array("Описание", "text", "description"),
        array("Цена", "float", "price", array("required" => true)),
        array("Количество", "int", "quantity", array("required" => true))
    );

    public function findAll()
    {
        $rows = $this -> select();
        return $rows;
    }
}
?>