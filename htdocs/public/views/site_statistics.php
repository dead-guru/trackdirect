<?php require dirname(__DIR__) . "../../includes/bootstrap.php"; ?>

<title>Інформація / Статистика </title>
<div class="modal-inner-content modal-inner-content-about" style="padding-bottom: 30px;">
    <div class="modal-inner-content-menu">
        <a href="/views/about.php" class="tdlink" title="More about this website!">Про сайт</a>
        <a href="/views/faq.php" class="tdlink" title="Frequently asked questions">Часті запитання</a>
        <span>Статистика</span>
        <?php if (getWebsiteConfig('aprs_is_status_url')): ?><a href="/views/server_health.php?server=aprs" class="tdlink" title="APRS Server Health">Статус APRS Сервера</a><?php endif; ?>
    </div>
    <div class="horizontal-line">&nbsp;</div>

    <p>
        This APRS tracker is brought to you by <?php echo getWebsiteConfig('owner_name'); ?>.
        Due to the high volume of incoming data, the statistics shown here are likely out of date by the time this page loads.
    </p>
    <br />
    <table style="width:50%" align="center">
        <thead>
        <tr>
            <th colspan="2" style="background: #dddddd">System Totals</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Stations:</td>
            <td id="system_stations"></td>
        </tr>
        <tr>
            <td>Senders:</td>
            <td id="system_senders"></td>
        </tr>
        <tr>
            <td colspan="2"><hr /></td>
        </tr>
        <tr>
            <td>Archived Packets:</td>
            <td id="system_packets"></td>
        </tr>
        <tr>
            <td>OGN Records:</td>
            <td id="system_ognpackets"></td>
        </tr>
        <tr>
            <td>Path Records:</td>
            <td id="system_pathpackets"></td>
        </tr>
        <tr>
            <td>Telemetry Records:</td>
            <td id="system_telemetrypackets"></td>
        </tr>
        <tr>
            <td>Weather Records:</td>
            <td id="system_weatherpackets"></td>
        </tr>
        <tr>
            <td colspan="2"><hr /></td>
        </tr>
        <tr>
            <td>Telemetry Bit Records:</td>
            <td id="system_telemetry_bits"></td>
        </tr>
        <tr>
            <td>Telemetry EQNS Records:</td>
            <td id="system_telemetry_eqns"></td>
        </tr>
        <tr>
            <td>Telemetry PARAM Records:</td>
            <td id="system_telemetry_param"></td>
        </tr>
        <tr>
            <td>Telemetry Unit Records:</td>
            <td id="system_telemetry_unit"></td>
        </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
    <br />
    <table style="width:50%" align="center">
        <thead>
        <tr>
            <th colspan="2" style="background: #dddddd">Today's Totals</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>RAW Packets:</td>
            <td id="today_packets"></td>
        </tr>
        <tr>
            <td>OGN Records:</td>
            <td id="today_ognpackets"></td>
        </tr>
        <tr>
            <td>Path Records:</td>
            <td id="today_pathpackets"></td>
        </tr>
        <tr>
            <td>Telemetry Records:</td>
            <td id="today_telemetrypackets"></td>
        </tr>
        <tr>
            <td>Weather Records:</td>
            <td id="today_weatherpackets"></td>
        </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>

</div>

<script>
    $(document).ready(function() {
        $.getJSON('/data/stats.php').done(function(response) {
            $.each(response, function(k, v){
                $('#'+k).text(v);
                $('#'+k).css('text-align', 'right');
            });
        });
    });
</script>