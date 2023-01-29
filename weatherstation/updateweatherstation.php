<?php

$url = 'http://rtupdate.wunderground.com/weatherstation/updateweatherstation.php?' . http_build_query($_GET);

#$url = 'http://localhost:8081/weatherstation?' . http_build_query($_GET);
$result = file_get_contents($url);

file_put_contents('update', json_encode([$_GET, $result]));

function f2c($f) {
	return 5 / 9 * ($f - 32);
}

function in2hpa($in) {
	return $in * 33.86389;
}

function p2dec($p) {
	return $p / 100;
}

function mph2kmph($mph) {
	return $mph * 1.609344;
}

function in2mm($in) {
	return $in * 25.4;
}

function toInflux() {
	$barometric_pressure_hpa = in2hpa($_GET['baromin']);

	$temperature_c = f2c($_GET['tempf']);
	$humidity = $_GET['humidity'];
	$dew_point_c = f2c($_GET['dewptf']);

	$wind_speed_kmph = mph2kmph($_GET['windspeedmph']);
	$wind_gust_kmph = mph2kmph($_GET['windgustmph']);
	$wind_direction = $_GET['winddir'];

	$rain_mm = in2mm($_GET['rainin']);
	$daily_rain_mm = in2mm($_GET['dailyrainin']);

	$indoor_temperature_c = f2c($_GET['indoortempf']);
	$indoor_humidity = $_GET['indoorhumidity'];

	$content = [
		'barometric_pressure_hpa' => $barometric_pressure_hpa,
		'temperature_c' => $temperature_c,
		'humidity' => $humidity,
		'dew_point_c' => $dew_point_c,
		'wind_speed_kmph' => $wind_speed_kmph,
		'wind_gust_kmph' => $wind_gust_kmph,
		'wind_direction' => $wind_direction,
		'rain_mm' => $rain_mm,
		'daily_rain_mm' => $daily_rain_mm,
		'indoor_temperature_c' => $indoor_temperature_c,
		'indoor_humidity' => $indoor_humidity
	];

	$opts = [
		'http' => [
			'method' => "POST",
			'content' => "weather " . http_build_query($content, '', ','),
		]
	];

	$params = [
		"db" => "meteo"
	];

	file_put_contents('influx', json_encode($opts));

	file_get_contents(
		'http://localhost:18086/write?' . http_build_query($params),
		false,
		stream_context_create($opts)
	);
}

toInflux();
