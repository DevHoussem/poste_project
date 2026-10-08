<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: sign_in.php");
    exit();
}

$host = "localhost";
$dbname = "poste_bd";
$username = "root";
$password = "";
try {

    $pdo= new PDO("mysql:host=$host;dbname=$dbname",$username,$password);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
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
#recent operations
$pre = $pdo->query(
    "SELECT *
     FROM operations
     ORDER BY date_operation DESC
     LIMIT 10"
);
$operations= $pre->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <script src="script.js"></script>
    <link rel="stylesheet" href="styledash.css">
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

        <a href="sign_in.php">
            Déconnexion
        </a>
    </div>
</div>
<div class="container">
    <h1>Dashboard</h1>
    <a href="add_operation.php" class="add_button">
        add operations +
    </a>
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

<h2>Last operations</h2>
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
    </tr>
    </thead>
    
    <tbody>
        <?php foreach ($operations as $operation): ?>
    <tr>
        <td>
        <?= htmlspecialchars($operation["date_operation"]) ?>
        </td>
        <td>
        <?= $operation["paiement_mandat"] ?>
        </td>
        <td>
        <?= $operation["emission_mandat"] ?>
        </td>
        <td>
        <?= $operation["retrait_ccp"] ?>
        </td>
        <td>
        <?= $operation["versement_ccp"] ?>
        </td>
        <td>
        <?= $operation["retrait_carnet"] ?>
        </td>
        <td>
        <?= $operation["versement_carnet"] ?>
        </td>
        <td>
        <?= $operation["western_union"] ?>
        </td>
        <td>
        <strong>
        <?= $operation["total_operations"] ?>
        </strong>
        </td>
    </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>
    <div class="export-box">

    <h2>Export Operations</h2>

    <form method="POST">

        <label>Export type:</label>

        <select name="type" id="exportType" onchange="changeExportType()">

            <option value="all">
                All operations
            </option>

            <option value="date">
                Specific date
            </option>

            <option value="range">
                Date range
            </option>

        </select>


        <!-- ONE DATE -->

        <div id="singleDate" style="display:none;">

            <label>Select date:</label>

            <input type="date" name="date">

        </div>


        <!-- DATE RANGE -->

        <div id="dateRange" style="display:none;">

            <label>Start date:</label>

            <input type="date" name="date_start">

            <label>End date:</label>

            <input type="date" name="date_end">

        </div>
        <div>
            <label>Format:</label>
            <select name="format">
            <option value="csv">CSV</option>
            <option value="pdf">PDF</option>
            </select>
        </div>


        <button type="submit" name="export">
            Export CSV
        </button>

    </form>

</div>
</div>
</body>
</html>
