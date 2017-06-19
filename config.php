<?php
define("DB_HOST", "localhost");
define("DB_NAME", "weather");
define("DB_USER", "root");
define("DB_PASS", "root");
define("SITE_URL", "http://localhost/weather");

if (PHP_SAPI=='cli') define('EOL',"\n");
else define('EOL',"<br/>");

function dump($v) {
    echo "<pre>";
    var_dump($v);
    echo "</pre>";
}