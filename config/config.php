<?php

session_start();

function redirectToServiceUnavailablePage(): never
{
    header("Location: /errors/503.html", true, 303);
    exit();
}

$envFile = __DIR__ . "/../.env.local";

if (!is_readable($envFile)) {
    redirectToServiceUnavailablePage();
}

$environment = [];
$envLines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($envLines as $line) {
    $line = trim($line);

    if ($line === "" || str_starts_with($line, "#")) {
        continue;
    }

    [$key, $value] = array_pad(explode("=", $line, 2), 2, null);
    $key = trim($key);

    if ($key === "" || $value === null) {
        continue;
    }

    $environment[$key] = trim($value, " \t\n\r\0\x0B\"'");
}

$requiredEnvironment = ["HOST", "DBNAME", "DBUSERNAME", "DBPASSWORD"];

foreach ($requiredEnvironment as $key) {
    if (!array_key_exists($key, $environment)) {
        redirectToServiceUnavailablePage();
    }
}

$host = $environment["HOST"];
$dbname = $environment["DBNAME"];
$dbUsername = $environment["DBUSERNAME"];
$dbPassword = $environment["DBPASSWORD"];

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname",
        $dbUsername,
        $dbPassword
    );
} catch (PDOException $e) {
    redirectToServiceUnavailablePage();
}
