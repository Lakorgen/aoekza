<?
$content = $mv -> pages -> defineCurrentPage($mv -> router);
$mv -> display404($content);
$mv -> seo -> mergeParams($content, "name");

include $mv -> views_path."main-header.php";
?>

<div class="container py-4">

  <div class="title text-center">
    <h1 class="fw-bold">АО "ЭКЗА"</h1>
    <div class="subtitle">Это современная, не имеющая аналогов ни в России, ни в Европе, комплексная технология коммерческой и технической подготовки транспортных средств для перевозки нефтеналивных грузов железнодорожным и автомобильным транспортом.</div>
    <a href="#more" class="btn-more">Подробнее! <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-down" viewBox="0 0 20 20">
  <path fill-rule="evenodd" d="M8 1a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L7.5 13.293V1.5A.5.5 0 0 1 8 1"/>
</svg>
  </a>
  </div>

  <section id="more" class="section-quality">
    <div  class="section-quality__inner mb-4">
      <img src="/media/images/more.jpg" alt="" class="more_img">
      <div class="quality__text">
        <h2 class="quality__title fw-bold text-center mb-4">Главное достоинство комплекса</h2>
        <p class="quality__subtitle lead text-center">Качественная подготовка цистерн, основанная на автоматизированном управлении процессами внутренней и наружной мойки.</p>
      </div>
    </div>
  </section>

  <section class="accordeon">
    <div class="accordeon__inner">
      <h2 class="text-center fw-bold mb-4">Основные виды деятельности АО "ЭКЗА"</h2>
      <div class="accordeon__item">
        <div class="accordeon__trigger">
          <p class="accordeon__trigger-title">
          Обработка цистерн и авто бойлеров
          </p>
        </div>
        <div class="accordeon__content">
            <p class="accordeon__content-text">
            Обработка цистерн и авто бойлеров - паром, горячей водой, воздухом для последующей перевозки нефти и всего спектра производимых нефтепродуктов (в том числе высокооктановых, реактивного топлива, отвечающих стандартам "Евро-3", "Евро-4", "Евро-5"), а также под огневые работы (плановые виды ремонтов) с выдачей актов годности установленной формы.
            </p>
        </div>
      </div>
      <div class="accordeon__item">
        <div class="accordeon__trigger">
          <p class="accordeon__trigger-title">
          Наружная мойка цистерн
          </p>
        </div>
        <div class="accordeon__content">
            <p class="accordeon__content-text">
            Наружная мойка цистерн - автоматизированный процесс на основе компьютерного управления порталом.
            </p>
        </div>
      </div>
      <div class="accordeon__item">
        <div class="accordeon__trigger">
          <p class="accordeon__trigger-title">
          Отцепочный ремонт цистерн
          </p>
        </div>
        <div class="accordeon__content">
            <p class="accordeon__content-text">
            Отцепочный ремонт цистерн - смена колёсной пары, замена регулировка тормозного оборудования, замена деталей, агрегатов, тележки, запорно-предохранительная арматура, ремонт элементов котла (сварочные работы).
            </p>
        </div>
      </div>
      <div class="accordeon__item">
        <div class="accordeon__trigger">
          <p class="accordeon__trigger-title">
          Аренда цистерн
          </p>
        </div>
        <div class="accordeon__content">
            <p class="accordeon__content-text">
            Предоставление в аренду собственных железнодорожных цистерн для перевозки нефтепродуктов
            </p>
        </div>
      </div>
    </div>
  </section>

  <section class="system-quality">
    <div class="system-quality__inner mb-4">
      <h2 class="fw-bold text-center mb-4">Система менеджмента качества</h2>
      <p>АО "ЭКЗА" сертифицирована и применяет систему менеджмента качества, соответствующую требованиям ГОСТ Р ИСО 9001:2015.</p>
      <p>Область применения:</h2>
      <ul class="list-group list-group-flush">
        <li class="list-group-item">подготовка железнодорожных цистерн под налив нефтепродуктов;</li>
        <li class="list-group-item">текущий ремонт железнодорожных цистерн.</li>
      </ul>
    </div>
  </section>

</div>

<?
include $mv -> views_path."main-footer.php";
?>