<? echo $mv -> seo -> displayMetaData("footer"); ?>

    <footer class="footer">
        <div class="container footer__inner">
            <div class="footer__logo">
                <div class="footer__title">
                    AO "ЭКЗА"
                </div>
                    <p class="footer__subtitle">Это современная, не имеющая аналогов ни в России, ни в Европе, комплексная технология коммерческой и технической подготовки транспортных средств для перевозки нефтеналивных грузов железнодорожным и автомобильным транспортом.</p>
                </div>    
        <nav class="footer_nav">
                <h3>Навигация</h3>
                <ul class="footer__ul">
                    <? echo $mv -> pages -> displayMenu(-1); ?>
                </ul>
            </nav>
            <div class="footer_contacts">
                <h3>Контакты</h3>
                <ul>
                    <li>
                        <a class="nav-link" href="mailto:<?php  echo $mv->footer->getValue('email')?>" class="contact__email">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-envelope" viewBox="0 0 20 20">
      <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1zm13 2.383-4.708 2.825L15 11.105zm-.034 6.876-5.64-3.471L8 9.583l-1.326-.795-5.64 3.47A1 1 0 0 0 2 13h12a1 1 0 0 0 .966-.741M1 11.105l4.708-2.897L1 5.383z"/>
    </svg><?php  echo $mv->footer->getValue('email')?></a>
                    </li>
                    <li>
                        <a class="nav-link" href="tel:<?php  echo $mv->footer->getValue('phone')?>" class="contact__phone">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-telephone-fill" viewBox="0 0 20 20">
  <path fill-rule="evenodd" d="M1.885.511a1.745 1.745 0 0 1 2.61.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.68.68 0 0 0 .178.643l2.457 2.457a.68.68 0 0 0 .644.178l2.189-.547a1.75 1.75 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.6 18.6 0 0 1-7.01-4.42 18.6 18.6 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877z"/></svg><?php  echo $mv->footer->getValue('phone')?></a>
                    </li>
                </ul>
            </div>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const burgerMenu = document.getElementById('burger-menu');
        const navMenu = document.getElementById('nav-menu');
        burgerMenu.addEventListener('click', function() {
            navMenu.classList.toggle('active');
        });
    });
</script>   
</body>
</html>