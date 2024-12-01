<?php

$mysqli = require __DIR__ . "/database.php";

function redirectToSignup($message) {
    echo "<h1>Error</h1>";
    echo "<p>$message</p>";
    echo '<button onclick="window.location.href=\'signup.html\'">Go to Signup</button>';
    exit;
}

if (empty($_POST["name"])) {
    redirectToSignup("Name is required!");
}

if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    redirectToSignup("Valid Email is required");
}

if (strlen($_POST["password"]) < 8) {
    redirectToSignup("Password must be at least 8 characters");
}   

if (!preg_match("/[a-z]/i", $_POST["password"])) {
    redirectToSignup("Password must contain at least one letter");
}

if (!preg_match("/[0-9]/", $_POST["password"])) {
    redirectToSignup("Password must contain at least one number");
}

if ($_POST["password"] !== $_POST["confirm-password"]) {
    redirectToSignup("Passwords must match");
}

$password_hash = password_hash($_POST["password"], PASSWORD_DEFAULT);

$sql = "INSERT INTO users (fullname, email, password_hash, role)
        VALUES (?, ?, ?, ?)";

$stmt = $mysqli->stmt_init();

if (!$stmt->prepare($sql)) {
    redirectToSignup("SQL error: " . $mysqli->error);
}

$role = $_POST["role"]; 

$stmt->bind_param("ssss",
                  $_POST["name"],
                  $_POST["email"],
                  $password_hash,
                  $role);

try {
    $stmt->execute();
    header("Location: signup-success.html");
    exit;
} catch (mysqli_sql_exception) {
    if ($mysqli->errno === 1062) {
        redirectToSignup("Email already taken");
    } else {
        redirectToSignup("Database error.");
    }
}

?>