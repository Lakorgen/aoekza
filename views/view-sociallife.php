<?
include $mv -> views_path."main-header.php";
?>
<div class="container">
	<h1 class="text-center fw-bold mb-4">Социально-техническая характеристика АО "ЭКЗА"</h1>
	<p>В настоящее время краткая социально-техническая характеристика АО "ЭКЗА" выглядит следующим образом:
	</p>
	<ul>
		<li>Площадь территории - 33,4 гектара</li>
		<li>Всего работающих - 268 человек</li>
		<li>В том числе с высшим образованием - 71 человек (26,5%)</li>
		<li>Со средне-профессиональным образованием - 86 человек (32%)</li>
	</ul>
	<p>С завершением строительства проведено и продолжает проводится благоустройство и озеленение участка в границах производственных объектов.
	</p>
	<h2 class="text-center fw-bold mt-4 mb-4">Мазутное озеро</h2>
	<p>Особую тревогу вызывало наличие на территории так называемого «мазутного озера». Начиная с 1958 года в течение более 40 лет с Новокуйбышевской промыво-пропарочной станции сюда сливались остатки нефтепродуктов после пропарки битумных цистерн, в результате чего образовалось «озеро» площадью 100 на 100 метров и глубиной 3 метра. Склоны оврага на участке длиной 600 - 700 метров и шириной 10 - 15 метров были загрязнены отходами нефтепродуктов толщиной 10 - 15 см. Для ликвидации последствий этих загрязнений было откачено и вывезено 5000 кубометров нефтеотходов и 13000 кубометров воды. Для рекультивации территории было завезено 2000 кубометров грунта, посажены деревья и благоустроена территория.
	</p>
    <div class="swiper">
        <!-- Additional required wrapper -->
        <div class="swiper-wrapper">
            <!-- Slides -->
            <div class="swiper-slide">
                <img class="social__img" src="<? echo $mv -> media_path; ?>images/sociallife1.jpg" alt="">
            </div>
            <div class="swiper-slide">
                <img class="social__img" src="<? echo $mv -> media_path; ?>images/sociallife2.jpg" alt="">
            </div>
            <div class="swiper-slide">
                <img class="social__img" src="<? echo $mv -> media_path; ?>images/sociallife3.jpg" alt="">
            </div>
        </div>
        
        <!-- If we need navigation buttons -->
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>

        <!-- If we need scrollbar -->
        <div class="swiper-scrollbar"></div>
        </div>
	<h2 class="text-center fw-bold mt-4 mb-4">Текущая ситуация</h2>
	<div class="image-placeholder">Территория ЗАО
	</div>
	<p>Все свободные земельные участки вокруг зданий, сооружений и площади благоустроены, разбиты газоны, оборудованы зоны отдыха. Высажено около трех тысяч деревьев и кустарников. Приятно смотреть во все времена года.
	</p>
	<p>В 2007 году АО "ЭКЗА" за создание нормальной экологической обстановки на предприятии награждено специальным дипломом.
	</p>
	<h2 class="text-center fw-bold mt-4 mb-4">Социальные вопросы</h2>
	<p>Для решения социальных вопросов на предприятии имеются:
	</p>
	<ul>
		<li>Бытовой корпус с раздевалками и душевыми, сауной, сушилкой, пункт химчистки спецодежды, столовая, медпункт</li>
		<li>Работники предприятия, проживающие в г.Новокуйбышевске и г. Самаре, доставляются на работу автотранспортом АО "ЭКЗА"</li>
		<li>Предприятие оплачивает прохождение сотрудниками периодической медицинской комиссии</li>
	</ul>
	<div class="image-placeholder">Бытовой корпус
	</div>
	<p>Круглосуточно, включая выходные и праздничные дни, работает столовая.
	</p>
	<h3 class="text-center fw-bold mt-4 mb-4">Социальные выплаты</h3>
	<ul>
		<li>Социальные выплаты за санаторно-курортное лечение; отдых на турбазах, профилакториях, за услуги детских оздоровительных лагерей, детских дошкольных учреждений</li>
		<li>Оплата проезда железнодорожным транспортом (1 раз в год всем членам семьи, находящимся на иждивении)</li>
		<li>Материальная помощь к отпуску</li>
		<li>Дополнительный день к отпуску, если в течение года работник не был на больничном</li>
	</ul>
	<p>На предприятии имеется медицинский пункт.
	</p>
	<p>Правилам и навыкам безопасных приемов труда работники обучаются в кабинете охраны труда.
	</p>
	<div class="image-placeholder">Кабинет охраны труда
	</div>
	<h3 class="text-center fw-bold mt-4 mb-4">Результаты специальной оценки условий труда АО "ЭКЗА"</h3>
	<ul>
		<li>Отчет о проведении специальной оценки условий труда 2021г.</li>
		<li>Перечень рекомендуемых мероприятий по улучшению условий труда 2021г.</li>
		<li>Сводная ведомость результатов проведения специальной оценки условий труда 2021г.</li>
	</ul>
</div>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
	const about_sociallive_swiper = new Swiper('.swiper', {
    // Optional parameters
    effect: "fade",
    loop: true,
    autoplay: {
      delay: 3000,
      disableOnInteraction: false,
    },
  
    // If we need pagination
    pagination: {
      el: '.swiper-pagination',
    },
  
    // Navigation arrows
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  
    // And if we need scrollbar
    scrollbar: {
      el: '.swiper-scrollbar',
    },
  });
</script>
<?
include $mv -> views_path."main-footer.php";
?>