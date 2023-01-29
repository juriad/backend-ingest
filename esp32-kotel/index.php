<?php

$b = $_GET['boot'];

echo ($b * 2);

$line = date(DATE_ISO8601) . " " . $b . "\n";

file_put_contents("log", $line, FILE_APPEND);
