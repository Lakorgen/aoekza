<?
include $mv -> views_path."main-header.php";
?>
<div class="container">
    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-body"></div>
        </div>
    </div>
    <h1 class="text-center">Вакансии</h1>
    <div class="cards">
        <?php
        $vacancy_model = new Vacancy();
        $vacancies = $vacancy_model->findOpen();
        foreach ($vacancies as $vacancy) {
            ?>
                <a href="#" class="card-link" data-id="<?php echo $vacancy['id']; ?>">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title nav-link"><?php echo $vacancy['title']; ?></h5>
                        </div>
                    </div>
                </a>
            <?php
            }
            ?>
        </div>
        <div class="contacts__form">
            <h3 class="text-center">Отклик на вакансию</h3>
            <div class="col-xl-12 col-sm-6">
                <form id="applyForm" class="d-flex flex-column gap-4 form-horizontal contacts-form" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="apply_email">E-mail</label>
                        <input type="email" class="form-control" name="apply_email" id="apply_email" placeholder="Введите ваш E-mail">
                    </div>
                    <div class="row">
                        <input type="hidden" name="phone">
                        <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <label for="apply_name">ФИО</label>
                            <input type="text" class="form-control" name="apply_name" id="apply_name" placeholder="Введите ваше ФИО">
                        </div>
                        <div class="form-group col-xs-12 col-sm-12 col-md-6">
                            <label for="apply_phone">Телефон</label>
                            <input type="tel" class="form-control" name="apply_phone" id="apply_phone" placeholder="Введите ваш телефон">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="apply_position">Название вакансии</label>
                        <select class="form-control" name="apply_position" id="apply_position">
                            <option value="" selected disabled>Выберите вакансию</option>
                            <?php
                                foreach ($vacancies as $vacancy) {
                                    echo "<option value=\"" . $vacancy['title'] . "\">" . $vacancy['title'] . "</option>";
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="apply_message">Сообщение</label>
                        <textarea class="form-control" rows="5" name="apply_message" id="apply_message" placeholder="Введите ваше сообщение"></textarea>
                </div>
                <div class="form-group">
                    <label for="apply_resume">Прикрепить резюме</label>
                    <input type="file" class="form-control" name="apply_resume" id="apply_resume">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="flexCheckDefault" name="agreement">
                    <label class="form-check-label" for="flexCheckDefault">
                        Согласие на обработку персональных данных в соответствии с
                        <a href="#" class="checkbox-label" data-target="#agreement" data-toggle="modal">пользовательским соглашением</a>
                    </label>
                </div>
                <div class="form-group">
                    <button type="button" id="applyFormBtn" class="contacts__btn">
                        Отправить отклик
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-send" viewBox="0 0 16 16">
                            <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576zm6.787-8.201L1.591 6.602l4.339 2.76z"/>
                        </svg>
                    </button>
                </div>
                <div id="formResponse"></div>
            </form>
        </div>
    </div>
</div>

<style>
/* The Modal (background) */
.modal {
    display: none; 
    position: fixed;
    z-index: 1; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    background-color: rgb(0,0,0); 
    background-color: rgba(0,0,0,0.4); 
    padding-top: 60px;
}

/* Modal Content */
.modal-content {
    border-radius: 8px;
    background-color: #fefefe;
    margin: 5% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
}

/* The Close Button */
.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}

.invalid {
    border-color: red;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    var modal = document.getElementById("modal");
    var span = document.getElementsByClassName("close")[0];
    $('.card-link').on('click', function(event) {
        event.preventDefault();
        var vacancyId = $(this).data('id');
        $.ajax({
            url: '/views/ajax/get_vacancy_details.php',
            method: 'GET',
            data: { id: vacancyId },
            success: function(response) {
                $('.modal-body').html(response);
                modal.style.display = "block";
            },
            error: function() {
                $('.modal-body').html('<p>Error fetching details.</p>');
                modal.style.display = "block";
            }
        });
    });
    span.onclick = function() {
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    $('#applyFormBtn').on('click', function() {
        var formData = new FormData($('#applyForm')[0]);
        var hasError = false;
        $('#applyForm .form-control').removeClass('invalid');
        $('#applyForm .form-check-input').removeClass('invalid');
        if (!formData.get('apply_email')) {
            $('#apply_email').addClass('invalid');
            hasError = true;
        }
        if (!formData.get('apply_name')) {
            $('#apply_name').addClass('invalid');
            hasError = true;
        }
        if (!formData.get('apply_phone')) {
            $('#apply_phone').addClass('invalid');
            hasError = true;
        }
        if (!formData.get('apply_position')) {
            $('#apply_position').addClass('invalid');
            hasError = true;
        }
        if (!formData.get('apply_message')) {
            $('#apply_message').addClass('invalid');
            hasError = true;
        }
        if (!formData.get('apply_resume').name) {
            $('#apply_resume').addClass('invalid');
            hasError = true;
        }
        if (!formData.get('agreement')) {
            $('#flexCheckDefault').addClass('invalid');
            hasError = true;
        }
        if (hasError) {
            $('#formResponse').html('<p style="color: red;">Пожалуйста, заполните все обязательные поля.</p>');
            return;
        }
        $.ajax({
            url: '/views/ajax/apply_handler.php', // замените на путь к вашему файлу обработчика
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                var jsonResponse = JSON.parse(response);
                if (jsonResponse.status === 'success') {
                    $('#formResponse').html('<p style="color: green;">' + jsonResponse.message + '</p>');
                    $('#applyForm')[0].reset();
                } else {
                    var errorMessage = '';
                    jsonResponse.message.forEach(function(error) {
                        errorMessage += '<p style="color: red;">' + error + '</p>';
                    });
                    $('#formResponse').html(errorMessage);
                }
            },
            error: function() {
                $('#formResponse').html('<p style="color: red;">Ошибка при отправке формы. Пожалуйста, попробуйте позже.</p>');
            }
        });
    });
});
</script>
<?php
include $mv->views_path . "main-footer.php";
?>
