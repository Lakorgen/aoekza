<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Проверка наличия ключей в массиве $_POST
    if (isset($_POST['request_id']) && isset($_POST['status'])) {
        // Получаем данные из запроса
        $request_id = $_POST['request_id'];
        $status = $_POST['status'];

        // Проверяем, что данные не пустые
        if (!empty($request_id) && !empty($status)) {
            // Создаем экземпляр заявки и находим запись по ID

            $request = $mv -> requests -> findRecordById($request_id);

            if ($request) {
                // Обновляем статус заявки
                $request->status = $status;
                $request->date_change = I18n::getCurrentDateTime();
                // Сохраняем изменения
                $request->update();

            }
        }
    }
}
?>
<?php
include $mv->views_path."main-header.php";

$appModel = new Requests();
$all = $appModel->findAll();
$new = $appModel->findNew();
$processing = $appModel->findProcessing();
$end = $appModel->findEnd();
$cancel = $appModel->findCancel();
$applications = $all;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["status"])) {
    switch ($_POST["status"]) {
        case "all":
            $applications = $all;
            break;
        case "new":
            $applications = $new;
            break;
        case "in_progress":
            $applications = $processing;
            break;
        case "completed":
            $applications = $end;
            break;
        case "canceled":
            $applications = $cancel;
            break;
        default:
            $applications = $all;
            break;
    }
}
?>



<div id="requestModal" class="modal">
    <div class="modal-content">
    <form action="/requests/" method="POST">
        <input type="hidden" name="request_id" id="request_id" value="">
                <div class="modal-header">
                    <h5 class="modal-title" id="requestModalLabel">Подробная информация о заявке</h5>
                    <span class="close">&times;</span>
                </div>
                <div class="modal-body" id="requestModalBody">
                    <!-- Содержимое модального окна -->
                </div>
                <div class="modal-footer">
                    <div class="row w-100 align-items-center">
                        <div class="col-md-9">
                            <select name="status" id="status" class="form-control">
                                <option value="Новая">Новая</option>
                                <option value="В обработке">В обработке</option>
                                <option value="Завершена">Завершена</option>
                                <option value="Отклонена">Отклонена</option>
                            </select>
                        </div>
                        <div class="col-md-3 text-right">
                            <button type="submit" class="contacts__btn" id="saveButton">Сохранить</button>
                        </div>
                    </div>
                </div>
            </form>
    </div>
</div>



