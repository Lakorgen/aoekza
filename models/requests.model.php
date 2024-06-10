<?
class Requests extends Model
{
    protected $name = "Заявки";

    protected $model_elements = array(
        array("ФИО", "char", "full_name", array("required" => true)),
        array("Email", "email", "email", array("required" => true)),
        array("Телефон", "phone", "phone", array("required" => true)),
        array("Название заявки", "char", "requests_title", array("required" => true)),
        array("Текст заявки", "text", "requests_text", array("required" => true)),
        array("Дата создания", "date_time", "date_created"),
        array("Статус заявки", "enum", "status", array("required" => true, "values_list" => array("Новая" => "Новая", "В обработке" => "В обработке", "Завершена"=>"Завершена", "Отклонена"=>"Отклонена"))),
        array("Дата изменения статуса", "date_time", "date_change")
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
    public function findProcessing()
    {
        $rows = $this -> select(array('status' => 'В обработке'));
        return $rows;
    }
    public function findEnd()
    {
        $rows = $this -> select(array('status' => 'Завершена'));
        return $rows;
    }
    public function findCancel()
    {
        $rows = $this -> select(array('status' => 'Отклонена'));
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
