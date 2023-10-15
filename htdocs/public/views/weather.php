<?php require dirname(__DIR__) . "../../includes/bootstrap.php"; ?>

<?php $station = StationRepository::getInstance()->getObjectById($_GET['id'] ?? null); ?>
<?php if ($station->isExistingObject()) : ?>
    <?php
        $maxDays = 10;
        if (!isAllowedToShowOlderData()) {
            $maxDays = 1;
        }
        $page = $_GET['page'] ?? 1;
        $rows = $_GET['rows'] ?? 25;
        $offset = ($page - 1) * $rows;
        $weatherPackets = PacketWeatherRepository::getInstance()->getLatestObjectListByStationIdAndLimit($station->id, $rows, $offset, $maxDays);
        $count = PacketWeatherRepository::getInstance()->getLatestNumberOfPacketsByStationIdAndLimit($station->id, $maxDays);
        $pages = ceil($count / $rows);
    ?>

    <title><?php echo $station->name; ?> Weather</title>
    <div class="modal-inner-content">
        <div class="modal-inner-content-menu">
            <a class="tdlink" title="Огляд" href="/views/overview.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Огляд</a>
            <a class="tdlink" title="Статистика" href="/views/statistics.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Статистика</a>
            <a class="tdlink" title="Графік данних" href="/views/trail.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Графік данних</a>
            <span>Погода</span>
            <a class="tdlink" title="Телеметрія" href="/views/telemetry.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Телеметрія</a>
            <a class="tdlink" title="Сирі пакети" href="/views/raw.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Сирі пакети</a>
        </div>

        <div class="horizontal-line">&nbsp;</div>

        <?php if (count($weatherPackets) > 0) : ?>

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

        <?php if (count($weatherPackets) == 0) : ?>
            <p><i><b>Недавніх погодних звітів не знайдено.</b></i></p>
        <?php endif; ?>

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


            if (window.trackdirect) {
                <?php if ($station->latestConfirmedLatitude != null && $station->latestConfirmedLongitude != null) : ?>
                    window.trackdirect.addListener("map-created", function() {
                        if (!window.trackdirect.focusOnStation(<?php echo $station->id ?>, true)) {
                            window.trackdirect.setCenter(<?php echo $station->latestConfirmedLatitude ?>, <?php echo $station->latestConfirmedLongitude ?>);
                        }
                    });
                <?php endif; ?>
            }
        });
    </script>
<?php endif; ?>
