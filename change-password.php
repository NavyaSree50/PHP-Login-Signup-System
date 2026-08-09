<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

include "dbconnect.php";

$userID = $_SESSION["user_id"];

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    // Get current password from database
    $sql = "SELECT password FROM users WHERE ID = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {

        $user = $result->fetch_assoc();

        // Check current password
        if (!password_verify($currentPassword, $user["password"])) {

            $message = "Current password is incorrect.";
            $messageType = "error";

        } elseif (strlen($newPassword) < 8) {

            $message = "New password must be at least 8 characters.";
            $messageType = "error";

        } elseif ($newPassword !== $confirmPassword) {

            $message = "New passwords do not match.";
            $messageType = "error";

        } elseif ($currentPassword === $newPassword) {

            $message = "New password must be different from your current password.";
            $messageType = "error";

        } else {

            // Hash the new password
            $hashedPassword = password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );

            // Update password
            $updateSql = "UPDATE users
                          SET password = ?
                          WHERE ID = ?";

            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param(
                "si",
                $hashedPassword,
                $userID
            );

            if ($updateStmt->execute()) {

                $message = "Password changed successfully!";
                $messageType = "success";

            } else {

                $message = "Something went wrong. Please try again.";
                $messageType = "error";
            }

            $updateStmt->close();
        }
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password | AUTH</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h2>Change Password 🔑</h2>

        <p class="subtitle">
            Update your account password
        </p>

        <?php if ($message != ""): ?>

            <p class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php endif; ?>

        <form method="POST">

            <label for="current_password">
                Current Password
            </label>

            <input
                type="password"
                id="current_password"
                name="current_password"
                placeholder="Enter current password"
                required
            >

            <label for="new_password">
                New Password
            </label>

            <input
                type="password"
                id="new_password"
                name="new_password"
                placeholder="Enter new password"
                minlength="8"
                required
            >

            <label for="confirm_password">
                Confirm New Password
            </label>

            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                placeholder="Confirm new password"
                minlength="8"
                required
            >

            <button type="submit">
                Change Password
            </button>

        </form>

        <p>
            <a href="user-account.php">
                ← Back to My Account
            </a>
        </p>

    </div>

</body>

</html>