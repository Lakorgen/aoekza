<?
include $mv -> views_path."main-header.php";

?>
<div class="container">
    <h1 class="text-center">Партнёрам и клиентам</h1>
    <div class="partners__subtitle">Промывочно-пропарочная станция занимает выгодное географическое положение. Стальные нити Куйбышевской железной дороги связывают центр и запад России с Уралом и Сибирью, Казахстаном и Средней Азией.</div>

    <section class="shareholders">
        <div class="shareholders__inner">
            <h2 class="text-center fw-bold mb-4">Акционеры</h2>
            <div class="shareholders__items">
                <?php
                $partn = new Partners();
                $shareholders = $partn->findShareholders();
                foreach($shareholders as $shareholder){
                ?>
                <a href="#!" class="shareholders-link">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title nav-link"><?php echo $shareholder['name'];?></h5>
                        </div>
                    </div>
                </a>
                <?php
                 }
                ?>
            </div>
        </div>
    </section>

    <section class="partners">
        <div class="partners__inner">
            <h2 class="text-center fw-bold mb-4">Партнеры</h2>
            <div class="partners__items">
                <?php
                $partn = new Partners();
                $partners = $partn->findpart();
                foreach($partners as $partner){
                    ?>  
                <a href="#!" class="parners-link">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title nav-link"><?php echo $partner['name'];?></h5>
                        </div>
                    </div>
                </a>
                <?php
                 }
                 ?>
            </div>
        </div>
    </section>
    
    <section class="contracts">
        <div class="contracts__inner">
            <h2 class="text-center fw-bold mb-4">Группа подготовки договоров</h2>
            <p class="contracts__text">Тел.: (846) 377-46-94 доб. 214</p>
            <p class="contracts__text">(846) 377-46-94 доб. 235</p>
            <p class="contracts__text">E-Mail: JilkinaIA@aoekza.ru</p>
        </div>
    </section>

    <section class="documents">
        <div class="documents__inner">
            <h2 class="text-center fw-bold">Пакет правоустанавливающих документов юридического <br> лица для заключения договоров:</h2>
            <p class="documents__text">1.Устав организации. <br> 2.Выписку из Единого государственного реестра юридических лиц, выданную не позднее, чем за 1 (один) месяц до заключения договора.
            <br>3.Свидетельство о внесении записи в Единый государственный реестр юридических лиц (индивидуальных предпринимателей).
 <br> 4.Свидетельство о государственной регистрации.
 <br> 5.Свидетельство о постановке на учет в налоговом органе.
 <br> 6.Лицензию, патент, свидетельства, разрешения, если это необходимо в соответствии с действующим законодательством.
 <br> 7.Документы, подтверждающие полномочия лица, подписывающего договор (протокол собрания акционеров об избрании (продлении полномочий) руководителя организации или решение учредителей(ля), если лицо действует по доверенности, то необходима доверенность за подписью руководителя организации).
 <br> 8.Документы, подтверждающие право юридического лица на применение упрощённой системы налогообложения (если УСН применяется данным предприятием при расчетах).
 <br> 9.Карту партнера с реквизитами и образцами подписей руководителя и главного бухгалтера и оттиска печати организации.
 <br> <br> Вышеуказанные документы должны быть представлены в виде заверенных печатью организации копий на бумажных носителях и могут быть переданы по каналам электронной или факсимильной связи с последующим предоставлением их почтовой корреспонденцией или через ответственного представителя АО "ЭКЗА" до оплаты товаров (услуг).</p>
        </div>
    </section>

    <section class="documents">
        <div class="documents__inner">
            <h2 class="text-center fw-bold mb-4">Железнодорожные реквизиты</h2>
            <p class="documents__text">Станция Новокуйбышевская Кбш.ж.д., код станции 639400, получатель: АО "ЭКЗА" ОКПО 11003326, код предприятия (6709), почтовый адрес: 446200, Самарская область, Новокуйбышевск, а/я 4</p>
        </div>
    </section>

</div>
<?
include $mv -> views_path."main-footer.php";
?>