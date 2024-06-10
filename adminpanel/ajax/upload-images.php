<? 
include "../../config/autoload.php";

$system = new System('ajax');
$system -> ajaxRequestContinueOrExit();

if(isset($_POST['model'], $_POST['current_multi_images_field']) && !empty($_FILES))
	if($system -> registry -> checkModel($_POST['model']))
	{
		$system -> runModel(strtolower($_POST['model']));
		$data_for_json = $system -> model -> uploadMultiImages($_POST['current_multi_images_field']);
	}
	else
		$data_for_json = array("error" => I18n :: locale('upload-file-error'));

echo json_encode($data_for_json);
?>