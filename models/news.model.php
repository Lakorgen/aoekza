<?
class News extends Model
{
    protected $name = "Новости";

    protected $model_elements = array(
        array("Название", "char", "name", array("required" => true)),
        array("Описание", "text", "description"),
        array("Дата", "date", "date", array("required" => true)),
        array("Фото1", "file", "photo1"),
        array("Фото2", "file", "photo2"),
        array("Фото3", "file", "photo3"),
        array("Фото4", "file", "photo4")
    );

    public function findAll()
    {
        $rows = $this -> select(array("order->desc" => "date"));
        return $rows;
    }
}
?>