<? 
class Contacts extends Model_Simple
{
   protected $name = "Контакты";
 
   protected $model_elements = array(
       array("Адрес", "char", "address"),
       array("Номер телефона 1", "phone", "phone1"),
       array("Номер телефона 2", "phone", "phone2"),
       array("Номер телефона 3", "phone", "phone3"),
       array("Факс", "phone", "fax"),
      array("Почта", "char", "email"),
   );
}
?>