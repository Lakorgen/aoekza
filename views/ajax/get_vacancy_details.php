<?php
include "../../config/autoload.php";
$mv = new Builder();
if (isset($_GET['id'])) {
    $vacancy_id = $_GET['id'];
    $vacancy = $mv->vacancy->findId($vacancy_id);
    foreach ($vacancy as $vac) {
        if ($vac) {
            echo "<div class='modal-vacancy'>";
            echo "<h5 class='card-title mb-4'>" . htmlspecialchars($vac['title']) . "</h5>";
            echo "<p class='card-text'><strong>Описание:</strong> " . htmlspecialchars($vac['description']) . "</p>";
            echo "<p class='card-text'><strong>Требования:</strong> " . htmlspecialchars($vac['requirements']) . "</p>";
            echo "<p class='card-text'><strong>Дата публикации:</strong> " . htmlspecialchars($vac['date_published']) . "</p>";
            echo "<p class='card-text'><strong>Статус:</strong> " . htmlspecialchars($vac['status']) . "</p>";
            echo "<p class='card-text'><strong>Дата изменения статуса:</strong> " . htmlspecialchars($vac['date_status_changed']) . "</p>";
            echo "</div>";
        }
    }
}
?>
