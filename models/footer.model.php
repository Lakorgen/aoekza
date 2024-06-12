<? 
class Footer extends Model_Simple
{
   protected $name = "Footer";
 
   protected $model_elements = array(
      array("Почта", "char", "email"),
      array("Номер телефона", "phone", "phone")
   );
}
?>