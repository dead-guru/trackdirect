<?php require dirname(__DIR__) . "../../includes/bootstrap.php"; ?>

<?php
if (isset($_GET['c'])) {
    $station = StationRepository::getInstance()->getObjectByName(strtoupper($_GET['c']) ?? null);
} else {
    $station = StationRepository::getInstance()->getObjectById($_GET['id'] ?? null);
}
?>
<?php if ($station->isExistingObject()) : ?>
    <title><?php echo $station->name; ?> Огляд</title>
    <div class="modal-inner-content">
        <div class="modal-inner-content-menu">
            <span>Огляд</span>
            <a class="tdlink" title="Статистика" href="/views/statistics.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Статистика</a>
            <a class="tdlink" title="Графік данних" href="/views/trail.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Графік данних</a>
            <a class="tdlink" title="Погода" href="/views/weather.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Погода</a>
            <a class="tdlink" title="Телеметрія" href="/views/telemetry.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Телеметрія</a>
            <a class="tdlink" title="Сирі пакети" href="/views/raw.php?id=<?php echo $station->id ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">Сирі пакети</a>
        </div>

        <div class="horizontal-line">&nbsp;</div>

        <div class="overview-content-summary">
            <div>
                <div class="overview-content-summary-hr">
                    <?php if ($station->sourceId == 5) : ?>
                        ID:
                    <?php else: ?>
                        Ім'я:
                    <?php endif; ?>
                </div>
                <div class="overview-content-station" title="Назва станції/об'єкта">
                    <?php echo htmlentities($station->name); ?>
                </div>
            </div>

            <div>
                <div class="overview-content-summary-hr">
                    ID Станції:
                </div>
                <div class="overview-content-station" title="Ідентифікатор станції.">
                    <?php echo $station->id; ?>
                </div>
            </div>

            <?php if ($station->sourceId != null) : ?>
                <div>
                    <div class="overview-content-summary-hr">Джерело:</div>
                    <div class="overview-content-station" title="Джерело цієї станції">
                        <?php echo $station->getSourceDescription(); ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($station->getOgnDevice() !== null) : ?>
                <br/>
                <?php if ($station->getOgnDevice()->registration != null) : ?>
                    <div>
                        <div class="overview-content-summary-hr">Реєстрація літака:</div>
                        <div class="overview-content-station" title="Реєстрація літака">
                            <b><?php echo htmlspecialchars($station->getOgnDevice()->registration); ?></b>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($station->getOgnDevice()->cn != null) : ?>
                    <div>
                        <div class="overview-content-summary-hr">Aircraft CN:</div>
                        <div class="overview-content-station" title="Aircraft CN">
                            <b><?php echo htmlspecialchars($station->getOgnDevice()->cn); ?></b>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ($station->getOgnDdbAircraftTypeName() !== null) : ?>
                <div>
                    <div class="overview-content-summary-hr">Aircraft Type:</div>
                    <div class="overview-content-station" title="Type of aircraft">
                        <?php echo htmlspecialchars($station->getOgnDdbAircraftTypeName()); ?>
                    </div>
                </div>
                <?php if ($station->getOgnDevice()->aircraftModel != null) : ?>
                    <div>
                        <div class="overview-content-summary-hr">Aircraft Model:</div>
                        <div class="overview-content-station" title="Aircraft Model">
                            <?php echo htmlspecialchars($station->getOgnDevice()->aircraftModel); ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php elseif ($station->getOgnAircraftTypeName() != null) : ?>
                <div>
                    <div class="overview-content-summary-hr">Aircraft Type:</div>
                    <div class="overview-content-station" title="Type of aircraft">
                        <?php echo htmlspecialchars($station->getOgnAircraftTypeName()); ?>
                    </div>
                </div>
            <?php else : ?>
                <div>
                    <div class="overview-content-summary-hr">Символ:</div>
                    <div class="overview-content-station" title="Символ">
                        <img src="<?php echo $station->getIconFilePath(24, 24); ?>" alt="Останнній символ" />
                        <span>&nbsp;<?php echo htmlentities($station->getLatestSymbolDescription()); ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Latest Packet -->
            <?php if ($station->latestPacketId !== null) : ?>
                <?php $latestPacket = PacketRepository::getInstance()->getObjectById($station->latestPacketId, $station->latestPacketTimestamp); ?>
                <div class="overview-content-divider"></div>

                <div>
                    <div class="overview-content-summary-hr">Останній пакет:</div>
                    <div class="overview-content-summary-cell-type overview-content-summary-indent">Пакет <?php echo $latestPacket->getPacketTypeName(); ?></div>
                </div>

                <?php $latestPacketSender = SenderRepository::getInstance()->getObjectById($latestPacket->senderId); ?>
                <?php if ($latestPacketSender->name != $station->name) : ?>
                <div>
                    <div class="overview-content-summary-hr-indent">Відправник:</div>
                    <div class="overview-content-summary-indent" title="Відправник поточного пакету">
                        <?php $latestPacketSenderStation = StationRepository::getInstance()->getObjectByNameAndSenderId($latestPacketSender->name, $latestPacketSender->id); ?>
                        <?php if ($latestPacketSenderStation->isExistingObject()) : ?>
                            <a class="tdlink" title="Відправник об'єкта" href="/views/overview.php?id=<?php echo $latestPacketSenderStation->id; ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>">
                                <?php echo htmlentities($latestPacketSenderStation->name); ?>
                            </a>
                        <?php else : ?>
                            <?php echo $latestPacketSender->name; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div>
                    <div class="overview-content-summary-hr-indent">Час отримання:</div>
                    <div title="Відмітка часу останнього пакета" id="latest-timestamp" class="overview-content-summary-cell-time overview-content-summary-indent">
                        <?php echo $station->latestPacketTimestamp; ?>
                    </div>
                </div>


                <div>
                    <div class="overview-content-summary-hr-indent">Вік:</div>
                    <div title="Вік останнього пакета" id="latest-timestamp-age" class="overview-content-summary-cell-time overview-content-summary-indent">
                        <?php echo $station->latestPacketTimestamp; ?>
                    </div>
                </div>


                <div>
                    <div class="overview-content-summary-hr-indent">Шлях:</div>
                    <div class="overview-content-summary-cell-path overview-content-summary-indent" title="Останній шлях"><?php echo $latestPacket->rawPath; ?></div>
                </div>

                <div>
                    <div class="overview-content-summary-hr-indent">Обладнання:</div>
                    <div class="overview-content-summary-cell-path overview-content-summary-indent" title="Останнє використане обладнання"><?php echo $latestPacket->getEquipmentTypeName(); ?></div>
                </div>

                <?php if ($latestPacket->comment != '') : ?>
                    <div>
                        <div class="overview-content-summary-hr-indent">Коментар:</div>
                        <div title="Коментар, знайдений в останньому пакеті" id="latest-packet-comment" class="overview-content-summary-indent">
                            <?php echo htmlentities($latestPacket->comment); ?>
                        </div>
                    </div>
                <?php endif;?>

                <?php if ($latestPacket->getPacketOgn()->isExistingObject()) : ?>
                    <div style="line-height: 8px">&nbsp;</div>
                    <?php if ($latestPacket->getPacketOgn()->ognSignalToNoiseRatio !== null) : ?>
                        <div>
                            <div class="overview-content-summary-hr-indent">Відношення сигнал-шум:</div>
                            <div class="overview-content-summary-indent" title="Виміряне відношення сигналу до шуму при прийомі."><?php echo $latestPacket->getPacketOgn()->ognSignalToNoiseRatio; ?> дБ</div>
                        </div>
                    <?php endif;?>

                    <?php if ($latestPacket->getPacketOgn()->ognBitErrorsCorrected !== null) : ?>
                        <div>
                            <div class="overview-content-summary-hr-indent">Виправлені біти:</div>
                            <div class="overview-content-summary-indent" title="Кількість виправлених помилкових бітів у пакеті під час прийому."><?php echo $latestPacket->getPacketOgn()->ognBitErrorsCorrected; ?></div>
                        </div>
                    <?php endif;?>

                    <?php if ($latestPacket->getPacketOgn()->ognFrequencyOffset !== null) : ?>
                        <div>
                            <div class="overview-content-summary-hr-indent">Зміщення частоти:</div>
                            <div class="overview-content-summary-indent" title="Зміщення частоти, виміряне при прийманні"><?php echo $latestPacket->getPacketOgn()->ognFrequencyOffset; ?> kHz</div>
                        </div>
                    <?php endif;?>
                <?php endif;?>

            <?php endif;?>


            <!-- Latest Weather -->
            <?php if ($station->latestWeatherPacketTimestamp !== null) : ?>
                <div class="overview-content-divider"></div>

                <div>
                    <div class="overview-content-summary-hr">Метеопакет:</div>
                    <div id="weather-timestamp" class="overview-content-summary-cell-weather-time" title="Остання отримана погода">
                        <?php echo $station->latestWeatherPacketTimestamp; ?>
                    </div>
                </div>

                <?php if ($station->latestWeatherPacketComment != '') : ?>
                    <div>
                        <div class="overview-content-summary-hr-indent">Коментар/ПЗ:</div>
                        <div class="overview-content-summary-cell-time overview-content-summary-indent" title="Коментар/програмне забезпечення метеопакету.">
                            <?php echo htmlentities($station->latestWeatherPacketComment); ?><br/>
                        </div>
                    </div>
                <?php endif;?>
            <?php endif;?>

            <!-- Latest Telemetry -->
            <?php if ($station->latestTelemetryPacketTimestamp !== null) : ?>
                <div class="overview-content-divider"></div>

                <div>
                    <div class="overview-content-summary-hr">Остання телеметрія:</div>
                    <div id="telemetry-timestamp" class="overview-content-summary-cell-telemetry-time" title="Останні отримані телеметричні дані">
                        <?php echo $station->latestTelemetryPacketTimestamp; ?>
                    </div>
                </div>
            <?php endif;?>

            <!-- Latest Position -->
            <?php if ($station->latestConfirmedPacketId !== null) : ?>

                <div class="overview-content-divider"></div>

                <div>
                    <div class="overview-content-summary-hr">Остання позиція</div>
                    <div id="overview-content-latest-position" class="overview-content-summary-cell-position" title="Остання позиція (яка була схвалена нашими фільтрами)">
                        <?php echo round($station->latestConfirmedLatitude, 5); ?>, <?php echo round($station->latestConfirmedLongitude, 5); ?>
                    </div>
                </div>

                <div>
                    <div class="overview-content-summary-hr-indent">Час прийому:</div>
                    <div id="position-timestamp" class="overview-content-summary-cell-time overview-content-summary-indent" title="Час отримання останньої позиції">
                        <?php if ($station->latestPacketId == $station->latestConfirmedPacketId && $station->latestPacketTimestamp == $station->latestConfirmedPacketTimestamp) : ?>
                            (Отримано в останньому пакеті)
                        <?php else : ?>
                            <?php echo $station->latestConfirmedPacketTimestamp; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div>
                    <div class="overview-content-summary-hr">&nbsp;</div>
                    <div class="overview-content-summary-cell-position">
                        <a href="?sid=<?php echo $station->id; ?>" onclick="
                            if (window.parent && window.parent.trackdirect) {
                                $('.modal', parent.document).hide();
                                window.parent.trackdirect.filterOnStationId([]);
                                window.parent.trackdirect.filterOnStationId([<?php echo $station->id; ?>]);
                                return false;
                            }">Показати на мапі</a>
                    </div>
                </div>


                <?php $latestConfirmedPacket = PacketRepository::getInstance()->getObjectById($station->latestConfirmedPacketId, $station->latestConfirmedPacketTimestamp); ?>
                <?php if ($latestConfirmedPacket->isExistingObject() && $latestConfirmedPacket->posambiguity > 0) : ?>
                <div>
                    <div class="overview-content-summary-hr-indent">Невизначеність позиції:</div>
                    <div class="overview-content-summary-cell-posambiguity overview-content-summary-indent" title="Якщо активована невизначеність позиції, то GPS-позиція може бути неточною">Так</div>
                </div>
                <?php endif;?>

                <?php if ($latestConfirmedPacket->isExistingObject()) : ?>
                    <?php if ($latestConfirmedPacket->speed != '' || $latestConfirmedPacket->course != '' || $latestConfirmedPacket->altitude != '') : ?>
                        <?php if (round($latestConfirmedPacket->speed) != 0 || round($latestConfirmedPacket->course) != 0 || round($latestConfirmedPacket->altitude) != 0) : ?>

                            <?php if ($latestConfirmedPacket->speed != '') : ?>
                            <div>
                                <div class="overview-content-summary-hr-indent">Швидкість:</div>
                                <div title="Latest speed" class="overview-content-summary-indent">
                                    <?php if (isImperialUnitUser()) : ?>
                                        <?php echo round(convertKilometerToMile($latestConfirmedPacket->speed), 2); ?> mph
                                    <?php else : ?>
                                        <?php echo round($latestConfirmedPacket->speed, 2); ?> км/г
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif;?>

                            <?php if ($latestConfirmedPacket->course != '') : ?>
                            <div>
                                <div class="overview-content-summary-hr-indent">Курс:</div>
                                <div title="Latest course" class="overview-content-summary-indent"><?php echo $latestConfirmedPacket->course; ?>&deg;</div>
                            </div>
                            <?php endif;?>

                            <?php if ($latestConfirmedPacket->altitude != '') : ?>
                            <div>
                                <div class="overview-content-summary-hr-indent">Висота:</div>
                                <div title="Latest altitude" class="overview-content-summary-indent">
                                    <?php if (isImperialUnitUser()) : ?>
                                        <?php echo round(convertMeterToFeet($latestConfirmedPacket->altitude), 2); ?> ft
                                    <?php else : ?>
                                        <?php echo round($latestConfirmedPacket->altitude, 2); ?> м
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif;?>

                        <?php endif;?>
                    <?php endif;?>

                    <?php if ($latestConfirmedPacket->getPacketOgn()->isExistingObject()) : ?>
                        <?php if ($latestConfirmedPacket->getPacketOgn()->ognClimbRate !== null) : ?>
                            <div>
                                <div class="overview-content-summary-hr-indent">Швидкість підйому:</div>
                                <div class="overview-content-summary-indent" title="Швидкість підйому в футах на хвилину."><?php echo $latestConfirmedPacket->getPacketOgn()->ognClimbRate; ?> fpm</div>
                            </div>
                        <?php endif;?>

                        <?php if ($latestConfirmedPacket->getPacketOgn()->ognTurnRate !== null) : ?>
                            <div>
                                <?php $turnRateNote = true; ?>
                                <div class="overview-content-summary-hr-indent">Швидкість повороту:</div>
                                <div class="overview-content-summary-indent" title="Поточна швидкість повороту."><?php echo $latestConfirmedPacket->getPacketOgn()->ognTurnRate; ?> rot</div>
                            </div>
                        <?php endif;?>
                    <?php endif;?>
                <?php endif;?>

                <!-- Latest PHG and RNG -->
                <?php if ($latestConfirmedPacket && $latestConfirmedPacket->isExistingObject()) : ?>
                    <?php if ($latestConfirmedPacket->phg != null || $latestConfirmedPacket->latestPhgTimestamp != null) : ?>
                        <div class="overview-content-divider"></div>
                        <div>
                            <div class="overview-content-summary-hr">Latest PHG:</div>
                            <div class="overview-content-summary-cell-phg" title="Power-Height-Gain (and directivity)">
                                <?php echo $latestConfirmedPacket->getPHGDescription(true); ?><br/>
                                (Calculated range:
                                    <?php if (isImperialUnitUser()) : ?>
                                        <?php echo round(convertKilometerToMile($latestConfirmedPacket->getPHGRange(true)/1000),2); ?> miles)
                                    <?php else : ?>
                                        <?php echo round($latestConfirmedPacket->getPHGRange(true)/1000,2); ?> km)
                                    <?php endif; ?>
                            </div>
                        </div>
                    <?php endif;?>

                    <?php if ($latestConfirmedPacket->rng != null || $latestConfirmedPacket->latestRngTimestamp != null) : ?>
                        <div class="overview-content-divider"></div>
                        <div>
                            <div class="overview-content-summary-hr">Latest RNG:</div>
                            <div class="overview-content-summary-cell-phg" title="The pre-calculated radio range">
                                <?php if (isImperialUnitUser()) : ?>
                                    <?php echo round(convertKilometerToMile($latestConfirmedPacket->getRng(true)), 2); ?> miles
                                <?php else : ?>
                                    <?php echo round($latestConfirmedPacket->getRng(true), 2); ?> km
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif;?>
                <?php endif;?>
            <?php endif;?>

            <!-- Latest Symbols -->
            <?php $stationLatestSymbols = $station->getLatestIconFilePaths(22, 22); ?>
            <?php if ($stationLatestSymbols !== null && count($stationLatestSymbols) > 1) : ?>
                <div class="overview-content-divider"></div>
                <div>
                    <div class="overview-content-summary-hr">Останні використані символи:</div>
                    <div title="Останні символи, які використовувала ця станція">
                        <?php foreach ($stationLatestSymbols as $symbolPath) : ?>
                            <img src="<?php echo $symbolPath; ?>" alt="Символ"/>&nbsp;
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>


            <!-- Packet Frequency & Totals-->
            <div class="overview-content-divider"></div>
            <div>
                <div class="overview-content-summary-hr">Packet frequency:</div>
                <div class="overview-content-packet-frequency" title="Calculated packet frequency" id="packet_frequency"><span>calculating ...</span></div>
            </div>
            <div>
                <div class="overview-content-summary-hr">Packets stored:</div>
                <div class="overview-content-packet-frequency" title="Total packets recorded" id="total_packets"><span>retrieving ...</span></div>
            </div>
            <?php $stationLatestBulletinPacket = PacketRepository::getInstance()->getBulletinObjectListByStationId($station->id, 1, 0, 2);?>
            <?php if ($stationLatestBulletinPacket != null) : ?>
                <div class="overview-content-divider"></div>
                <div>
                    <div class="overview-content-summary-hr">Latest bulletin:</div>
                    <div class="overview-content-packet-frequency" title="Latest bulletin"><span><?php echo $stationLatestBulletinPacket[0]->to_call; ?>: <?php echo $stationLatestBulletinPacket[0]->comment; ?></span> (<span id="bulletin-timestamp"><?php echo $stationLatestBulletinPacket[0]->timestamp; ?></span>)</div>
                </div>
            <?php endif; ?>
            <br/><span style="float:left;width:400px;"><img src="/images/dotColor3.svg" style="height:24px;vertical-align:middle;" id="live-img" /><span id="live-status" style="vertical-align:middle;">Waiting for connection...</span></span>

            <div class="overview-content-divider"></div>
        </div>

        <div class="overview-content-symbol" id ="overview-content-symbol-<?php echo $station->id; ?>">
            <img src="<?php echo $station->getIconFilePath(150, 150); ?>" alt="Останній символ" title="<?php echo $station->getLatestSymbolDescription(); ?>"/>
            <?php if ($station->latestPacketId !== null) : ?>
                <br/>
                <div style="text-align: center; padding-top: 30px;">
                    <?php if ($station->getOgnDevice() !== null && $station->getOgnDevice()->registration != null) : ?>
                        <div>
                            Пошук фотографій <a href="https://www.jetphotos.com/registration/<?php echo $station->getOgnDevice()->registration; ?>" target="_blank"><?php echo htmlspecialchars($station->getOgnDevice()->registration); ?></a>!
                        </div>
                    <?php endif; ?>

                    <?php if ($station->sourceId == 1) : ?>
                        <?php if ($station->getLiklyHamRadioCallsign() !== null) : ?>
                            <div>Знайти <a href="https://www.qrz.com/db/<?php echo $station->getLiklyHamRadioCallsign(); ?>" target="_blank"><?php echo htmlspecialchars($station->getLiklyHamRadioCallsign()); ?></a> на QRZ</div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div>Експортувати <a href="/data/kml.php?id=<?php echo $station->id; ?>"><?php echo htmlspecialchars($station->name); ?></a> данні в KML</div>
                </div>
                <div style="clear: both;"></div>
            <?php endif; ?>
        </div>

        <div class="horizontal-line">&nbsp;</div>

        <div class="overview-content-summary">

            <!-- Related stations -->
            <?php $relatedStations = StationRepository::getInstance()->getRelatedObjectListByStationId($station->id, 15); ?>
            <?php if (count($relatedStations) > 1) : ?>
                <div>
                    <?php $relatedStattionNote = true; ?>
                    <div class="overview-content-summary-hr">Пов'язані станції/об'єкти.:</div>
                    <div class="overview-content-station-list" title="Станції зі схожим викликним знаком, окрім SSID, або об'єкти від пов'язаного відправника">
                        <?php foreach ($relatedStations as $relatedStation) : ?>
                            <?php if ($relatedStation->id != $station->id) : ?>
                                <img src="<?php echo $relatedStation->getIconFilePath(22, 22); ?>" alt="Символ"/>&nbsp;
                                <span><a class="tdlink" href="/views/overview.php?id=<?php echo $relatedStation->id; ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>"><?php echo htmlentities($relatedStation->name) ?></a></span>
                                <br/>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="overview-content-divider"></div>
            <?php endif; ?>


            <!-- Close by stations -->
            <?php $closeByStations = StationRepository::getInstance()->getCloseByObjectListByStationId($station->id, 15); ?>
            <?php if (count($closeByStations) > 1) : ?>
                <div>
                    <div class="overview-content-summary-hr">Поблизу розташовані станції/об'єкти:</div>
                    <div class="overview-content-station-list" title="Найближчі станції/об'єкти на поточній позиції"  style="width:100%">
                        &nbsp;
                        <span>
                          <span class="nts" style="width:10.4em"><b>Last Received</b></span>
                          <span style="width:7.7em"><b>Distance</b></span>
                      </span>
                        <br/>
                        <?php foreach ($closeByStations as $closeByStation) : ?>
                            <?php if ($closeByStation->id != $station->id) : ?>

                                <img src="<?php echo $closeByStation->getIconFilePath(22, 22); ?>" alt="Символ"/>&nbsp;
                                <span>
                                    <a class="tdlink" href="/views/overview.php?id=<?php echo $closeByStation->id; ?>&imperialUnits=<?php echo $_GET['imperialUnits'] ?? 0; ?>"><?php echo htmlentities($closeByStation->name) ?></a>
                                    <span class="nts"><?php echo $closeByStation->latestPacketTimestamp; ?></span>
                                    <span>
                                        <?php if (isImperialUnitUser()) : ?>
                                            <?php if (convertMeterToYard($closeByStation->getDistance($station->latestConfirmedLatitude, $station->latestConfirmedLongitude)) < 1000) : ?>
                                                <?php echo round(convertMeterToYard($closeByStation->getDistance($station->latestConfirmedLatitude, $station->latestConfirmedLongitude)), 0); ?> yd
                                            <?php else : ?>
                                                <?php echo round(convertKilometerToMile($closeByStation->getDistance($station->latestConfirmedLatitude, $station->latestConfirmedLongitude) / 1000), 2); ?> miles
                                            <?php endif; ?>
                                        <?php else : ?>
                                            <?php if ($closeByStation->getDistance($station->latestConfirmedLatitude, $station->latestConfirmedLongitude) < 1000) : ?>
                                                <?php echo round($closeByStation->getDistance($station->latestConfirmedLatitude, $station->latestConfirmedLongitude), 0); ?> м
                                            <?php else : ?>
                                                <?php echo round($closeByStation->getDistance($station->latestConfirmedLatitude, $station->latestConfirmedLongitude) / 1000, 2); ?> км
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </span>

                                </span>
                            <br/>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="overview-content-divider"></div>
            <?php endif; ?>
        </div>

        <?php if (count($relatedStations) > 1 || count($closeByStations) > 1) : ?>
            <div class="horizontal-line">&nbsp;</div>
        <?php endif; ?>

        <div class="overview-content-explanations">
            <ul>
                <li>Вказаний "ідентифікатор станції" - це ідентифікатор, який присвоюється цій станції на цьому веб-сайті. Цей ідентифікатор корисний при створенні посилання на цей веб-сайт. Докладнішу інформацію можна знайти в розділі "Про нас/Часті запитання".</li>

                <?php if ($station->sourceId == 5) : ?>
                    <li>Packets is received from the <a target="_blank" href="http://wiki.glidernet.org/">Open Glider Network</a>. The goal of the Open Glider Network project is to create a unified platform for tracking aircraft equipped with FLARM and OGN trackers.</li>

                    <li>Aircraft device details such as Registration, CN and Aircraft Model is collected from the <a target="_blank" href="http://wiki.glidernet.org/ddb">OGN Devices DataBase</a>. We will only display information that can be used to identify an aircraft if the aircraft device details exists in the OGN Devices DataBase, and if the setting "I don't want this device to be identified" is deactivated.</li>

                    <li>Detailed aircraft information is received from the <a target="_blank" rel="nofollow" href="http://wiki.glidernet.org/ddb">OGN Devices DataBase</a>. If the aircraft is not registered in the <a target="_blank" rel="nofollow" href="http://wiki.glidernet.org/ddb">OGN Devices DataBase</a> or if the aircraft does not want to be identified, the aircraft type indicated in the FLARM/OGN packet is displayed (but only if the website is configured to show aircrafts not registered in the database, that setting is not enabled by default). We also adapt which symbol that is used based on the selected aircraft type.</li>

                    <li>According to <a target="_blank" href="http://wiki.glidernet.org/">OGN</a>, 4-5dB is about the limit of meaningful reception (but currently we still save packets with low SNR).</li>

                    <li>According to <a target="_blank" href="http://wiki.glidernet.org/">OGN</a>, it is recommended that you ignore packets that have a high CRC error rate (>5) as their information may be corrupt (but currently we still save packets with high CRC error rate).</li>

                    <li>1rot is the standard aircraft rotation rate of 1 half-turn per minute (equal to 1.5&deg; per second).</li>
                <?php endif; ?>

                <?php if ($station->sourceId == 1) : ?>
                    <li>Для отримання кращого розуміння маршруту APRS я рекомендую прочитати <a target="_blank" rel="nofollow" href="http://wa8lmf.net/DigiPaths/">пояснення, написане wa8lmf</a>.</li>

                    <li>Якщо цей пакет має позначку "Posambiguity" (Невизначеність позиції), це означає, що відправлена позиція є неоднозначною (з кінця позиції було відсічено певну кількість цифр).</li>

                    <li>PHG означає Power-Height-Gain (та напрямленість). Висота не є висотою над рівнем моря, це висота над середнім рельєфом (HAAT = Height Above Average Terrain). PHG використовується для розрахунку відносного діапазону радіосполучення станції. Якщо ця станція повідомляла кілька позицій або символів, дані PHG будуть використовуватися лише для позиції та символу, використовуваного в PHG-пакеті.</li>

                    <li>RNG - це "попередньо розрахований всенапрямний радіус радіостанції" (повідомлений самою станцією). Якщо ця станція повідомляла кілька позицій або символів, дані RNG будуть використовуватися лише для позиції та символу, використовуваного в пакеті RNG. Здається, що багато станцій D-STAR використовують значення RNG для визначення діапазону D-STAR.</li>

                    <li>Один об'єкт може бути надісланий кількома різними відправниками. На мапі вони можуть ділити один і той же маршрут, але у них є власні окремі "Інформація про станцію" модальні вікна.</li>

                    <li>Якщо у станції є більше 15 пов'язаних станцій, ми відображатимемо лише 10 найближчих пов'язаних станцій.</li>
                <?php endif; ?>

                <?php if ($station->sourceId == 2) : ?>
                    <li>Station data is received from the CWOP network (Citizen Weather Observer Program). Visit <a href="http://www.wxqa.com/cwop_info.htm" target="_blank">CWOP Information</a> if you want know more!</li>


                <?php endif; ?>

            </ul>
        </div>
        <div class="quiklink">
            Link directly to this page: <input id="quiklink" type="text" value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>/station/<?php echo $station->name; ?>/" readonly>
            <img id="quikcopy" src="/images/copy.svg"/>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            var locale = window.navigator.userLanguage || window.navigator.language;
            latest_packet_timestamp = <?php echo $station->latestPacketTimestamp; ?>;
            moment.locale(locale);

            $('#overview-content-comment, #overview-content-beacon, #overview-content-status').each(function() {
                if ($(this).html().trim() != '') {
                    $(this).html(Autolinker.link($(this).html()));
                }
            });

            $('#latest-timestamp, #comment-timestamp, #status-timestamp, #beacon-timestamp, #position-timestamp, #weather-timestamp, #telemetry-timestamp, #bulletin-timestamp, .nts').each(function() {
                if ($(this).html().trim() != '' && !isNaN($(this).html().trim())) {
                    $(this).html(moment(new Date(1000 * $(this).html())).format('L LTSZ'));
                }
            });

            if ($('#latest-timestamp-age').length && $('#latest-timestamp-age').html().trim() != '' && !isNaN($('#latest-timestamp-age').html().trim())) {
                $('#latest-timestamp-age').html(moment(new Date(1000 * $('#latest-timestamp-age').html())).locale('en').fromNow());
            }

            if (window.trackdirect) {
                <?php if ($station->latestConfirmedLatitude != null && $station->latestConfirmedLongitude != null) : ?>
                    window.trackdirect.addListener("map-created", function() {
                        if (!window.trackdirect.focusOnStation(<?php echo $station->id ?>, true)) {
                            window.trackdirect.setCenter(<?php echo $station->latestPacketTimestamp ?>, <?php echo $station->latestConfirmedLongitude ?>);
                        }
                    });
                window.trackdirect.addListener("trackdirect-init-done", function () {
                    window.liveData.start("<?php echo $station->name; ?>", <?php echo $station->latestConfirmedPacketTimestamp; ?>, 'overview');
                });
                <?php endif; ?>
                loadOverviewData(<?php echo $station->id ?>);
                quikLink();
            }
        });
    </script>
<?php endif; ?>
