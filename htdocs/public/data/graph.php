<?php

require dirname(__DIR__) . "../../includes/bootstrap.php";

$response = [];
$station = StationRepository::getInstance()->getObjectById($_GET['id'] ?? null);
if ($station->isExistingObject()) {
    $graphIdx = $_GET['index'] ?? 0;
    $graphType = $_GET['type'] ?? '';

    $startAt = $_GET['start'] ?? time()-864000;
    $endAt = $_GET['end'] ?? time();
    $maxDays = $_GET['days'] ?? 10;

    if (!isAllowedToShowOlderData()) {
        $maxDays = 1;
    }

    if ($graphType === 'telemetry') {       // No more than 250 rows for graphs
        $telemetryColors = array(
            0 => null,
            1 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            2 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            3 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            4 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            5 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
        );

        $telemetryPackets = PacketTelemetryRepository::getInstance()->getLatestObjectListByStationId($station->id, 1000, 0, $maxDays, 'asc', $startAt, $endAt);
        $latestPacketTelemetry = (count($telemetryPackets) > 0 ? $telemetryPackets[0] : new PacketTelemetry(null));

        // Ajax graph data
        if ($graphIdx > 0) {
            $response = array_merge($response, $telemetryColors[$graphIdx]);
            $response['label'] = $latestPacketTelemetry->getValueParameterName($graphIdx) . ' (' . universalDataUnitConvert(round($latestPacketTelemetry->getValue($graphIdx), 2), $latestPacketTelemetry->getValueUnit($graphIdx))['unit'] . ')';
            foreach ($telemetryPackets as $packetTelemetry) {
                $response['data'][] = array('x' => ($packetTelemetry->wxRawTimestamp != null ? $packetTelemetry->wxRawTimestamp : $packetTelemetry->timestamp) * 1000, 'y' => ($packetTelemetry->{"val$graphIdx"} !== null) ? universalDataUnitConvert(round($packetTelemetry->getValue($graphIdx), 2), $packetTelemetry->getValueUnit($graphIdx))['value'] : '');
            }
            $response['oldest_timestamp'] = $response['data'][0]['x'] / 1000;
            $response['latest_timestamp'] = $response['data'][sizeof($response['data'])-1]['x'] / 1000;
            $response['records'] = sizeof($response['data']);
        }
    }


    if ($graphType == 'telemetrybits') {       // No more than 250 rows for graphs
        $telemetryColors = array(
            0 => null,
            1 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            2 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            3 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            4 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            5 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            6 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            7 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7'),
            8 => array('borderColor' => '#2E2EFE', 'backgroundColor' => '#81BEF7')
        );

        $telemetryPackets = PacketTelemetryRepository::getInstance()->getLatestObjectListByStationId($station->id, 1000, 0, $maxDays, 'asc', $startAt, $endAt);
        $latestPacketTelemetry = (count($telemetryPackets) > 0 ? $telemetryPackets[0] : new PacketTelemetry(null));

        // Ajax graph data
        if ($graphIdx) {
            $response = array_merge($response, $telemetryColors[$graphIdx]);
            $response['label'] = $latestPacketTelemetry->getBitParameterName($graphIdx);
            foreach ($telemetryPackets as $i => $packetTelemetry) {
                if ($packetTelemetry->bits !== null && $i >= 2 ) {
                    $response['data'][] = array('x' => ($packetTelemetry->wxRawTimestamp != null ? $packetTelemetry->wxRawTimestamp : $packetTelemetry->timestamp) * 1000, 'y' => $packetTelemetry->getBit($graphIdx));
                }
            }
            $response['oldest_timestamp'] = $response['data'][0]['x'] / 1000;
            $response['latest_timestamp'] = $response['data'][sizeof($response['data'])-1]['x'] / 1000;
            $response['records'] = sizeof($response['data']);
        }
    }


    if ($graphType === 'weather') {       // No more than 1000 rows for graphs
        $graphLabels = array('Час', 'Температруа', 'Вологість', 'Тиск', 'Дощ (Останній Час)', 'Дощ (Остання доба)', 'Дощ (З початку доби)', 'Швидкість вітру', 'Напрям вітру', 'Освітлення', 'Сніг');
        $weatherPackets = PacketWeatherRepository::getInstance()->getLatestObjectListByStationIdAndLimit($station->id, 1000, 0, $maxDays, $startAt, $endAt);

        // Ajax graph data
        if ($graphIdx > 0) {
            switch ($graphIdx) {
                case 0:
                    break;
                case 1: // Temperature
                    foreach ($weatherPackets as $packetWeather) {
                        $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => isImperialUnitUser() ? round(convertCelciusToFahrenheit($packetWeather->temperature), 2) : round($packetWeather->temperature, 2));
                    }
                    $response['borderColor'] = '#2E2EFE';
                    $response['backgroundColor'] = '#81BEF7';
                    $response['label'] = $graphLabels[$graphIdx] . ' (°' . (isImperialUnitUser() ? 'F' : 'C') . ')';
                    break;
                case 2: // Humidity
                    foreach ($weatherPackets as $packetWeather) {
                        $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => $packetWeather->humidity);
                    }
                    $response['borderColor'] = '#31B404';
                    $response['backgroundColor'] = '#3ADF00';
                    $response['label'] = $graphLabels[$graphIdx] . ' (%)';
                    break;
                case 3: // Pressure
                    foreach ($weatherPackets as $packetWeather) {
                        $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => isImperialUnitUser() ? round(convertMbarToInchHg($packetWeather->pressure), 1) : round($packetWeather->pressure, 1));
                    }
                    $response['borderColor'] = '#DF0101';
                    $response['backgroundColor'] = '#FA5858';
                    $response['label'] = $graphLabels[$graphIdx] . ' (' . (isImperialUnitUser() ? 'inHg' : 'hPa') . ')';
                    break;
                case 4: // Rain - Last hour
                    if ($weatherPackets[0]->rain_1h !== null) {
                        foreach ($weatherPackets as $packetWeather) {
                            $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => isImperialUnitUser() ? round(convertMmToInch($packetWeather->rain_1h), 2) : round($packetWeather->rain_1h, 2));
                        }
                    }
                    $response['borderColor'] = '#31B404';
                    $response['backgroundColor'] = '#3ADF00';
                    $response['label'] = $graphLabels[$graphIdx] . ' (' . (isImperialUnitUser() ? 'in' : 'mm') . ')';
                    break;
                case 5: // Rain - Last 24 hours
                    if ($weatherPackets[0]->rain_24h !== null) {
                        foreach ($weatherPackets as $packetWeather) {
                            $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => isImperialUnitUser() ? round(convertMmToInch($packetWeather->rain_24h), 2) : round($packetWeather->rain_24h, 2));
                        }
                    }
                    $response['borderColor'] = '#31B404';
                    $response['backgroundColor'] = '#3ADF00';
                    $response['label'] = $graphLabels[$graphIdx] . ' (' . (isImperialUnitUser() ? 'in' : 'mm') . ')';
                    break;
                case 6: // Rain - Since midnight
                    if ($weatherPackets[0]->rain_since_midnight !== null) {
                        foreach ($weatherPackets as $packetWeather) {
                            $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => isImperialUnitUser() ? round(convertMmToInch($packetWeather->rain_since_midnight), 2) : round($packetWeather->rain_since_midnight, 2));
                        }
                    }
                    $response['borderColor'] = '#31B404';
                    $response['backgroundColor'] = '#3ADF00';
                    $response['label'] = $graphLabels[$graphIdx] . ' (' . (isImperialUnitUser() ? 'in' : 'mm') . ')';
                    break;
                case 7: // Wind speed
                    foreach ($weatherPackets as $packetWeather) {
                        if ($packetWeather->wind_speed !== null && $packetWeather->wind_speed > 0) {
                            $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => isImperialUnitUser() ? round(convertMpsToMph($packetWeather->wind_speed), 2) : round($packetWeather->wind_speed, 2));
                        } else {
                            $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => 0);
                        }
                    }
                    $response['borderColor'] = '#0174DF';
                    $response['backgroundColor'] = '#81BEF7';
                    $response['label'] = $graphLabels[$graphIdx] . ' (' . (isImperialUnitUser() ? 'mph' : 'm/s') . ')';
                    break;
                case 8: // Wind direction
                    foreach ($weatherPackets as $packetWeather) {
                        if ($packetWeather->wind_direction !== null && $packetWeather->wind_direction > 0) {
                            $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => round($packetWeather->wind_direction, 0));
                        } else {
                            $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => 0);
                        }
                    }
                    $response['borderColor'] = '#0174DF';
                    $response['backgroundColor'] = '#81BEF7';
                    $response['label'] = $graphLabels[$graphIdx] . ' (degrees)';
                    break;
                case 9: // Luminosity
                    foreach ($weatherPackets as $packetWeather) {
                        $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => round($packetWeather->luminosity, 0));
                    }
                    $response['borderColor'] = '#FF0080';
                    $response['backgroundColor'] = '#B4045F';
                    $response['label'] = $graphLabels[$graphIdx] . ' (lux)';
                    break;
                case 10: // Snow
                    foreach ($weatherPackets as $packetWeather) {
                        $response['data'][] = array('x' => ($packetWeather->wxRawTimestamp != null ? $packetWeather->wxRawTimestamp : $packetWeather->timestamp) * 1000, 'y' => isImperialUnitUser() ? round(convertMmToInch($packetWeather->snow), 0) : round($packetWeather->snow, 0));
                    }
                    $response['borderColor'] = '#A4A4A4';
                    $response['backgroundColor'] = '#E0ECF8';
                    $response['label'] = $graphLabels[$graphIdx] . ' (' . (isImperialUnitUser() ? 'in' : 'mm') . ')';
                    break;
            }
            $response['oldest_timestamp'] = $response['data'][0]['x'] / 1000;
            $response['latest_timestamp'] = $response['data'][sizeof($response['data'])-1]['x'] / 1000;
            $response['records'] = sizeof($response['data']);
        }
    }
}

header('Content-type: application/json');
echo json_encode($response);