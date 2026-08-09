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

// Get current user details
$sql = "SELECT name, email FROM users WHERE ID = ?";

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


// Handle update
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);

    if ($name == "" || $email == "") {

        $message = "Name and email cannot be empty.";
        $messageType = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $messageType = "error";

    } else {

        // Check whether email belongs to another account
        $checkSql = "SELECT ID FROM users WHERE email = ? AND ID != ?";

        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("si", $email, $userID);
        $checkStmt->execute();

        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {

            $message = "This email is already registered.";
            $messageType = "error";

        } else {

            // Update user
            $updateSql = "UPDATE users
                          SET name = ?, email = ?
                          WHERE ID = ?";

            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ssi", $name, $email, $userID);

            if ($updateStmt->execute()) {

                // Update session name
                $_SESSION["user_name"] = $name;

                $message = "Account updated successfully!";
                $messageType = "success";

                // Update displayed values
                $user["name"] = $name;
                $user["email"] = $email;

            } else {

                $message = "Something went wrong. Please try again.";
                $messageType = "error";
            }

            $updateStmt->close();
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

    <title>Edit Account | AUTH</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

    <div class="container">

        <h2>Edit Account ✏️</h2>

        <p class="subtitle">
            Update your account information
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
                value="<?php echo htmlspecialchars($user["name"]); ?>"
                required
            >

            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?php echo htmlspecialchars($user["email"]); ?>"
                required
            >

            <button type="submit">
                Save Changes
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