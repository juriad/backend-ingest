<?php

function toInflux($data)
{
    $row = [];
    $row['took_ms'] = $_GET['took'];

    $row['boot_cnt'] = $data->boot->count;
    $row['boot_reason'] = $data->boot->reason;

    $row["wifi_rssi_dbm"] = $data->wifi->rssi;
    $row["wifi_took_ms"] = $data->wifi->took;

    foreach ($data->temp as $i => $temp) {
        if ($temp) {
            $row["temp${i}_c"] = $temp->value;
            $row["temp${i}_took_ms"] = $temp->took;
        }
    }

    foreach ($data->light as $i => $light) {
        if ($light) {
            $row["light${i}_lux"] = $light->value;
            $row["light${i}_took_ms"] = $light->took;
        }
    }

    $opts = [
        'http' => [
            'method' => "POST",
            'content' => "esp32-kotel " . http_build_query($row, '', ','),
        ]
    ];

    $params = [
        "db" => "basement"
    ];

    file_put_contents('influx', json_encode($opts));

    file_get_contents(
        'http://localhost:18086/write?' . http_build_query($params),
        false,
        stream_context_create($opts)
    );
}

$json = file_get_contents('php://input');
$data = json_decode($json);
file_put_contents('update', json_encode([$_GET]) . "\n" . $json);

toInflux($data);

echo "OK";
