<?php
require_once __DIR__ . "/auth/auth.php";
require_once __DIR__ . "/operations.php";

$filterError = "";
try {
    $filters = operationFilters($_GET);
} catch (InvalidArgumentException $exception) {
    $filters = ["date_start" => "", "date_end" => ""];
    $filterError = $exception->getMessage();
}

$page = max(1, (int) ($_GET["page"] ?? 1));
$pageSize = 10;
$conditions = [];
$parameters = [];
if ($filters["date_start"] !== "") {
    $conditions[] = "date_operation >= :date_start";
    $parameters["date_start"] = $filters["date_start"];
}
if ($filters["date_end"] !== "") {
    $conditions[] = "date_operation <= :date_end";
    $parameters["date_end"] = $filters["date_end"];
}
$whereClause = $conditions === [] ? "" : " WHERE " . implode(" AND ", $conditions);

$countStatement = $pdo->prepare("SELECT COUNT(*) FROM operations" . $whereClause);
$countStatement->execute($parameters);
$operationCount = (int) $countStatement->fetchColumn();
$pageCount = max(1, (int) ceil($operationCount / $pageSize));
$page = min($page, $pageCount);
$offset = ($page - 1) * $pageSize;

/* staistics */
# total operations
$total_operations = $pdo->query(
    "SELECT COALESCE(SUM(total_operations),0)
     FROM operations"
)->fetchColumn();
#mandats
$paiement_mandat = $pdo->query(
    "SELECT COALESCE(SUM(paiement_mandat),0)
     FROM operations"
)->fetchColumn();
$emmission_mandat=$pdo->query(
    "SELECT COALESCE(SUM(emission_mandat),0)
     FROM operations"
)->fetchColumn();
$total_mandat=$emmission_mandat+$paiement_mandat;
#ccp
$retrait_ccp= $pdo->query(
    "SELECT COALESCE(SUM(retrait_ccp),0)
     FROM operations"
)->fetchColumn();
$versement_ccp=$pdo->query(
    "SELECT COALESCE(SUM(versement_ccp),0)
     FROM operations"
)->fetchColumn();
$total_ccp=$retrait_ccp+$versement_ccp;
#carnet
$retrait_carnet= $pdo->query(
    "SELECT COALESCE(SUM(retrait_carnet),0)
     FROM operations"
)->fetchColumn();
$versement_carnet=$pdo->query(
    "SELECT COALESCE(SUM(versement_carnet),0)
     FROM operations"
)->fetchColumn();
$total_carnet=$retrait_carnet+$versement_carnet;
#western union
$western_union=$pdo->query(
    "SELECT COALESCE(SUM(western_union),0)
     FROM operations"
)->fetchColumn();
#days
$days=$pdo->query(
    "SELECT COUNT(*)
     FROM operations"
)->fetchColumn();
#filtered operations
$pre = $pdo->prepare(
    "SELECT *
     FROM operations" . $whereClause . "
     ORDER BY date_operation DESC, id DESC
     LIMIT :limit OFFSET :offset"
);
foreach ($parameters as $name => $value) {
    $pre->bindValue(":$name", $value);
}
$pre->bindValue(":limit", $pageSize, PDO::PARAM_INT);
$pre->bindValue(":offset", $offset, PDO::PARAM_INT);
$pre->execute();
$operations = $pre->fetchAll(PDO::FETCH_ASSOC);
$csrfToken = ensureCsrfToken();
$operationError = $_SESSION["operation_error"] ?? "";
unset($_SESSION["operation_error"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="../js/script.js"></script>
    <link rel="stylesheet" href="../style/styledash.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="navbar">
    <h1>Poste Tunisienne</h1>
    <div>
        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="add_operation.php">
            Ajouter
        </a>

        <a href="auth/sign_in.php">
            Déconnexion
        </a>
    </div>
</div>
<div class="container">
    <h1>Dashboard</h1>
    <a href="add_operation.php" class="add_button">
        add operations +
    </a>
    <?php if ($filterError || $operationError): ?>
        <div class="message">
            <?= htmlspecialchars($filterError ?: $operationError) ?>
        </div>
    <?php endif; ?>
    <form method="get" class="filter-form">
        <label for="date_start">From</label>
        <input type="date" id="date_start" name="date_start" value="<?= htmlspecialchars($filters["date_start"]) ?>">
        <label for="date_end">To</label>
        <input type="date" id="date_end" name="date_end" value="<?= htmlspecialchars($filters["date_end"]) ?>">
        <button type="submit">Filter</button>
        <a href="dashboard.php">Clear</a>
    </form>
    <div class="cards">
        <div class="card">
            <h3>Total operations</h3>
            <p>
                <?= number_format($total_operations) ?>
            </p>
        </div>

        <div class="card">
            <h3>Total operations for mandats</h3>
            <p>
                <?= number_format($total_mandat) ?>
            </p>
        </div>

        <div class="card">
            <h3>Total operations for CCP</h3>
            <p>
                <?= number_format($total_ccp) ?>
            </p>
        </div>

        <div class="card">
            <h3>Total operations for carnet</h3>
            <p>
            <?= number_format($total_carnet) ?>
            </p>
        </div>

    </div>
<div class="table-container">

<h2>Operations</h2>
<table>
    <thead>
    <tr>
    <th>Date</th>
    <th>paiement mandats</th>
    <th>emission mandat</th>
    <th>retrait CCP</th>
    <th>versement CCP</th>
    <th>retrait carnet</th>
    <th>versement carnet</th>
    <th>Western Union</th>
    <th>Total</th>
    <th>Actions</th>
    </tr>
    </thead>
    
    <tbody>
        <?php foreach ($operations as $operation): ?>
    <tr>
        <td>
        <?= htmlspecialchars($operation["date_operation"]) ?>
        </td>
        <td>
        <?= number_format((int) $operation["paiement_mandat"]) ?>
        </td>
        <td>
        <?= number_format((int) $operation["emission_mandat"]) ?>
        </td>
        <td>
        <?= number_format((int) $operation["retrait_ccp"]) ?>
        </td>
        <td>
        <?= number_format((int) $operation["versement_ccp"]) ?>
        </td>
        <td>
        <?= number_format((int) $operation["retrait_carnet"]) ?>
        </td>
        <td>
        <?= number_format((int) $operation["versement_carnet"]) ?>
        </td>
        <td>
        <?= number_format((int) $operation["western_union"]) ?>
        </td>
        <td>
        <strong>
        <?= number_format((int) $operation["total_operations"]) ?>
        </strong>
        </td>
        <td>
            <a href="edit_operation.php?id=<?= (int) $operation["id"] ?>">Edit</a>
            <form method="post" action="delete_operation.php" onsubmit="return confirm('Delete this operation?');">
                <input type="hidden" name="id" value="<?= (int) $operation["id"] ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <button type="submit">Delete</button>
            </form>
        </td>
    </tr>
        <?php endforeach; ?>
        <?php if ($operations === []): ?>
        <tr>
            <td colspan="10">No operations found for the selected dates.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php if ($pageCount > 1): ?>
    <nav aria-label="Operation pages">
        <?php for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++): ?>
            <a href="?date_start=<?= urlencode($filters["date_start"]) ?>&date_end=<?= urlencode($filters["date_end"]) ?>&page=<?= $pageNumber ?>">
                <?= $pageNumber ?>
            </a>
        <?php endfor; ?>
    </nav>
<?php endif; ?>

</div>
    <div class="export-box">

    <h2>Export Operations</h2>

    <form method="get" action="export_operations.php">
        <input type="hidden" name="date_start" value="<?= htmlspecialchars($filters["date_start"]) ?>">
        <input type="hidden" name="date_end" value="<?= htmlspecialchars($filters["date_end"]) ?>">
        <button type="submit">Export filtered CSV</button>

    </form>

</div>
</div>
</body>
</html>
