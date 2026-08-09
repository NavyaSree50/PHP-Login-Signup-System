<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "dbconnect.php";

$userID = $_SESSION["user_id"];

$sql = "SELECT ID, name, email, created_at
        FROM users
        WHERE ID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
} else {
    echo "User not found.";
    exit();
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Account | AUTH</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h2>My Account 👤</h2>

        <p class="subtitle">
            Your account information
        </p>

        <div class="dashboard">

            <p>
                <strong>Name</strong><br>
                <?php echo htmlspecialchars($user["name"]); ?>
            </p>

            <p>
                <strong>Email</strong><br>
                <?php echo htmlspecialchars($user["email"]); ?>
            </p>

            <p>
                <strong>User ID</strong><br>
                <?php echo htmlspecialchars($user["ID"]); ?>
            </p>

            <p>
                <strong>Account Created</strong><br>
                <?php echo htmlspecialchars($user["created_at"]); ?>
            </p>

        </div>
        <p>
            <a href="change-password.php"> 🔑 Change Password</a>
        </p>

        <p>
            <a href="edit-account.php">✏️ Edit Account</a>
        </p>

        <p>
            <a href="home.php">← Back to Dashboard</a>
        </p>

        <a href="logout.php" class="logout-btn">
            Logout
        </a>

    </div>

</body>

</html>