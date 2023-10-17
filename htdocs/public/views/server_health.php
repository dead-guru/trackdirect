<?php
require dirname(__DIR__) . "../../includes/bootstrap.php";

$server = $_GET['server'] ?? '';
$status_url = '';
if ($server != '') {
    $status_url = getWebsiteConfig($server."_is_status_url");
}
?>
<script>
    $(document).ready(function() {
        function resize_iframe() {
            var height = $('.modal-content-body').height();
            $('#statuspanel').css('height', height - 95);
        }
        resize_iframe();

        $(window).resize(function() {
            resize_iframe();
        });
    });
</script>
<title>Інформація / Статус <?php echo strtoupper($server); ?> серверу</title>
<div class="modal-inner-content modal-inner-content-about" style="padding-bottom: 30px;">
    <div class="modal-inner-content-menu">
        <a href="/views/about.php" class="tdlink" title="More about this website!">Про сайт</a>
        <a href="/views/faq.php" class="tdlink" title="Frequently asked questions">Часті запитання</a>
        <a href="/views/site_statistics.php" class="tdlink" title="Website and server statistics!">Статистика</a>
        <?php if (getWebsiteConfig('aprs_is_status_url') && $server != 'aprs'): ?><a href="/views/server_health.php?server=aprs" class="tdlink" title="Статус APRS серверу">Статус APRS серверу</a><?php else: ?><span>Статус APRS серверу</span><?php endif; ?>
        </div>
    <div class="horizontal-line">&nbsp;</div>
    Ми використовуємо окремий <?php echo strtoupper($server); ?>-IS сервер. Цю сторінку стану сервера можна переглянути безпосередньо за цим <a href="<?php echo $status_url; ?>" target=-"_blank">посиланням</a>.
    <iframe src="<?php echo $status_url; ?>" style="width:100%;height:100%" id="statuspanel"?>
</div>