<div class="container mt-5">
    <h3 class="mb-3 text-center fs-1">Заявки</h3>
    <div class="table-responsive">
        <div class="button__container">
            <form method="post">
                    <button type="submit" name="status" class="btn btn-outline-success filter-btn <?php echo $applications == $all ? 'active' : ''; ?>" value="all">Все</button>
                    <button type="submit" name="status" class="btn btn-outline-success filter-btn <?php echo $applications == $new ? 'active' : ''; ?>" value="new">Новые</button>
                    <button type="submit" name="status" class="btn btn-outline-success filter-btn <?php echo $applications == $processing ? 'active' : ''; ?>" value="in_progress">В обработке</button>
                    <button type="submit" name="status" class="btn btn-outline-success filter-btn <?php echo $applications == $end ? 'active' : ''; ?>" value="completed">Завершенные</button>
                    <button type="submit" name="status" class="btn btn-outline-success filter-btn <?php echo $applications == $cancel ? 'active' : ''; ?>" value="canceled">Отклонены</button>
                </form>
                <p>Количество: в разработке</p>
        </div>
        <table id="requestsTable" class="table table-Light table-hover sortable">
            <thead>
                <tr>
                    <th data-sort="id" class="sorted sorted-asc">Номер заявки</th>
                    <th data-sort="requests_title">Название заявки</th>
                    <th data-sort="full_name">ФИО</th>
                    <th data-sort="email">Email</th>
                    <th data-sort="phone">Телефон</th>
                    <th data-sort="requests_text">Текст заявки</th>
                    <th data-sort="date_created">Дата создания</th>
                    <th data-sort="status">Статус</th>
                    <th data-sort="date_change">Дата изменения статуса</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $application): ?>
                <tr>
                    <td><?php echo htmlspecialchars($application['id']); ?></td>
                    <td><?php echo htmlspecialchars($application['requests_title']); ?></td>
                    <td><?php echo htmlspecialchars($application['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($application['email']); ?></td>
                    <td><?php echo htmlspecialchars($application['phone']); ?></td>
                    <td><?php echo htmlspecialchars($application['requests_text']); ?></td>
                    <td><?php echo htmlspecialchars($application['date_created']); ?></td>
                    <td><?php echo htmlspecialchars($application['status']); ?></td>
                    <td><?php echo htmlspecialchars($application['date_change']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<script src="https://maxcdn.bootstrapcdn.com/bootstrap/5.0.2/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('requestsTable');
    const headers = table.querySelectorAll('th');
    
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const column = header.dataset.sort;
            const order = header.classList.contains('sorted-asc') ? 'desc' : 'asc';
            
            headers.forEach(th => th.classList.remove('sorted', 'sorted-asc', 'sorted-desc'));
            header.classList.add('sorted');
            header.classList.add(order === 'asc' ? 'sorted-asc' : 'sorted-desc');
            
            sortTableByColumn(table, column, order);
        });
    });

    function sortTableByColumn(table, column, order) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        const compare = (rowA, rowB) => {
            const cellA = rowA.querySelector(`td:nth-child(${getColumnIndex(column)})`).innerText.toLowerCase();
            const cellB = rowB.querySelector(`td:nth-child(${getColumnIndex(column)})`).innerText.toLowerCase();

            if (!isNaN(cellA) && !isNaN(cellB)) {
                return order === 'asc' ? cellA - cellB : cellB - cellA;
            } else {
                return order === 'asc' ? cellA.localeCompare(cellB) : cellB.localeCompare(cellA);
            }
        };

        rows.sort(compare);
        rows.forEach(row => tbody.appendChild(row));
    }

    function getColumnIndex(column) {
        const columns = ['id', 'requests_title', 'full_name', 'email', 'phone', 'requests_text', 'date_created', 'status', 'date_change'];
        return columns.indexOf(column) + 1;
    }
});
const filterButtons = document.querySelectorAll('.filter-btn');
    const rows = document.querySelectorAll('#requestsTable tbody tr');

    filterButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const status = btn.dataset.status;

            rows.forEach(row => {
                if (status === 'all') {
                    row.style.display = '';
                } else {
                    const rowStatus = row.querySelector('td:nth-child(7)').innerText.trim().toLowerCase();
                    if (status === rowStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });

            // Подсветка активной кнопки фильтра
            filterButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('requestModal');
    const span = document.getElementsByClassName('close')[0];

    document.querySelectorAll('#requestsTable tbody tr').forEach(row => {
        row.addEventListener('click', function() {
            const requestId = row.querySelector('td:nth-child(1)').innerText;
            const requestTitle = row.querySelector('td:nth-child(2)').innerText;
            const requestFullName = row.querySelector('td:nth-child(3)').innerText;
            const requestEmail = row.querySelector('td:nth-child(4)').innerText;
            const requestPhone = row.querySelector('td:nth-child(5)').innerText;
            const requestText = row.querySelector('td:nth-child(6)').innerText;
            const requestDateCreated = row.querySelector('td:nth-child(7)').innerText;
            const requestStatus = row.querySelector('td:nth-child(8)').innerText;
            const requestDateChange = row.querySelector('td:nth-child(9)').innerText;

            const requestIdInput = document.getElementById('request_id');
            requestIdInput.value = requestId;
            console.log("ID заявки установлен:", requestId);

            const modalBody = document.getElementById('requestModalBody');
            modalBody.innerHTML = `
                <p><strong>Номер заявки:</strong> ${requestId}</p>
                <p><strong>Название заявки:</strong> ${requestTitle}</p>
                <p><strong>ФИО:</strong> ${requestFullName}</p>
                <p><strong>Email:</strong> ${requestEmail}</p>
                <p><strong>Телефон:</strong> ${requestPhone}</p>
                <p><strong>Текст заявки:</strong> ${requestText}</p>
                <p><strong>Дата создания:</strong> ${requestDateCreated}</p>
                <p><strong>Статус:</strong> ${requestStatus}</p>
                <p><strong>Дата изменения статуса:</strong> ${requestDateChange}</p>
            `;

            modal.style.display = 'block';
        });
    });

    span.onclick = function() {
        modal.style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    }
});
</script>
<?php
include $mv->views_path."main-footer.php";
?>
