<?php require dirname(__DIR__) . "../../includes/bootstrap.php"; ?>

<?php $station = StationRepository::getInstance()->getObjectById($_GET['id'] ?? null); ?>
<?php if ($station->isExistingObject()) : ?>

    <?php
        $page = $_GET['page'] ?? 1;
        $rows = $_GET['rows'] ?? 25;
        $offset = ($page - 1) * $rows;

        if (($_GET['category'] ?? 1) == 2) {
            $packets = PacketRepository::getInstance()->getObjectListWithRawBySenderStationId($station->id, $rows, $offset);
            $count = PacketRepository::getInstance()->getNumberOfPacketsWithRawBySenderStationId($station->id);
        } else {
            $packets = PacketRepository::getInstance()->getObjectListWithRawByStationId($station->id, $rows, $offset);
            $count = PacketRepository::getInstance()->getNumberOfPacketsWithRawByStationId($station->id);
        }

        $pages = ceil($count / $rows);
    ?>

    <title><?php echo $station->name; ?> Raw Packets</title>
    <div class="modal-inner-content">
        <div class="modal-inner-content-menu">
            <a class="tdlink" title="Огляд" href="/views/overview.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Огляд</a>
            <a class="tdlink" title="Статистика" href="/views/statistics.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Статистика</a>
            <a class="tdlink" title="Графік данних" href="/views/trail.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Графік данних</a>
            <a class="tdlink" title="Погода" href="/views/weather.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Погода</a>
            <a class="tdlink" title="Телеметрія" href="/views/telemetry.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Телеметрія</a>
            <span>Сирі пакети</span>
        </div>

        <div class="horizontal-line">&nbsp;</div>

        <p>
            Це останні отримані пакети, збережені в нашій базі даних для станції/об'єкта <?php echo $station->name; ?>. Якщо пакети не відображаються, це означає, що відправник не передавав жодних пакетів протягом останніх 24 годин.
        </p>

        <?php if ($station->sourceId == 5) : ?>
            <p>
                Ми не зберігаємо сирі пакети для літаків, які не існують у <a target="_blank" href="http://wiki.glidernet.org/ddb">Базі даних пристроїв OGN</a>. Ми відображаємо інформацію, яка може бути використана для ідентифікації літака, тільки якщо деталі пристрою літака існують в Базі даних пристроїв OGN і налаштування "Я не хочу, щоб цей пристрій був ідентифікований" вимкнено.
            </p>
        <?php else : ?>
            <p>
                Якщо ви порівнюєте сирі пакети з аналогічними даними з інших веб-сайтів, вони можуть відрізнятися (особливо, щодо шляху). Причина в тому, що ми не збираємо пакети з одних і тих самих серверів APRS-IS. Кожен сервер APRS-IS виконує фільтрацію дублікатів, і який пакет вважається дублікатом може відрізнятися в залежності від того, з якого серверу APRS-IS ви отримуєте свої дані.
            </p>
        <?php endif; ?>

        <div class="form-container">
            <?php if ($station->stationTypeId == 1) : ?>
                <select id="raw-category" style="float:left; margin-right: 5px;">
                    <option <?php echo (($_GET['category'] ?? 1) == 1 ? 'selected' : ''); ?> value="1">Пакети, що стосуються <?php echo $station->name; ?></option>
                    <option <?php echo (($_GET['category'] ?? 1) == 2 ? 'selected' : ''); ?> value="2">Пакети, відправлені <?php echo $station->name; ?></option>
                </select>
            <?php endif; ?>

            <select id="raw-type" style="float:left; margin-right: 5px;">
                <option <?php echo (($_GET['type'] ?? 1) == 1 ? 'selected' : ''); ?> value="1">Сирі пакети</option>
                <option <?php echo (($_GET['type'] ?? 1) == 2 ? 'selected' : ''); ?> value="2">Розкодовані дані</option>
            </select>

            <select id="raw-rows" style="float:left; margin-right: 5px;" class="pagination-rows">
                <option <?php echo ($rows == 25 ? 'selected' : ''); ?> value="25">25 рядків</option>
                <option <?php echo ($rows == 50 ? 'selected' : ''); ?> value="50">50 рядків</option>
                <option <?php echo ($rows == 100 ? 'selected' : ''); ?> value="100">100 рядків</option>
                <option <?php echo ($rows == 200 ? 'selected' : ''); ?> value="200">200 рядків</option>
                <option <?php echo ($rows == 300 ? 'selected' : ''); ?> value="300">300 рядків</option>
            </select>
        </div>

        <?php if ($pages > 1): ?>
            <div class="pagination">
              <a class="tdlink" href="/views/raw.php?id=<?php echo $station->id; ?>&category=<?php echo ($_GET['category'] ?? 1) ?>&type=<?php echo ($_GET['type'] ?? 1); ?>&rows=<?php echo $rows; ?>&page=1"><<</a>
              <?php for($i = max(1, $page - 3); $i <= min($pages, $page + 3); $i++) : ?>
              <a href="/views/raw.php?id=<?php echo $station->id; ?>&category=<?php echo ($_GET['category'] ?? 1) ?>&type=<?php echo ($_GET['type'] ?? 1); ?>&rows=<?php echo $rows; ?>&page=<?php echo $i; ?>" <?php echo ($i == $page ? 'class="tdlink active"': 'class="tdlink"')?>><?php echo $i ?></a>
              <?php endfor; ?>
              <a class="tdlink" href="/views/raw.php?id=<?php echo $station->id; ?>&category=<?php echo ($_GET['category'] ?? 1) ?>&type=<?php echo ($_GET['type'] ?? 1); ?>&rows=<?php echo $rows; ?>&page=<?php echo $pages; ?>">>></a>
            </div>
        <?php endif; ?>

        <div id="raw-content-output">
            <?php foreach (array_slice($packets, 0, $rows) as $packet) : ?>
                <?php if (($_GET['type'] ?? 1) == 1) : ?>
                    <p>
                        <span class="raw-packet-timestamp"><?php echo $packet->timestamp; ?></span>:

                        <?php if (in_array($packet->mapId, Array(3, 6))) : ?>
                        <span class="raw-packet-error">
                        <?php else : ?>
                        <span>
                        <?php endif; ?>

                            <?php echo str_replace_first(htmlspecialchars($station->name . '>'), '<b>' . htmlspecialchars($station->name) . '</b>&gt;', htmlspecialchars($packet->raw)); ?>

                            <?php if ($packet->mapId == 3) : ?>
                            &nbsp;<b>[Дублікат]</b>
                            <?php elseif ($packet->mapId == 6) : ?>
                            &nbsp;<b>[Отримано в неправильному порядку]</b>
                            <?php endif; ?>

                        </span>
                    </p>
                <?php elseif (($_GET['type'] ?? 1) == 2) : ?>
                    <div class="decoded">
                        <div class="datagrid">
                            <table>
                                <thead>
                                    <tr>
                                        <th colspan="2">
                                            <?php if (in_array($packet->mapId, Array(3, 6))) : ?>
                                            <span class="raw-packet-error">
                                            <?php else : ?>
                                            <span>
                                            <?php endif; ?>
                                                <span class="raw-packet-timestamp"><?php echo $packet->timestamp; ?></span>

                                                <?php if ($packet->mapId == 3) : ?>
                                                &nbsp;<b>[Дублікат]</b>
                                                <?php elseif ($packet->mapId == 6) : ?>
                                                &nbsp;<b>[Отримано в неправильному порядку]</b>
                                                <?php endif; ?>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Raw</td>
                                        <td>
                                            <?php echo str_replace_first(htmlspecialchars($station->name . '>'), '<b>' . htmlspecialchars($station->name) . '</b>&gt;', htmlspecialchars($packet->raw)); ?>
                                        </td>
                                    </tr>

                                    <tr><td>Тип пакету</td><td><?php echo $packet->getPacketTypeName(); ?></td></tr>

                                    <?php if ($packet->getStationObject()->stationTypeId == 2) : ?>
                                        <tr><td>Назва об'єкта</td><td><?php echo htmlspecialchars($packet->getStationObject()->name); ?></td></tr>
                                    <?php else : ?>
                                        <tr><td>Кличний</td><td><?php echo htmlspecialchars($packet->getStationObject()->name); ?></td></tr>
                                    <?php endif; ?>

                                    <?php if ($packet->getStationObject()->name != $packet->getSenderObject()->name) : ?>
                                        <tr><td>Відправник</td><td><?php echo htmlspecialchars($packet->getSenderObject()->name); ?></td></tr>
                                    <?php endif; ?>

                                    <tr><td>Шлях</td><td><?php echo htmlspecialchars($packet->rawPath); ?></td></tr>

                                    <?php if ($packet->reportedTimestamp != null) : ?>
                                        <tr><td>Час повідомлення</td><td><?php echo $packet->reportedTimestamp; ?> - <span class="raw-packet-timestamp"><?php echo $packet->reportedTimestamp; ?></span></td></tr>
                                    <?php endif; ?>

                                    <?php if ($packet->latitude != null && $packet->longitude != null) : ?>
                                        <tr><td>Широта</td><td><?php echo round($packet->latitude, 5); ?></td></tr>
                                        <tr><td>Довгота</td><td><?php echo round($packet->longitude, 5); ?></td></tr>
                                    <?php endif; ?>

                                    <?php if ($packet->symbol != null && $packet->symbolTable != null) : ?>
                                        <tr><td>Символ</td><td><?php echo htmlspecialchars($packet->symbol); ?></td></tr>
                                        <tr><td>Таблиця символів</td><td><?php echo htmlspecialchars($packet->symbolTable); ?></td></tr>
                                    <?php endif; ?>

                                    <?php if ($packet->speed != null) : ?>
                                        <?php if (isImperialUnitUser()) : ?>
                                            <tr><td>Швидкість</td><td><?php echo convertKilometerToMile($packet->speed); ?> mph</td></tr>
                                        <?php else : ?>
                                            <tr><td>Швидкість</td><td><?php echo $packet->speed; ?> км/г</td></tr>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($packet->course != null) : ?>
                                        <tr><td>Курс</td><td><?php echo $packet->course; ?>°</td></tr>
                                    <?php endif; ?>

                                    <?php if ($packet->altitude != null) : ?>
                                        <?php if (isImperialUnitUser()) : ?>
                                            <tr><td>Висота</td><td><?php echo convertMeterToFeet($packet->altitude); ?> ft</td></tr>
                                        <?php else : ?>
                                            <tr><td>Висота</td><td><?php echo $packet->altitude; ?> м</td></tr>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($packet->comment != null) : ?>
                                        <?php if ($packet->packetTypeId == 10) : ?>
                                            <tr><td>Статус</td><td><?php echo htmlspecialchars($packet->comment); ?></td></tr>
                                        <?php elseif ($packet->packetTypeId == 7) : ?>
                                            <tr><td>Маяк</td><td><?php echo htmlspecialchars($packet->comment); ?></td></tr>
                                        <?php else : ?>
                                            <tr><td>Коментар</td><td><?php echo htmlspecialchars($packet->comment); ?></td></tr>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($packet->posambiguity == 1) : ?>
                                        <tr><td>Позамбігвітність</td><td>Так</td></tr>
                                    <?php endif; ?>

                                    <?php if ($packet->phg != null) : ?>
                                        <?php if (isImperialUnitUser()) : ?>
                                            <tr><td>PHG</td><td><?php echo $packet->phg; ?> (Розрахована відстань: <?php echo round(convertKilometerToMile($packet->getPHGRange()/1000),2); ?> миль)</td></tr>
                                        <?php else : ?>
                                            <tr><td>PHG</td><td><?php echo $packet->phg; ?> (Розрахована відстань: <?php echo round($packet->getPHGRange()/1000,2); ?> км)</td></tr>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($packet->rng != null) : ?>
                                        <tr><td>RNG</td><td><?php echo $packet->rng; ?></td></tr>
                                    <?php endif; ?>

                                    <?php if ($station->latestWeatherPacketTimestamp !== null) : ?>
                                        <?php $weather = $packet->getPacketWeather(); ?>
                                        <?php if ($weather->isExistingObject()) : ?>
                                            <tr>
                                                <td>Погода</td>
                                                <td>
                                                    <table>
                                                        <tbody>
                                                            <?php if ($weather->wxRawTimestamp !== null) : ?>
                                                                <tr>
                                                                    <td>Час:</td><td><span class="raw-packet-timestamp"><?php echo $weather->wxRawTimestamp; ?></span></td>
                                                                </tr>
                                                            <?php endif; ?>

                                                            <?php if ($weather->temperature !== null) : ?>
                                                                <tr>
                                                                    <td>Температура:</td>
                                                                    <?php if (isImperialUnitUser()) : ?>
                                                                        <td><?php echo round(convertCelciusToFahrenheit($weather->temperature), 2); ?>&deg; F</td>
                                                                    <?php else : ?>
                                                                        <td><?php echo round($weather->temperature, 2); ?>&deg; C</td>
                                                                    <?php endif; ?>
                                                                </tr>
                                                            <?php endif; ?>

                                                            <?php if ($weather->humidity !== null) : ?>
                                                                <tr>
                                                                    <td>Вологість:</td>
                                                                    <td><?php echo $weather->humidity; ?>%</td>
                                                                </tr>
                                                            <?php endif; ?>

                                                            <?php if ($weather->pressure !== null) : ?>
                                                                <tr>
                                                                    <td>Тиск:</td>
                                                                    <?php if (isImperialUnitUser()) : ?>
                                                                        <td><?php echo round(convertMbarToMmhg($weather->pressure),1); ?> mmHg</td>
                                                                    <?php else : ?>
                                                                        <td><?php echo round($weather->pressure,1); ?> hPa</td>
                                                                    <?php endif; ?>
                                                                </tr>
                                                            <?php endif; ?>

                                                            <?php if ($weather->rain_1h !== null) : ?>
                                                                <tr>
                                                                    <td>Опади за час:</td>
                                                                    <?php if (isImperialUnitUser()) : ?>
                                                                        <td><?php echo round(convertMmToInch($weather->rain_1h),2); ?> in</td>
                                                                    <?php else : ?>
                                                                        <td><?php echo round($weather->rain_1h,2); ?> мм</td>
                                                                    <?php endif; ?>
                                                                </tr>
                                                            <?php endif; ?>

                                                            <?php if ($weather->rain_24h !== null) : ?>
                                                                <tr>
                                                                    <td>Опади за 24 години:</td>
                                                                    <?php if (isImperialUnitUser()) : ?>
                                                                        <td><?php echo round(convertMmToInch($weather->rain_24h),2); ?> in</td>
                                                                    <?php else : ?>
                                                                        <td><?php echo round($weather->rain_24h,2); ?> мм</td>
                                                                    <?php endif; ?>
                                                                </tr>
                                                            <?php endif; ?>

                                                            <?php if ($weather->rain_since_midnight !== null) : ?>
                                                                <tr>
                                                                    <td>Опади з початку доби:</td>
                                                                    <?php if (isImperialUnitUser()) : ?>
                                                                        <td><?php echo round(convertMmToInch($weather->rain_since_midnight),2); ?> in</td>
                                                                    <?php else : ?>
                                                                        <td><?php echo round($weather->rain_since_midnight,2); ?> мм</td>
                                                                    <?php endif; ?>
                                                                </tr>
                                                            <?php endif; ?>

                                                            <?php if (isImperialUnitUser()) : ?>
                                                                <?php if ($weather->wind_speed !== null && $weather->wind_speed > 0) : ?>
                                                                    <tr>
                                                                        <td>Швидкість вітру:</td>
                                                                        <td><?php echo round(convertMpsToMph($weather->wind_speed), 2); ?> mph, <?php echo $weather->wind_direction; ?>&deg;</td>
                                                                    </tr>
                                                                <?php elseif($weather->wind_speed !== null) : ?>
                                                                    <tr>
                                                                        <td>Швидкість вітру:</td>
                                                                        <td><?php echo round(convertMpsToMph($weather->wind_speed), 2); ?> mph</td>
                                                                    </tr>
                                                                <?php endif; ?>

                                                            <?php else : ?>
                                                                <?php if ($weather->wind_speed !== null && $weather->wind_speed > 0) : ?>
                                                                    <tr>
                                                                        <td>Швидкість вітру:</td>
                                                                        <td><?php echo round($weather->wind_speed, 2); ?> м/с, <?php echo $weather->wind_direction; ?>&deg;</td>
                                                                    </tr>
                                                                <?php elseif($weather->wind_speed !== null) : ?>
                                                                    <tr>
                                                                        <td>Швидкість вітру:</td>
                                                                        <td><?php echo round($weather->wind_speed, 2); ?> м/с</td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                            <?php endif; ?>

                                                            <?php if ($weather->luminosity !== null) : ?>
                                                                <tr>
                                                                    <td>Яскравість:</td><td><?php echo round($weather->luminosity,0); ?> В/м&sup2;</td>
                                                                </tr>
                                                            <?php endif; ?>

                                                            <?php if ($weather->snow !== null) : ?>
                                                                <tr>
                                                                <?php if (isImperialUnitUser()) : ?>
                                                                    <td>Сніг:</td><td><?php echo round(convertMmToInch($weather->snow), 0); ?> in</td>
                                                                <?php else : ?>
                                                                    <td>Сніг:</td><td><?php echo round($weather->snow, 0); ?> мм</td>
                                                                <?php endif; ?>
                                                                </tr>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>

                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($station->latestTelemetryPacketTimestamp !== null) : ?>
                                        <?php $telemetry = $packet->getPacketTelemetry(); ?>
                                        <?php if ($telemetry->isExistingObject()) : ?>
                                            <tr>
                                                <td>Аналогові значення телеметрії</td>
                                                <td>
                                                    <table>
                                                        <tbody>
                                                            <?php for ($i = 1; $i<=5; $i++) : ?>
                                                                <?php if ($telemetry->isValueSet($i)) : ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($telemetry->getValueParameterName($i)); ?>:</td>
                                                                        <td><?php echo round($telemetry->getValue($i), 2); ?> <?php echo htmlspecialchars($telemetry->getValueUnit($i)); ?></td>
                                                                    </tr>
                                                                <?php endif; ?>
                                                            <?php endfor; ?>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <?php if ($telemetry->bits !== null) : ?>
                                                <tr>
                                                    <td>Бітові значення телеметрії</td>
                                                    <td>
                                                        <table>
                                                            <tbody>
                                                                <?php for ($i = 1; $i<=8; $i++) : ?>
                                                                    <tr>
                                                                        <td><?php echo htmlspecialchars($telemetry->getBitParameterName($i)); ?>:</td>
                                                                        <td><?php echo $telemetry->getBit($i); ?></td>
                                                                    </tr>
                                                                <?php endfor; ?>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endif; ?>


                                        <?php if ($packet->packetTypeId == 7 && strstr($packet->raw, ':UNIT.')) : ?>
                                            <?php $pos = strpos($packet->raw, ':UNIT.'); ?>
                                            <tr>
                                                <td>Телеметрія UNIT</td>
                                                <td>
                                                    <?php echo htmlspecialchars(substr($packet->raw, $pos + 6)); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if ($packet->packetTypeId == 7 && strstr($packet->raw, ':BITS.')) : ?>
                                            <?php $pos = strpos($packet->raw, ':BITS.'); ?>
                                            <tr>
                                                <td>Телеметрія BITS</td>
                                                <td>
                                                    <?php echo htmlspecialchars(substr($packet->raw, $pos + 6)); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if ($packet->packetTypeId == 7 && strstr($packet->raw, ':EQNS.')) : ?>
                                            <?php $pos = strpos($packet->raw, ':EQNS.'); ?>
                                            <tr>
                                                <td>Телеметрія EQNS</td>
                                                <td>
                                                    <?php echo htmlspecialchars(substr($packet->raw, $pos + 6)); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>

                                        <?php if ($packet->packetTypeId == 7 && strstr($packet->raw, ':PARM.')) : ?>
                                            <?php $pos = strpos($packet->raw, ':PARM.'); ?>
                                            <tr>
                                                <td>Телеметрія PARM</td>
                                                <td>
                                                    <?php echo htmlspecialchars(substr($packet->raw, $pos + 6)); ?>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($packet->getPacketOgn()->isExistingObject()) : ?>
                                        <?php if ($packet->getPacketOgn()->ognSignalToNoiseRatio !== null) : ?>
                                            <tr>
                                                <td>Співвідношення сигнал-шум</td>
                                                <td>
                                                    <?php echo $packet->getPacketOgn()->ognSignalToNoiseRatio; ?> dB
                                                </td>
                                            </tr>
                                        <?php endif;?>

                                        <?php if ($packet->getPacketOgn()->ognBitErrorsCorrected !== null) : ?>
                                            <tr>
                                                <td>Виправлені біти</td>
                                                <td>
                                                    <?php echo $packet->getPacketOgn()->ognBitErrorsCorrected; ?>
                                                </td>
                                            </tr>
                                        <?php endif;?>

                                        <?php if ($packet->getPacketOgn()->ognFrequencyOffset !== null) : ?>
                                            <tr>
                                                <td>Зсув частоти</td>
                                                <td>
                                                    <?php echo $packet->getPacketOgn()->ognFrequencyOffset; ?> kHz
                                                </td>
                                            </tr>
                                        <?php endif;?>

                                        <?php if ($packet->getPacketOgn()->ognClimbRate !== null) : ?>
                                            <tr>
                                                <td>Швидкість підйому</td>
                                                <td>
                                                    <?php echo $packet->getPacketOgn()->ognClimbRate; ?> fpm
                                                </td>
                                            </tr>
                                        <?php endif;?>

                                        <?php if ($packet->getPacketOgn()->ognTurnRate !== null) : ?>
                                            <tr>
                                                <td>Швидкість повороту</td>
                                                <td>
                                                    <?php echo $packet->getPacketOgn()->ognTurnRate; ?> fpm
                                                </td>
                                            </tr>
                                        <?php endif;?>
                                    <?php endif;?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>

        <?php if (count($packets) == 0) : ?>
        <p>
            <b><i>Сирі пакети не знайдено.</i></b>
        </p>
        <?php endif; ?>
    </div>

    <script>
        $(document).ready(function() {
            var locale = window.navigator.userLanguage || window.navigator.language;
            moment.locale(locale);

            $('.raw-packet-timestamp').each(function() {
                if ($(this).html().trim() != '' && !isNaN($(this).html().trim())) {
                    $(this).html(moment(new Date(1000 * $(this).html())).format('L LTSZ'));
                }
            });

            $('#raw-category').change(function () {
                loadView("/views/raw.php?id=<?php echo $station->id ?>&type=" + $('#raw-type').val() + "&category=" + $('#raw-category').val() + "&rows=" + $('#raw-rows').val() + "&page=1");
            });

            $('#raw-type').change(function () {
                loadView("/views/raw.php?id=<?php echo $station->id ?>&type=" + $('#raw-type').val() + "&category=" + $('#raw-category').val() + "&rows=" + $('#raw-rows').val() + "&page=1");
            });

            $('#raw-rows').change(function () {
                loadView("/views/raw.php?id=<?php echo $station->id ?>&type=" + $('#raw-type').val() + "&category=" + $('#raw-category').val() + "&rows=" + $('#raw-rows').val() + "&page=1");
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
