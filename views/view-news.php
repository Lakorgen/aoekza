<?
include $mv -> views_path."main-header.php";
?>
<div class="container">
    <h1 class="text-center">Новости</h1>
    <div class="news d-flex justify-content-center py-3 gap-5">        
    <?php
$product_model = new News();
$news = $product_model->findAll();
$items_per_page = 10;
$total_pages = ceil(count($news) / $items_per_page);
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;
$news_on_page = array_slice($news, $offset, $items_per_page);
foreach ($news_on_page as $new) {
?>
    <div class="news__item">
        <h3 class="nav-link"><?php echo date('d.m.Y', strtotime($new['date']));?> - <?php echo $new['name']; ?></h3>
        <p><?php echo $new['description']; ?></p>
        <div class="photo_item">
            <?php
            $photos = [$new['photo1'], $new['photo2'], $new['photo3'], $new['photo4']];
            foreach ($photos as $photo) {
                if ($photo !== "") {
                    echo '<img src="/' . $photo . '" alt="">';
                }
            }
            ?>
        </div>
    </div>
<?php
}
?>
</div>
<div class="news__pagination">
    <?php
    if ($current_page > 1) {
        echo '<a class="nav-link" href="?page=' . ($current_page - 1) . '">Предыдущая</a>';
    }
    echo '<div class="page-numbers">';
    for ($i = 1; $i <= $total_pages; $i++) {
        if ($i == $current_page) {
            echo '<span class="active">' . $i . '</span>';
        } else {
            echo '<a href="?page=' . $i . '">' . $i . '</a>';
        }
        if ($i < $total_pages) {
            echo '<span class="page-separator">&nbsp;&nbsp;|&nbsp;&nbsp;</span>';
        }
    }
    echo '</div>';
    if ($current_page < $total_pages) {
        echo '<a class="nav-link" href="?page=' . ($current_page + 1) . '">Следующая</a>';
    }
    ?>
</div>
</div>
<?
include $mv -> views_path."main-footer.php";
?>