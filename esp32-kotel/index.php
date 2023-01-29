<?php

file_put_contents('update', json_encode([$_GET]));


function toInflux()
{
    $boot_cnt = (int)$_GET['boot'];
    $rssi_dbm = (int)$_GET['rssi'];
    $temp1_c = (float)$_GET['temp1'];
    $temp2_c = (float)$_GET['temp2'];
    $temp3_c = (float)$_GET['temp3'];
    $light1_lux = (int)$_GET['light1'];

    $content = [
        'boot_cnt' => $boot_cnt,
        'rssi_dbm' => $rssi_dbm,
        'temp1_c' => $temp1_c,
        'temp2_c' => $temp2_c,
        'temp3_c' => $temp3_c,
        'light1_lux' => $light1_lux
    ];

    $opts = [
        'http' => [
            'method' => "POST",
            'content' => "esp32-kotel " . http_build_query($content, '', ','),
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

toInflux();

echo "OK";
