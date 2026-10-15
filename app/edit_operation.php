<?php
require_once __DIR__ . "/auth/auth.php";
require_once __DIR__ . "/operations.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: dashboard.php");
    exit();
}

$statement = $pdo->prepare("SELECT * FROM operations WHERE id = :id");
$statement->execute(["id" => $id]);
$operation = $statement->fetch(PDO::FETCH_ASSOC);

if (!$operation) {
    header("Location: ../errors/404.html");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        verifyCsrfToken($_POST["csrf_token"] ?? null);
        $values = operationInput($_POST);
        $values["id"] = $id;

        $statement = $pdo->prepare(
            "UPDATE operations SET
                date_operation = :date_operation,
                paiement_mandat = :paiement_mandat,
                emission_mandat = :emission_mandat,
                retrait_ccp = :retrait_ccp,
                versement_ccp = :versement_ccp,
                retrait_carnet = :retrait_carnet,
                versement_carnet = :versement_carnet,
                western_union = :western_union,
                total_operations = :total_operations
             WHERE id = :id"
        );
        $statement->execute($values);
        header("Location: dashboard.php");
        exit();
    } catch (InvalidArgumentException | RuntimeException $exception) {
        $error = $exception->getMessage();
    }
}

$operation = array_merge($operation, $_POST);
$csrfToken = ensureCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/styleadd.css">
    <title>Edit operation</title>
</head>
<body>
<div class="container">
    <h1>Edit operation</h1>
    <?php if ($error): ?>
        <div class="message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <div class="form-grid">
            <fieldset>
                <legend>Date</legend>
                <div class="form-group">
                    <label for="date_operation">Operation date</label>
                    <input type="date" id="date_operation" name="date_operation" value="<?= htmlspecialchars((string) ($operation["date_operation"] ?? "")) ?>" required>
                </div>
            </fieldset>
            <?php foreach (OPERATION_FIELDS as $field): ?>
                <fieldset>
                    <legend><?= htmlspecialchars(ucwords(str_replace("_", " ", $field))) ?></legend>
                    <div class="form-group">
                        <label for="<?= htmlspecialchars($field) ?>">Value</label>
                        <input type="number" id="<?= htmlspecialchars($field) ?>" name="<?= htmlspecialchars($field) ?>" value="<?= htmlspecialchars((string) ($operation[$field] ?? 0)) ?>" min="0" required>
                    </div>
                </fieldset>
            <?php endforeach; ?>
        </div>
        <button type="submit">Save changes</button>
    </form>
</div>
</body>
</html>
