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
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";
    #checking if account exists
    $pre = $pdo->prepare(
        "SELECT *
         FROM admins
         WHERE email = :email"
    );
    #executing the query
    $pre->execute([
        ":email" => $email
    ]);
    #fetching the array 
        $admin = $pre->fetch(PDO::FETCH_ASSOC);
    #checking name,email and password
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {

        $error = "Please fill in all fields.";

    }elseif ($password !== $confirm_password) {
        $error=" verify password"; 
    }elseif (
        $admin &&
        password_verify($password, $admin["password"])
    ){  $error="you already have account";
    }
     else {
#hashing password
        $hashedpassword = password_hash($password,PASSWORD_DEFAULT);

#preparing the sql query and inserting data
            $sql = "INSERT INTO admins
                (username, email, password)
                VALUES
                (:username, :email, :password)";
            $pre = $pdo->prepare($sql);
#executing the query
            $pre->execute([
                 ":username" => $username,
                ":email" => $email,
                ":password" => $hashedpassword
            ]);
            $success = "Registration successful!";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>register un compte</title>
</head>
<body>
    <div class="login-container">
        <h1>Creer un compte</h1>
<!--in case of error this div appears -->        
        <?php if ($error !== ""): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
    
<!-- in case of success this div appears -->
    <?php if ($success !== ""): ?>

    <div class="success">
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
        <label>username</label>
        <input type="text" name="username" required>
        </div>

        <div class="form-group">
        <label>email</label>
        <input type="email" name="email" required>
        </div>

        <div class="form-group">
        <label>password</label>
        <input type="password" name="password" required>
        </div>

        <div class="form-group">
        <label>confirm password</label>
        <input type="password" name="confirm_password" required>
        </div>

        <button type="submit">
            create account
        </button>

        <div class="login">
        already have account ?
        <a href="sign_in.php">
            sign in
        </a>
        </div>
    </form>
    </div>
</body>
</html>