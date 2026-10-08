<?php
require_once __DIR__ . "/auth/auth.php";
require_once __DIR__ . "/operations.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: dashboard.php");
    exit();
}

try {
    verifyCsrfToken($_POST["csrf_token"] ?? null);
    $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);
    if (!$id) {
        throw new InvalidArgumentException("Invalid operation.");
    }

    $statement = $pdo->prepare("DELETE FROM operations WHERE id = :id");
    $statement->execute(["id" => $id]);
} catch (InvalidArgumentException | RuntimeException $exception) {
    $_SESSION["operation_error"] = $exception->getMessage();
}

header("Location: dashboard.php");
exit();
