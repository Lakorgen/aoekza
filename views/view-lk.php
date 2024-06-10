<?
include $mv -> views_path."main-header.php";
?>

<?php
$accounts = new Accounts();
$user = $accounts->checkAuthorization();

if ($user) {
    echo "Пользователь: " . $user->name;
} else {
    echo "Пользователь не авторизован";
}
?>
<?
include $mv -> views_path."main-footer.php";
?>