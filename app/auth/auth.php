<?php

require_once __DIR__ . "/../../config/config.php";

if (!isset($_SESSION["admin"])) {
    header("Location: auth/sign_in.php");
    exit();
}
