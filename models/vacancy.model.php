<?
class Vacancy extends Model
{
    protected $name = "Вакансии";

    protected $model_elements = array(
        array("Название вакансии", "char", "title", array("required" => true)),
        array("Описание", "text", "description", array("required" => true)),
        array("Требования", "text", "requirements", array("required" => true)),
        array("Дата публикации", "date_time", "date_published"),
        array("Статус вакансии", "enum", "status", array("required" => true, "values_list" => array("Открыта" => "Открыта", "Закрыта" => "Закрыта", "В архиве" => "В архиве"))),
        array("Дата изменения статуса", "date_time", "date_status_changed")
    );

    public function findAll()
    {
        $rows = $this -> select();
        return $rows;
    }

    public function findOpen()
    {
        $rows = $this -> select(array('status' => 'Открыта'));
        return $rows;
    }
    public function findId($id)
    {
        $rows = $this -> select(array('id'=> $id));
        return $rows;
    }
    
    // Можно добавить методы для работы с заявками, если потребуется

    protected function beforeCreate($fields)
    {
        $fields['date_created'] = I18n::getCurrentDateTime();
        return $fields;
    }

    protected function beforeUpdate($id, $old_fields, $new_fields)
    {
        // Дополнительная логика перед обновлением заявки, если потребуется
        return $new_fields;
    }

    // Пример метода для изменения статуса заявки
    public function changeStatus($id, $new_status)
    {
        $application = $this->findRecord(array("id" => $id));
        if ($application) {
            $application->status = $new_status;
            return $application->update();
        }
        return false;
    }
}
?>
