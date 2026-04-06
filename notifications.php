<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "", "senior_portal");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check login
if (!isset($_SESSION['user_id'])) {
    echo "Please login first!";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch notifications
$sql = "SELECT * FROM notifications 
        WHERE user_id = '$user_id' 
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f6f9;
            padding: 20px;
        }
        .container {
            width: 60%;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        .notification {
            padding: 12px;
            margin-bottom: 10px;
            border-left: 5px solid #007bff;
            background: #f9f9f9;
            border-radius: 5px;
        }
        .unread {
            border-left-color: red;
            background: #fff3f3;
        }
        .time {
            font-size: 12px;
            color: gray;
        }
        .status {
            font-size: 12px;
            font-weight: bold;
        }
        h2 {
            text-align: center;
        }
        .empty {
            text-align: center;
            color: gray;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Your Notifications</h2>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $isUnread = ($row['status'] == 'unread') ? 'unread' : '';

            echo "<div class='notification $isUnread'>";
            echo "<p>" . htmlspecialchars($row['message']) . "</p>";
            echo "<div class='status'>Status: " . $row['status'] . "</div>";
            echo "<div class='time'>" . $row['created_at'] . "</div>";
            echo "</div>";
        }
    } else {
        echo "<p class='empty'>No notifications yet.</p>";
    }
    ?>

</div>

</body>
</html>

<?php
$conn->close();
?>