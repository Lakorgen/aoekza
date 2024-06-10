<?php
if(!empty($_POST)){

    $id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $accounts = new Accounts();
    $user = $accounts->checkAuthorization();
    if ($user) {
        $name_user = $user->name;
        $email_user = $user->email;
        $phone_user = $user->phone;
    } else {
        $name_user = "Пользователь не авторизован";
    }
    $token = "7087459376:AAGk9IQALF-kHDtvzSB79NJ-dY5ZAmN5Z1U";
    $chat_id = "-1002074115049";
    $txt = ''; 
    $arr = array(
        'id: ' => $id,
        'quantity: ' => $quantity,
        'Пользователь' => $name_user,
        'Почта' => $email_user,
        'Телефон' => $phone_user
    );
    
    foreach($arr as $key => $value) {
        $txt .= "<b>".$key."</b> ".$value."%0A";
    };
    
    $sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text={$txt}","r");
    $_POST = null;
  /*  if ($sendToTelegram) {
        header('Location: success.html');
    } else {
        echo "Error";
    }*/
}
?>

<?
include $mv -> views_path."main-header.php";
?>
<div class="container">
    <h1>Каталог товаров</h1>
    <div class="carts d-flex justify-content-center py-3 gap-5">        
    <?php
$product_model = new Products();

// Получение всех товаров из базы данных
$products = $product_model->findAll();

// Отображение каждого товара
foreach ($products as $product) {
?>
    <div>
        <h2><?php echo $product['name']; ?></h2>
        <p><?php echo $product['description']; ?></p>
        <p>Цена: <?php echo $product['price']; ?> руб.</p>
        <p>Количество: <?php echo $product['quantity']; ?></p>
        <form action='<?php echo $mv->root_path; ?>products/' method='post'>
            <input type='hidden' name='product_id' value='<?php echo $product['id']; ?>'>
            <input type='number' name='quantity' value='1' min='1' max='<?php echo $product['quantity']; ?>'>
            <input type='submit' name='add_to_cart' value='Добавить в корзину'>
        </form>
    </div>
<?php
}
?>

</div>
</div>
<?
include $mv -> views_path."main-footer.php";
?>