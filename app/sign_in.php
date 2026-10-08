<?php
session_start();
$host = "localhost";
$dbname = "poste_bd";
$username = "root";
$password = "";
# a pdo that communicates with database
try {

    $pdo= new PDO("mysql:host=$host;dbname=$dbname",$username,$password);

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
$error="";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

#preparing the sql query and selecting data
     $pre = $pdo->prepare(
        "SELECT *
         FROM admins
         WHERE username = :username"
    );
#executing the query
    $pre->execute([
        ":username" => $username
    ]);
#fetching the array 
        $admin = $pre->fetch(PDO::FETCH_ASSOC);

    if (
        $admin &&
        password_verify($password, $admin["password"])
    ){

        $_SESSION["admin"] = true;
        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_username"] = $admin["username"];

        header("Location: dashboard.php");
        exit();

    } else {

        $error = "Username or password incorrect.";

    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

<div class="login-container">
    <div class="logo">
        <h1>Poste Tunisienne</h1>
        <p>Espace Administration</p>
    </div>
    <?php if ($error !== ""): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
  
    <form method="post">
        <div class="form-group">
            <label for="username">
                username
            </label>
            <input
                type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">
                password
            </label>
            <input
                type="password" id="password" name="password" required>
        </div>
        <button type="submit">
            Sign in
        </button>
        <p>
            don't have account?
        <a href="register.php">Sign up</a>
        </p>
    
    </form>
</div>
</body>
</html>
