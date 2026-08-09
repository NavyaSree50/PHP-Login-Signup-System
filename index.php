<?php

session_start();
include "dbconnect.php";

$message = "";
if (isset($_GET["registered"])) {
    $message = "Account created successfully! Please login.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["ID"];
            $_SESSION["user_name"] = $user["name"];

            header("Location: home.php");
            exit();

        } else {
            $message = "Invalid password.";
        }

    } else {
        $message = "Account not found.";
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | AUTH</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h2>Welcome Back 👋</h2>

        <p class="subtitle">Login to your account</p>

        <?php if ($message != ""): ?>

            <p class="message">
                <?php echo $message; ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                placeholder="Enter your email"
                required
            >

            <label for="password">Password</label>

            <div class="password-box">

    <input
        type="password"
        id="password"
        name="password"
        placeholder="Enter your password"
        required
    >

    <button type="button" class="show-password" onclick="togglePassword()">
        Show
    </button>

</div>

            <button type="submit">
                Login
            </button>

        </form>

        <p class="signup-link">
            Don't have an account?
            <a href="register.php">Create Account</a>
        </p>

    </div>
    <script src="script.js"></script>

</body>

</html>