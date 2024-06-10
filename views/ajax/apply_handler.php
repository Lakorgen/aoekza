<?php
include "../../config/autoload.php";
$mv = new Builder();

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    $form_data = array(
        'apply_email' => $_POST['apply_email'],
        'apply_name' => $_POST['apply_name'],
        'apply_phone' => $_POST['apply_phone'],
        'apply_position' => $_POST['apply_position'],
        'apply_message' => $_POST['apply_message'],
        'apply_resume' => $_FILES['apply_resume'],
        'agreement' => isset($_POST['agreement']) ? true : false 
    );

    $errors = array();

    if (empty($form_data['apply_email'])) {
        $errors[] = "Пожалуйста, введите ваш E-mail.";
    } elseif (!filter_var($form_data['apply_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введите корректный E-mail.";
    }
    if (empty($form_data['apply_name'])) {
        $errors[] = "Пожалуйста, введите ваше ФИО.";
    }
    if (empty($form_data['apply_phone'])) {
        $errors[] = "Пожалуйста, введите ваш телефон.";
    }
    if (empty($form_data['apply_position'])) {
        $errors[] = "Пожалуйста, выберите вакансию.";
    }
    if (empty($form_data['apply_message'])) {
        $errors[] = "Пожалуйста, введите ваше сообщение.";
    }
    if (empty($form_data['apply_resume']['name'])) {
        $errors[] = "Пожалуйста, прикрепите резюме.";
    }
    if (empty($form_data['agreement'])) {
        $errors[] = "Пожалуйста, примите пользовательское соглашение.";
    }

    $allowed_extensions = array("txt", "doc", "docx");
    $file_info = pathinfo($form_data['apply_resume']['name']);
    $file_extension = strtolower($file_info['extension']);
    if (!in_array($file_extension, $allowed_extensions)) {
        $errors[] = "Недопустимый формат файла. Разрешены только txt, doc, docx.";
    }


    if (!empty($errors)) {
        echo json_encode(array('status' => 'error', 'message' => $errors));
        exit;
    } else{

        $upload_dir = "../../userfiles/models/vacancyapp-files/";
        $file_name = uniqid() . '.' . $file_extension;
        $upload_file = $upload_dir . $file_name;

        if (!move_uploaded_file($form_data['apply_resume']['tmp_name'], $upload_file)) {
            echo json_encode(array('status' => 'error', 'message' => 'Произошла ошибка при загрузке файла.'));
            exit;
        }

        $Vacancyapp = new Vacancyapp();
        $new_request = $Vacancyapp->getEmptyRecord();

        $new_request->apply_email = $form_data['apply_email'];
        $new_request->apply_name = $form_data['apply_name'];
        $new_request->apply_phone = $form_data['apply_phone'];
        $new_request->apply_position = $form_data['apply_position'];
        $new_request->apply_message = $form_data['apply_message'];
        $new_request->apply_resume = 'userfiles/models/vacancyapp-files/' . $file_name; 
        $new_request->status = 'Новая';
        $new_request->created_at = I18n::getCurrentDateTime();
        $new_request->status_changed_at = I18n::getCurrentDateTime();

        $id = $new_request->create();
        $good = '';

        if ($id) {
            echo json_encode(array('status' => 'success', 'message' => "Отклик успешно отправлен! ID отклика: " . $id));
        } else {
            echo json_encode(array('status' => 'error', 'message' => 'Произошла ошибка при отправке отклика.'));
        }
    }
}
?>
