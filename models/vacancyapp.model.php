<?
class Vacancyapp extends Model
{
    protected $name = "Отклики на вакансии";

    protected $model_elements = array(
        array("Почта", "char", "apply_email", array("required" => true)),
        array("ФИО", "char", "apply_name", array("required" => true)),
        array("Номер телефона", "char", "apply_phone", array("required" => true)),
        array("Должность", "char", "apply_position"),
        array("Сообщение", "text", "apply_message"),
        array("Резюме", "file", "apply_resume", array("allowed_extensions" => array("txt", "doc", "docx"))),
        array("Дата создания", "date_time", "created_at"),
        array("Статус вакансии", "enum", "status", array("required" => true,
             "values_list" => array("Новая" => "Новая", "Завершена" => "Завершена", "Отклонена" => "Отклонена"))),
        array("Дата изменения статуса", "date_time", "status_changed_at")
    );

    public function findAll()
    {
        $rows = $this -> select();
        return $rows;
    }

    public function findNew()
    {
        $rows = $this -> select(array('status' => 'Новая'));
        return $rows;
    }
    public function findId($id)
    {
        $rows = $this -> select(array('id'=> $id));
        return $rows;
    }
    
}
?>
