<?
include $mv -> views_path."main-header.php";

?>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST)) {
    // Получаем данные из формы
    $form_data = array(
        'contacts_name' => $_POST['contacts_name'],
        'contacts_email' => $_POST['contacts_email'],
        'contacts_phone' => $_POST['contacts_phone'],
        'contacts_title' => $_POST['contacts_title'],
        'contacts_message' => $_POST['contacts_message'],
		'agreement' => isset($_POST['agreement']) ? true : false 
    );
    $errors = array();
    if (empty($form_data['contacts_name'])) {
        $errors[] = "Пожалуйста, введите ваше ФИО.";
    }
    if (empty($form_data['contacts_email'])) {
        $errors[] = "Пожалуйста, введите ваш E-mail.";
    } elseif (!filter_var($form_data['contacts_email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Введите корректный E-mail.";
    }
    if (empty($form_data['contacts_phone'])) {
        $errors[] = "Пожалуйста, введите ваш телефон.";
    }
    if (empty($form_data['contacts_title'])) {
        $errors[] = "Пожалуйста, введите заголовок.";
    }
    if (empty($form_data['contacts_message'])) {
        $errors[] = "Пожалуйста, введите ваше сообщение.";
    }
	if (empty($form_data['agreement'])) {
        $errors[] = "Пожалуйста, примите пользовательское соглашение.";
    }


    // Если есть ошибки, выводим их пользователю
    if (!empty($errors)) {
        
    } else {
        // Если ошибок нет, создаем новую заявку
        $requests = new Requests();
        $new_request = $requests->getEmptyRecord();

        // Устанавливаем значения полей
        $new_request->full_name = $form_data['contacts_name'];
        $new_request->email = $form_data['contacts_email'];
        $new_request->phone = $form_data['contacts_phone'];
        $new_request->requests_title = $form_data['contacts_title'];
        $new_request->requests_text = $form_data['contacts_message'];
        $new_request->status = 'Новая';
        $new_request->date_created = I18n::getCurrentDateTime();
        $new_request->date_change = I18n::getCurrentDateTime();

        // Сохраняем заявку
        $id = $new_request->create();
		$good = '';
        // Проверка результата и вывод сообщения пользователю
        if ($id) {
            $good =  "Заявка успешно отправлена! ID заявки: " . $id . "</p>";
        } else {
            echo "<p style='color: red;'>Произошла ошибка при отправке заявки.</p>";
        }
    }
}
$_POST = null;
?>

<div class="container">
    <h1 class="title__contacts text-center">Контакты</h1>
	<div class="contacts__inner">
		<div class="contacts__info">
            <h4 class="title__mini">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-geo-alt-fill" viewBox="0 0 20 20">
				<path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10m0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6"/>
				</svg>Адрес</h4>
			<p class="address mb-4">
				<span class="nav-link" id="address">446200, Российская Федерация, Самарская обл., г.Новокуйбышевск, ул.Энергетиков, 7.</span><br>
			</p>
            <h4 class="title__mini">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 20 20">
  <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/>
</svg>Номера телефонов</h4>
            <p class="contact-info">
                <span>Тeл.:</span><a href="tel:+78463774695" class="nav-link">(846) 377-46-95</a><br>
                <span></span><a href="tel:+78463774694" class="nav-link">(846) 377-46-94 + доб.</a><br>
                <span></span><a href="tel:+78463533192" class="nav-link">(846-35) 33-192</a><br><br>
                <span>Факс:</span><a href="tel:+78463774694" class="nav-link">(846) 377-46-94 доб. 113</a>
            </p>
            <h4 class="title__mini">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-envelope" viewBox="0 0 20 20">
  <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
</svg>	Электроная почта</h4>
			<p class="contacts__email "><a class="nav-link" href="mailto:secretary@aoekza.ru">secretary@aoekza.ru</a>
			</p>
		</div>
		<div class="contacts__map">
			<div class="map-container">
				<iframe class="map" src="https://yandex.ru/map-widget/v1/?ll=49.873324%2C53.095025&amp;mode=search&amp;oid=228192191063&amp;ol=biz&amp;z=16.75" width="100%" height="500" frameborder="0" allowfullscreen="true">
				</iframe>
			</div>
		</div>
	</div>
	<div class="contacts__form">
		<h3 class="text-center">Заявка на сотрудничество</h3>
		<div class="col-xl-12 col-sm-6">
			<form action="<?php echo $mv->root_path; ?>contacts/" method="post" class="d-flex flex-column  gap-4 form-horizontal contacts-form" id="contactsForm" novalidate="novalidate">
				<div class="form-group">
					<label for="contacts_email">E-mail</label>
					<input type="email" class="form-control" name="contacts_email" id="contacts_email" placeholder="Введите ваш E-mail">
				</div>
				<div class="row">
					<input type="hidden" name="phone">
					<div class="form-group col-xs-12 col-sm-12 col-md-6">
						<label for="contacts_name">ФИО</label>
						<input type="text" class="form-control" name="contacts_name" id="contacts_name" placeholder="Введите ваше ФИО">
					</div>
					<div class="form-group col-xs-12 col-sm-12 col-md-6">
						<label for="contacts_phone">Телефон</label>
						<input type="tel" class="form-control" name="contacts_phone" id="contacts_phone" placeholder="Введите ваш телефон">
					</div>
				</div>
				<div class="form-group">
					<label for="contacts_email">Заголовок</label>
					<input type="text" class="form-control" name="contacts_title" id="contacts_title" placeholder="Введите Заголовок">
				</div>
				<div class="form-group">
					<label for="contacts_message">Сообщение</label>
					<textarea class="form-control" rows="5" name="contacts_message" id="contacts_message" placeholder="Введите ваше сообщение"></textarea>
				</div>
				<div class="checkbox form-group">
					<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" name="agreement">
					<label class="form-check-label" for="flexCheckDefault">
							Согласие на обработку персональных данных в соответствии с
							<a href="#" class="checkbox-label" data-target="#agreement" data-toggle="modal">пользовательским соглашением</a>
					</label>
				</div>
				<div class="form-group">
					<button type="submit" id="contactsFormBtn" class="contacts__btn">Отправить сообщение
					<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
						<path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
						</svg>
					</button>
				</div>
				<?php
					if (!empty($errors)) {
						echo "<div style='color: red;'>";
						foreach ($errors as $error) {
							echo "<p>$error</p>";
						}
						echo "</div>";
					}
					if(!empty($good)) {
						echo "<div style='color: green;'>";
							echo "<p>$good</p>";
						}
						echo "</div>";
					?>

				</form>
		</div>
	</div>
</div>
<?
include $mv -> views_path."main-footer.php";
?>