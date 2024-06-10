<?
class Carts extends Model
{
	protected $name = "Корзина";
	
	protected $model_elements = array(
        array("Пользователь", "int", "account_id"),
        array("Товар", "int", "product_id"),
        array("Количество", "int", "product_count"),
        array("Создано", "date_time", "created_at"),
        array("Обновлено", "date_time", "updated_at")
    );

    public function addToCart($accountId, $productId, $productCount) {
        // Проверяем, авторизован ли пользователь (ваша логика проверки авторизации)
        $accounts = new Accounts();
        $user = $accounts->checkAuthorization();
        if ($user) {
            // Получаем ID текущего пользователя, который добавляет товар в корзину
            $account_id = $user['id']; // Пример
    
            // Проверяем, есть ли уже этот товар в корзине у текущего пользователя
            $query = "SELECT * FROM `" . $this->table . "` WHERE `account_id`=:accountId AND `product_id`=:productId";
            $params = array(":accountId" => $accountId, ":productId" => $productId);
            $cart_items = $this->db->query($query, $params);
    
            if (!empty($cart_items)) {
                // Если товар уже есть в корзине, обновляем количество
                $cart_item = $cart_items[0]; // Предполагаем, что товар уникален для пользователя
                $cart_item['product_count'] += $productCount;
                $cart_item['updated_at'] = I18n::getCurrentDateTime();
                // Здесь может потребоваться выполнить обновление в базе данных с использованием метода вашей модели
                // Например: $this->update($cart_item);
            } else {
                // Если товара еще нет в корзине, создаем новую запись
                $data = array(
                    "account_id" => $accountId,
                    "product_id" => $productId,
                    "product_count" => $productCount,
                    "created_at" => I18n::getCurrentDateTime(),
                    "updated_at" => I18n::getCurrentDateTime()
                );
                $this->db->insert($this->table, $data);
            }
            echo "Товар успешно добавлен в корзину.";
        } else {
            echo "Пользователь не авторизован.";
        }
    }
}
?>