<?
$form = new Form("Accounts");
$form -> setRequiredFields(array("email", "password"));
$form -> setHtmlParams(array("email", "password"), 'class="form-control mt-2 mb-3"');
$form -> useTokenCSRF();
 
if(!empty($_POST))
{
    $form -> getDataFromPost() -> validate(array("email", "password"));
     
    if($form -> isValid())
        if(!$account = $mv -> accounts -> login($form -> email, $form -> password))
            $form -> addError("Неверный email или пароль.");
        else
            $mv -> redirect("lk"); //Переход на нужный нам URL
 
    $form -> password = "";
}
 
include $mv -> views_path."main-header.php";
?>
    <? echo $form -> displayErrors(); ?>
    <main class="form-signin w-25 mx-auto text-left ">
        <h2 class="h3 mb-3 fw-bold text-center fs-3">Авторизация</h2>
        <form class="login__form w-100 mt-2" method="post" action="<?php echo $mv -> root_path; ?>login/">
            <table class="mb-2 w-100">
                <?php echo $form -> displayVertical(array("email", "password")); ?>
            </table>
            <div class="form-buttons">
                <?php echo $form -> displayTokenCSRF(); ?>
                <input class="btn-more  w-100" type="submit" value="Вход" />
            </div>
        </form>
    </main>



   
<?
include $mv -> views_path."main-footer.php";
?>