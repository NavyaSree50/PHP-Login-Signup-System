<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard | AUTH</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>! 👋</h2>

        <p class="subtitle">
            You have successfully logged in.
        </p>

        <div class="dashboard">

            <h3>Account Dashboard</h3>

            <p>
                Your authentication is working successfully.
            </p>
            <a href="user-account.php" >
                 View My Account </a>

            <a href="logout.php" class="logout-btn">
                Logout
            </a>

        </div>

    </div>

</body>

</html>