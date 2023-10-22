<?php require dirname(__DIR__) . "../../includes/bootstrap.php"; ?>

<?php
if (isset($_GET['c'])) {
    $station = StationRepository::getInstance()->getObjectByName(strtoupper($_GET['c']) ?? null);
} else {
    $station = StationRepository::getInstance()->getObjectById($_GET['id'] ?? null);
}
?>
<?php if ($station->isExistingObject()) : ?>
    <?php
    $maxDays = 10;
    $format = $_GET['format'] ?? 'table';
    $start = $_GET['start'] ?? time()-864000;
    $end = $_GET['end'] ?? time();
    $page = $_GET['page'] ?? 1;
    $rows = $_GET['rows'] ?? 25;
    $offset = ($page - 1) * $rows;
    $pages = 0;

    $graphLabels = array('Time', 'Temperature', 'Humidity', 'Pressure', 'Rain (Last Hour)', 'Rain (Last 24 Hours)', 'Rain (Since Midnight)', 'Wind Speed', 'Wind Direction', 'Luminosity', 'Snow');
    $missingGraphs = [];
    if ($format === 'table') {
        $weatherPackets = PacketWeatherRepository::getInstance()->getLatestObjectListByStationIdAndLimit($station->id, $rows, $offset, $maxDays);
        $count = PacketWeatherRepository::getInstance()->getLatestNumberOfPacketsByStationIdAndLimit($station->id, $maxDays);
        $pages = ceil($count / $rows);
    }


    if ($format === 'graph') {
        $weatherPackets = PacketWeatherRepository::getInstance()->getLatestObjectListByStationIdAndLimit($station->id, 1, 0, $maxDays);
    }

    $titles = array('graph' => 'Графік', 'table' => 'Таблиця');
    ?>

    <title><?php echo $station->name; ?> <?php echo $titles[$format]; ?></title>
    <div class="modal-inner-content">
        <div class="modal-inner-content-menu">
            <a class="tdlink" title="Огляд" href="/views/overview.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Огляд</a>
            <a class="tdlink" title="Статистика" href="/views/statistics.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Статистика</a>
            <a class="tdlink" title="Графік данних" href="/views/trail.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Графік данних</a>
            <span>Погода</span>
            <a class="tdlink" title="Телеметрія" href="/views/telemetry.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Телеметрія</a>
            <a class="tdlink" title="Сирі пакети" href="/views/raw.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Сирі пакети</a>
        </div>

        <div class="horizontal-line" style="margin:0">&nbsp;</div>

        <div class="modal-inner-content-menu" style="margin-left:25px;">
            <?php if ($format !== 'table'): ?><a class="tdlink" href="/views/weather.php?id=<?php echo $station->id; ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ;?>&format=table"><?php echo $titles['table']; ?></a><?php else: ?><span><?php echo $titles['table']; ?></span><?php endif; ?>
            <?php if ($format !== 'graph'): ?><a class="tdlink" href="/views/weather.php?id=<?php echo $station->id; ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ;?>&format=graph"><?php echo $titles['graph']; ?></a><?php else: ?><span><?php echo $titles['graph']; ?></span><?php endif; ?>
        </div>

        <div class="horizontal-line">&nbsp;</div>

        <?php if (count($weatherPackets) > 0) : ?>
            <p>Це останні отримані пакети погодних даних, збережені у нашій базі даних для станції/об'єкта <?php echo $station->name; ?>. Якщо не відображаються жодні пакети, це означає, що станція не надсилає жодних погодних пакетів протягом останніх <?php echo $maxDays; ?> днів.</p>

            <div style="float:left;line-height: 28px;">
                <?php if ($format == 'graph'): ?>
                    <span style="float:left;">Данні за <span id="oldest-timestamp" style="font-weight:bold;"></span> по <span id="latest-timestamp" style="font-weight:bold;"></span>.  <span id="records"></span> (макс 1000)</span>
                <?php endif; ?>
                  <script type="text/javascript">
                          $('#oldest-timestamp, #latest-timestamp').each(function() {
                              if ($(this).html().trim() != '' && !isNaN($(this).html().trim())) {
                                  $(this).html(moment(new Date(1000 * $(this).html())).format('L LTS'));
                              }
                          });
                  </script>

            </div>

            <div style="clear:both;"></div>

        <?php if ($format === 'graph'): ?>
        <?php for ($graphIdx = 1; $graphIdx < 11; $graphIdx++) : ?>
        <?php
        if (
            ($graphIdx == 1 && $weatherPackets[0]->temperature === null) ||
            ($graphIdx == 2 && $weatherPackets[0]->humidity === null) ||
            ($graphIdx == 3 && $weatherPackets[0]->pressure === null) ||
            ($graphIdx == 4 && $weatherPackets[0]->rain_1h === null) ||
            ($graphIdx == 5 && $weatherPackets[0]->rain_24h === null) ||
            ($graphIdx == 6 && $weatherPackets[0]->rain_since_midnight === null) ||
            ($graphIdx == 7 && $weatherPackets[0]->wind_speed === null) ||
            ($graphIdx == 8 && $weatherPackets[0]->wind_direction === null) ||
            ($graphIdx == 9 && $weatherPackets[0]->luminosity === null) ||
            ($graphIdx == 10 && $weatherPackets[0]->snow === null)
        ) {
            $missingGraphs[] = $graphIdx;
            continue;
        }
        ?>
            <div style="width:100%;background:#dddddd;padding:2px;font-weight:bold;"><?php echo $station->name; ?> [<?php echo $graphLabels[$graphIdx]; ?>]</div>
            <canvas id="graph_<?php echo $graphIdx; ?>" height="80"></canvas>
            <div style="height:20px;"></div>
        <?php endfor; ?>

        <?php if (count($missingGraphs)) : ?>
            <p>Станція <b><?php echo $station->name; ?></b> не передавала або ще не передала данних за період:</p>
            <ul>
                <?php
                foreach ($missingGraphs as $graphId) {
                    echo '<li>'.$graphLabels[$graphId].'</li>';
                }
                ?>
            </ul>
        <?php endif; ?>

            <script type="text/javascript">
                initGraph(10);
                $(document).ready(function() {
                    for (let i = 1; i < 11; i++) {
                        if (window['chart_'+i] != null) {
                            $.getJSON('/data/graph.php?id=<?php echo $station->id ?>&type=weather&start=<?php echo $start; ?>&end=<?php echo $end; ?>&index=' + i).done(function(response) {
                                $('#oldest-timestamp').text(response.oldest_timestamp);
                                $('#latest-timestamp').text(response.latest_timestamp);
                                $('#oldest-timestamp, #latest-timestamp').each(function() {
                                    if ($(this).html().trim() != '' && !isNaN($(this).html().trim())) {
                                        $(this).html(moment(new Date(1000 * $(this).html())).format('L LTS'));
                                    }
                                });
                                $('#records').text(response.records + ' знайдено');

                                window['chart_'+i].data.datasets[0].data = response.data;
                                window['chart_'+i].data.datasets[0].label = response.label;
                                if (response.borderColor != null) window['chart_'+i].data.datasets[0].borderColor = response.borderColor;
                                if (response.borderColor != null) window['chart_'+i].data.datasets[0].backgroundColor = response.backgroundColor;
                                window['chart_'+i].update();
                            });
                        }
                    }
                });
            </script>
        <?php endif; ?>
        <?php if ($format == 'table'): ?>
            <p>Це останній отриманий пакет погодних даних, збережений у нашій базі даних для станції/об'єкта <?php echo $station->name; ?>. Якщо не відображаються жодні пакети, це означає, що відправник не надсилає жодних пакетів погодних даних протягом останніх <?php echo $maxDays; ?> днів.</p>

            <div class="form-container">
                <select id="weather-rows" style="float:left; margin-right: 5px;" class="pagination-rows">
                    <option <?php echo ($rows == 25 ? 'selected' : ''); ?> value="25">25 рядків</option>
                    <option <?php echo ($rows == 50 ? 'selected' : ''); ?> value="50">50 рядків</option>
                    <option <?php echo ($rows == 100 ? 'selected' : ''); ?> value="100">100 рядків</option>
                    <option <?php echo ($rows == 200 ? 'selected' : ''); ?> value="200">200 рядків</option>
                    <option <?php echo ($rows == 300 ? 'selected' : ''); ?> value="300">300 рядків</option>
                </select>
            </div>

            <?php if ($pages > 1): ?>
                <div class="pagination">
                  <a class="tdlink" href="/views/weather.php?id=<?php echo $station->id; ?>&rows=<?php echo $rows; ?>&page=1"><<</a>
                  <?php for($i = max(1, $page - 3); $i <= min($pages, $page + 3); $i++) : ?>
                  <a href="/views/weather.php?id=<?php echo $station->id; ?>&rows=<?php echo $rows; ?>&page=<?php echo $i; ?>" <?php echo ($i == $page ? 'class="tdlink active"': 'class="tdlink"')?>><?php echo $i ?></a>
                  <?php endfor; ?>
                  <a class="tdlink" href="/views/weather.php?id=<?php echo $station->id; ?>&rows=<?php echo $rows; ?>&page=<?php echo $pages; ?>">>></a>
                </div>
            <?php endif; ?>


            <div class="datagrid datagrid-weather" style="max-width:1000px;">
                <table>
                    <thead>
                    <tr>
                        <th>Час</th>
                        <th>Темп.</th>
                        <th>Вологість</th>
                        <th>Тиск</th>
                        <th>Опади*</th>
                        <th>Вітер**</th>
                        <th>Освітленність</th>
                        <th>Сніг</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($weatherPackets as $packetWeather) : ?>

                        <tr>
                            <td class="weathertime">
                                <?php echo ($packetWeather->wxRawTimestamp != null?$packetWeather->wxRawTimestamp:$packetWeather->timestamp); ?>
                            </td>
                            <td>
                                <?php if ($packetWeather->temperature !== null) : ?>
                                    <?php if (isImperialUnitUser()) : ?>
                                        <?php echo round(convertCelciusToFahrenheit($packetWeather->temperature), 2); ?>&deg; F
                                    <?php else : ?>
                                        <?php echo round($packetWeather->temperature, 2); ?>&deg; C
                                    <?php endif; ?>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($packetWeather->humidity !== null) : ?>
                                    <?php echo $packetWeather->humidity; ?>%
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($packetWeather->pressure !== null) : ?>
                                    <?php if (isImperialUnitUser()) : ?>
                                        <?php echo round(convertMbarToMmhg($packetWeather->pressure),1); ?> mmHg
                                    <?php else : ?>
                                        <?php echo round($packetWeather->pressure,1); ?> hPa
                                    <?php endif; ?>

                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <?php if ($weatherPackets[0]->rain_1h !== null) : ?>
                                <td title="<?php echo $packetWeather->getRainSummary(false, true, true); ?>">
                                    <?php if ($packetWeather->rain_1h !== null) : ?>
                                        <?php if (isImperialUnitUser()) : ?>
                                            <?php echo round(convertMmToInch($packetWeather->rain_1h), 2); ?> in
                                        <?php else : ?>
                                            <?php echo round($packetWeather->rain_1h, 2); ?> мм
                                        <?php endif; ?>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            <?php elseif ($weatherPackets[0]->rain_24h !== null) : ?>
                                <td title="<?php echo $packetWeather->getRainSummary(true, false, true); ?>">
                                    <?php if ($packetWeather->rain_24h !== null) : ?>
                                        <?php if (isImperialUnitUser()) : ?>
                                            <?php echo round(convertMmToInch($packetWeather->rain_24h), 2); ?> in
                                        <?php else : ?>
                                            <?php echo round($packetWeather->rain_24h, 2); ?> мм
                                        <?php endif; ?>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            <?php else : ?>
                                <td title="<?php echo $packetWeather->getRainSummary(true, true, false); ?>">
                                    <?php if ($packetWeather->rain_since_midnight !== null) : ?>
                                        <?php if (isImperialUnitUser()) : ?>
                                            <?php echo round(convertMmToInch($packetWeather->rain_since_midnight), 2); ?> in
                                        <?php else : ?>
                                            <?php echo round($packetWeather->rain_since_midnight, 2); ?> мм
                                        <?php endif; ?>
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>

                            <td title="Wind gust: <?php echo ($packetWeather->wind_gust !== null?round($packetWeather->wind_gust,2):'-'); ?> м/с">

                                <?php if (isImperialUnitUser()) : ?>
                                    <?php if ($packetWeather->wind_speed !== null && $packetWeather->wind_speed > 0) : ?>
                                        <?php echo round(convertMpsToMph($packetWeather->wind_speed), 2); ?> mph, <?php echo $packetWeather->wind_direction; ?>&deg;
                                    <?php elseif($packetWeather->wind_speed !== null) : ?>
                                        <?php echo round(convertMpsToMph($packetWeather->wind_speed), 2); ?> mph
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>

                                <?php else : ?>
                                    <?php if ($packetWeather->wind_speed !== null && $packetWeather->wind_speed > 0) : ?>
                                        <?php echo round($packetWeather->wind_speed, 2); ?> м/с, <?php echo $packetWeather->wind_direction; ?>&deg;
                                    <?php elseif($packetWeather->wind_speed !== null) : ?>
                                        <?php echo round($packetWeather->wind_speed, 2); ?> м/с
                                    <?php else : ?>
                                        -
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($packetWeather->luminosity !== null) : ?>
                                    <?php echo round($packetWeather->luminosity,0); ?> В/м&sup2;
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($packetWeather->snow !== null) : ?>
                                    <?php if (isImperialUnitUser()) : ?>
                                        <?php echo round(convertMmToInch($packetWeather->snow), 0); ?> in
                                    <?php else : ?>
                                        <?php echo round($packetWeather->snow, 0); ?> мм
                                    <?php endif; ?>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <p>

                <?php if ($weatherPackets[0]->rain_1h !== null) : ?>
                    * Опади за останню годину (наведіть, щоб побачити інші вимірювання опадів)<br/>
                <?php elseif ($weatherPackets[0]->rain_24h !== null) : ?>
                    * Опади за останні 24 години (наведіть, щоб побачити інші вимірювання опадів)<br/>
                <?php else : ?>
                    * Опади з півночі (наведіть, щоб побачити інші вимірювання опадів)<br/>
                <?php endif; ?>
                ** Поточна швидкість вітру в м/с (наведіть, щоб побачити поточну швидкість поривів вітру)
            </p>

        <?php endif; ?>

        <?php if (count($weatherPackets) === 0) : ?>
            <p><i><b>Недавніх погодних звітів не знайдено.</b></i></p>
        <?php endif; ?>

        <?php endif; ?>

        <?php if (count($weatherPackets) === 0) : ?>
            <p><i><b>Недавніх погодних звітів не знайдено.</b></i></p>
        <?php endif; ?>

        <div class="quiklink">
            Пряме посилання на цю сторінку: <input id="quiklink" type="text" value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>/station/<?php echo $station->name; ?>/<?php echo basename(__FILE__, '.php'); ?>/<?php echo $format; ?>/" readonly>
            <img id="quikcopy" src="/images/copy.svg"/>
        </div>

    </div>


    <script>
        $(document).ready(function() {
            var locale = window.navigator.userLanguage || window.navigator.language;
            moment.locale(locale);

            $('.weathertime').each(function() {
                if ($(this).html().trim() != '' && !isNaN($(this).html().trim())) {
                    $(this).html(moment(new Date(1000 * $(this).html())).format('L LTSZ'));
                }
            });

            $('#weather-rows').change(function () {
                loadView("/views/weather.php?id=<?php echo $station->id ?>&rows=" + $('#weather-rows').val() + "&page=1");
            });

            <?php if ($format=='table'): ?>

            <?php endif; ?>

            if (window.trackdirect) {
                <?php if ($station->latestConfirmedLatitude != null && $station->latestConfirmedLongitude != null) : ?>
                window.trackdirect.addListener("map-created", function() {
                    if (!window.trackdirect.focusOnStation(<?php echo $station->id ?>, true)) {
                        window.trackdirect.setCenter(<?php echo $station->latestConfirmedLatitude ?>, <?php echo $station->latestConfirmedLongitude ?>);
                    }
                });
                <?php endif; ?>
                window.trackdirect.addListener("trackdirect-init-done", function () {
                    window.liveData.start("<?php echo $station->name;?>", <?php echo $station->latestPacketTimestamp; ?>, 'wxcurrent');
                });
            }

            quikLink();
        });
    </script>
<?php endif; ?>