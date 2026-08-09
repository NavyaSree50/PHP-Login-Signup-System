<?php

include "dbconnect.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    // Check password confirmation
    if ($password !== $confirmPassword) {

        $message = "Passwords do not match.";
        $messageType = "error";

    } elseif (strlen($password) < 8) {

        $message = "Password must be at least 8 characters.";
        $messageType = "error";

    } else {

        // Check whether email already exists
        $checkSql = "SELECT ID FROM users WHERE email = ?";

        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();

        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {

            $message = "Email already registered. Please login.";
            $messageType = "error";

        } else {

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (name, email, password,created_at)
                    VALUES (?, ?, ?,CURDATE())";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {

               header("Location:index.php? registered=1");
               exit();

            } else {

                $message = "Something went wrong. Please try again.";
                $messageType = "error";
            }

            $stmt->close();
        }

        $checkStmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign Up | AUTH</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h2>Create Account ✨</h2>

        <p class="subtitle">
            Sign up to get started
        </p>

        <?php if ($message != ""): ?>

            <p class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <label for="name">Name</label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter your name"
                required
            >

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
                    placeholder="Create a password"
                    required
                    minlength="8"
                    oninput="checkPasswordStrength()"
                >

                <button
                    type="button"
                    class="show-password"
                    onclick="togglePassword()"
                >
                    Show
                </button>

            </div>

            <p id="password-strength"></p>

            <label for="confirm_password">Confirm Password</label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Re-enter your password"
                required
                minlength="8"
            >

            <button type="submit">
                Create Account
            </button>

        </form>

        <p class="signup-link">

            Already have an account?

            <a href="index.php">
                Login
            </a>

        </p>

    </div>

    <script src="script.js"></script>

</body>

</html>