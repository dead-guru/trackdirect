<?php require dirname(__DIR__) . "../../includes/bootstrap.php"; ?>

<?php $station = StationRepository::getInstance()->getObjectById($_GET['id'] ?? null); ?>
<?php if ($station->isExistingObject()) : ?>
    <?php
        $days = 10;
        if (!isAllowedToShowOlderData()) {
            $days = 1;
        }
    ?>
    <?php $senderStats = PacketPathRepository::getInstance()->getSenderPacketPathSatistics($station->id, time() - (60*60*24*$days)); ?>
    <?php $receiverStats = PacketPathRepository::getInstance()->getReceiverPacketPathSatistics($station->id, time() - (60*60*24*$days)); ?>

    <title><?php echo $station->name; ?> Stats</title>
    <div class="modal-inner-content">
        <div class="modal-inner-content-menu">
            <a class="tdlink" title="Огляд" href="/views/overview.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Огляд</a>
            <span>Статистика</span>
            <a class="tdlink" title="Графік данних" href="/views/trail.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Графік данних</a>
            <a class="tdlink" title="Погода" href="/views/weather.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Погода</a>
            <a class="tdlink" title="Телеметрія" href="/views/telemetry.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Телеметрія</a>
            <a class="tdlink" title="Сирі пакети" href="/views/raw.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Сирі пакети</a>
        </div>

        <div class="horizontal-line">&nbsp;</div>

        <p>
            Статистика зв'язку, яку ми показуємо тут, може відрізнятися від подібної статистики зв'язку на інших веб-сайтах, ймовірно, тому що цей веб-сайт не збирає пакети з тих самих APRS-серверів. Кожен APRS-сервер виконує фільтрацію дублікатів, і пакет, який вважається дублікатом, може відрізнятися від одного сервера до іншого, залежно від того, з якого APRS-сервера ви отримуєте свої дані.
        </p>

        <?php if (count($senderStats) > 0) : ?>
            <p>Stations that heard <?php echo htmlspecialchars($station->name) ?> <b>directly</b> during the latest <?php echo $days; ?> day(s).</p>
            <div class="datagrid datagrid-statistics" style="max-width:700px;">
                <table>
                    <thead>
                        <tr>
                            <th>Станція</th>
                            <th>Кількість пакетів</th>
                            <th>Останній пакет</th>
                            <th>Найбільша відстань</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($senderStats as $stats) : ?>
                        <?php $otherStation = StationRepository::getInstance()->getObjectById($stats["station_id"]) ?>
                        <tr>
                            <td>
                                <img alt="Символ" src="<?php echo $otherStation->getIconFilePath(22, 22); ?>" style="vertical-align: middle;"/>&nbsp;
                                <a class="tdlink" href="/views/overview.php?id=<?php echo $otherStation->id; ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>"><?php echo htmlentities($otherStation->name) ?></a>
                            </td>
                            <td>
                                <?php echo $stats["number_of_packets"]; ?>
                            </td>
                            <td class="latest-heard">
                                <?php echo $stats["latest_timestamp"];?>
                            </td>

                            <td class="longest-distance">
                                <?php if ($stats["longest_distance"] !== null) : ?>
                                    <?php if (isImperialUnitUser()) : ?>
                                        <?php echo round(convertKilometerToMile($stats["longest_distance"] / 1000), 2); ?> miles
                                    <?php else : ?>
                                        <?php echo round($stats["longest_distance"] / 1000, 2); ?> км
                                    <?php endif; ?>
                                <?php else : ?>
                                    &nbsp;
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <br/>
        <?php endif; ?>


        <?php if (count($receiverStats) > 0) : ?>
            Станції, безпосередньо почуті станцією <?php echo htmlspecialchars($station->name); ?> протягом останніх <?php echo $days; ?> днів.
            <div class="datagrid datagrid-statistics" style="max-width:700px;">
                <table>
                    <thead>
                        <tr>
                            <th>Станція</th>
                            <th>Кількість пакетів</th>
                            <th>Останній пакет</th>
                            <th>Найбільша відстань</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($receiverStats as $stats) : ?>
                        <?php $otherStation = StationRepository::getInstance()->getObjectById($stats["station_id"]) ?>
                        <tr>
                            <td>
                                <img alt="Symbol" src="<?php echo $otherStation->getIconFilePath(22, 22); ?>" style="vertical-align: middle;"/>&nbsp;
                                <a class="tdlink" href="/views/overview.php?id=<?php echo $otherStation->id; ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>"><?php echo htmlentities($otherStation->name) ?></a>

                            </td>
                            <td>
                                <?php echo $stats["number_of_packets"]; ?>
                            </td>
                            <td class="latest-heard">
                                <?php echo $stats["latest_timestamp"];?>
                            </td>
                            <td class="longest-distance">
                                <?php if ($stats["longest_distance"] !== null) : ?>
                                    <?php if (isImperialUnitUser()) : ?>
                                        <?php echo round(convertKilometerToMile($stats["longest_distance"] / 1000), 2); ?> miles
                                    <?php else : ?>
                                        <?php echo round($stats["longest_distance"] / 1000, 2); ?> км
                                    <?php endif; ?>
                                <?php else : ?>
                                    &nbsp;
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <br/>
        <?php endif; ?>


        <?php if (count($senderStats) == 0 && count($receiverStats) == 0): ?>
            <p><i><b>Немає статистики радіозв'язку протягом останніх <?php echo $days; ?> днів.</b></i></p>
        <?php endif; ?>
    </div>
    <script>
        $(document).ready(function() {
            var locale = window.navigator.userLanguage || window.navigator.language;
            moment.locale(locale);

            $('.latest-heard').each(function() {
                if ($(this).html().trim() != '' && !isNaN($(this).html().trim())) {
                    $(this).html(moment(new Date(1000 * $(this).html())).format('L LTSZ'));
                }
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
