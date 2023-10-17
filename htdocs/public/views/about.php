<?php require dirname(__DIR__) . "../../includes/bootstrap.php";
$server = $_GET['server'] ?? '';
$status_url = '';
if ($server != '') {
    $status_url = getWebsiteConfig($server."_is_status_url");
}
?>

<title>Інформація / Часті запитання</title>
<div class="modal-inner-content modal-inner-content-about" style="padding-bottom: 30px;">
    <div class="modal-inner-content-menu">
        <span>Про сайт</span>
        <a href="/views/faq.php" class="tdlink" title="Часті запитання">Часті запитання</a>
        <a href="/views/site_statistics.php" class="tdlink" title="Website and server statistics!">Статистика</a>
        <?php if (getWebsiteConfig('aprs_is_status_url') && $server != 'aprs'): ?><a href="/views/server_health.php?server=aprs" class="tdlink" title="Статус APRS серверу">Статус APRS серверу</a><?php else: ?><span>Статус APRS серверу</span><?php endif; ?>
    </div>
    <div class="horizontal-line">&nbsp;</div>

    <p>
        Ласкаво просимо на цей МЕРТВИЙ веб-сайт відстеження APRS! Нашою метою є надання вам швидкої та простою в користуванні карти з даними APRS від <a href="http://www.aprs-is.net" target="_blank">APRS-IS</a>.
        <br>
        Цей сайт є частиною <a href="https://dead.guru">dead.guru</a> мережі.
    </p>

    <img src="/images/aprs-symbols.png" title="APRS symbols" style="width:100%"/>


    <p>
        Цей веб-сайт базується на інструментах APRS Track Direct. А саме не форку - <a href="https://github.com/dead-guru/trackdirect">https://github.com/dead-guru/trackdirect</a>. Докладніше про APRS Track Direct можна прочитати <a href="https://www.aprsdirect.com" target="_blank">тут</a> або перейти безпосередньо до <a href="https://github.com/dead-guru/trackdirect" target="_blank">GitHub</a>. Окрім карти з швидкими оновленнями даних APRS, APRS Track Direct також надає пов'язані функції, такі як <a href="/views/latest.php" class="tdlink" title="Останні почуті станції">Останні почуті</a> і <a href="/views/search.php" class="tdlink" title="Пошук станцій">Пошук станцій</a> і т. д.
    </p>

    <h3>Що таке APRS?</h3>
    <p>
        APRS (Автоматична Пакетна Система Звітування) - це цифрова система зв'язку, яка використовує пакетне радіо для передачі тактичної інформації в режимі реального часу (на частотах любительського радіозв'язку).
        Мережа APRS використовується радіоаматорами по всьому світу.
        Інформація, яку ділять через мережу APRS, включає координати, висоту, швидкість, напрямок, текстові повідомлення, сповіщення, оголошення, бюлетені та погодні дані.
        APRS була розроблена Бобом Брунінга, позивний WB4APR.
        Більше інформації про APRS можна знайти на <a target="_blank" rel="nofollow" href="http://www.aprs.org/">www.aprs.org</a> або на <a target="_blank" rel="nofollow" href="https://en.wikipedia.org/wiki/Automatic_Packet_Reporting_System">Вікіпедії</a>.
    </p>
    <p>
        Але, як ви, ймовірно, вже зрозуміли, специфікація APRS використовується не тільки радіоаматорами, а також для кількох інших галузей, таких як, наприклад, для даних CWOP і OGN.
    </p>

</div>
