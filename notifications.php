<?php
session_start();

// Database connection
$conn = new mysqli("localhost", "root", "root123", "senior_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch ALL notifications (not just 5)
$sql = "SELECT * FROM notifications 
        WHERE user_id='$user_id' 
        ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notifications</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color:#f8f9fa; }

.card{
    border-radius:15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

</style>
</head>

<body>

<!-- Navbar (SAME AS DASHBOARD) -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
<div class="container">
<a class="navbar-brand" href="dashboard.php">Senior Support Portal</a>

<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="menu">
<ul class="navbar-nav ms-auto">
<li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
<li class="nav-item"><a class="nav-link" href="request_service.php">Services</a></li>
<li class="nav-item"><a class="nav-link" href="medical_help.php">Medical Help</a></li>
<li class="nav-item"><a class="nav-link" href="track_request.php">Track Request</a></li>
<li class="nav-item"><a class="nav-link active" href="notifications.php">Notifications</a></li>
<li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</nav>

<!-- Main Content -->
<div class="container mt-5">

<h2 class="text-center mb-4">Your Notifications</h2>

<div class="card p-4">

<?php
if ($result->num_rows > 0) {

    echo "<table class='table table-bordered table-hover'>";
    echo "<thead class='table-light'>
            <tr>
                <th>Message</th>
                <th>Status</th>
                <th>Date</th>
            </tr>
          </thead><tbody>";

    while ($row = $result->fetch_assoc()) {

        // Status colors
        $statusColor = "";
        if ($row['status'] == "Pending") $statusColor = "text-warning";
        elseif ($row['status'] == "Approved") $statusColor = "text-success";
        elseif ($row['status'] == "Completed") $statusColor = "text-primary";
        elseif ($row['status'] == "unread") $statusColor = "text-danger";

        echo "<tr>
                <td>" . htmlspecialchars($row['message']) . "</td>
                <td class='$statusColor fw-bold'>" . $row['status'] . "</td>
                <td>" . $row['created_at'] . "</td>
              </tr>";
    }

    echo "</tbody></table>";

} else {
    echo "<p class='text-center'>No notifications yet.</p>";
}
?>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>