<?php
require_once __DIR__ . "/auth/auth.php";
require_once __DIR__ . "/operations.php";

$message = "";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        $values = operationInput($_POST);
        $sql = "INSERT INTO operations
            (date_operation, paiement_mandat, emission_mandat, retrait_ccp,
             versement_ccp, retrait_carnet, versement_carnet, western_union,
             total_operations)
            VALUES
            (:date_operation, :paiement_mandat, :emission_mandat, :retrait_ccp,
             :versement_ccp, :retrait_carnet, :versement_carnet, :western_union,
             :total_operations)";

        $pre = $pdo->prepare($sql);
        $pre->execute($values);
        header("Location: dashboard.php");
        exit();
    } catch (InvalidArgumentException $exception) {
        $error = $exception->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../style/styleadd.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operations</title>
</head>
<body>
    <div class="navbar">
    <strong>Poste Tunisienne</strong>
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="auth/sign_in.php">Sign out</a>
        </div>
    </div>
    <div class="container">
    <h1>Add operation </h1>
    <?php if ($message): ?>
    <div class="message">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="message">
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>
    <form method="post">
        <div class="form-grid">
            <fieldset>
                <legend>Date d'operation</legend>
            <div class="form-group">
            <label>Date</label>
            <input type="date" name="date_operation" required>
            </div>
            </fieldset>
            <!-- Mandats Section -->
        <fieldset>
            <legend>Mandats</legend>
            <div class="form-group">
                <label for="paiement_mandats">Paiement Mandats:</label>
                <input type="number" id="paiement_mandats" name="paiement_mandat" value="0" min="0" required>
            </div>
            <div class="form-group">
                <label for="emission_mandats">Emission Mandats:</label>
                <input type="number" id="emission_mandats" name="emission_mandat" value="0" min="0" required>
            </div>
        </fieldset>

        <!-- CCP Accounts Section -->
        <fieldset>
            <legend>CCP Accounts</legend>
            <div class="form-group">
                <label for="retrait_ccp">Retrait CCP:</label>
                <input type="number" id="retrait_ccp" name="retrait_ccp" value="0" min="0" required>
            </div>
            <div class="form-group">
                <label for="versement_ccp">Versement CCP:</label>
                <input type="number" id="versement_ccp" name="versement_ccp" value="0" min="0" required>
            </div>
        </fieldset>

        <!-- Carnet Section -->
        <fieldset>
            <legend>Carnet Accounts</legend>
            <div class="form-group">
                <label for="retrait_carnet">Retrait Carnet:</label>
                <input type="number" id="retrait_carnet" name="retrait_carnet" value="0" min="0" required>
            </div>
            <div class="form-group">
                <label for="versement_carnet">Versement Carnet:</label>
                <input type="number" id="versement_carnet" name="versement_carnet" value="0" min="0" required>
            </div>
        </fieldset>

        <!-- Western Union Section -->
        <fieldset>
            <legend>Other</legend>
            <div class="form-group">
                <label for="western_union">Western Union:</label>
                <input type="number" id="western_union" name="western_union" value="0" min="0" required>
            </div>
        </fieldset>
        <div class="total">
        Total operations :
        <span id="total">0</span>
        </div>

        <button type="submit">
        save operations
        </button>

    </form>
    </div>
<script>
    const inputs = document.querySelectorAll('input[type="number"]');
    const total = document.getElementById("total");
    function calculateTotal() {
    let sum = 0;
    inputs.forEach(function(input) {
        sum += Number(input.value) || 0;
    });
    total.textContent = sum;
    }
    inputs.forEach(function(input) {

        input.addEventListener(
            "input",
            calculateTotal
        );

    });

    calculateTotal();

</script>    
</body>
</html>
