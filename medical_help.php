<?php
session_start();

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// DB connection
$conn = new mysqli("localhost", "root", "root123", "senior_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION['user_id'];
    $symptoms = $conn->real_escape_string($_POST['symptoms']);
    $severity = $_POST['severity'];

    // Convert severity → priority score
    if ($severity == "High") {
        $priority = 3;
    } elseif ($severity == "Medium") {
        $priority = 2;
    } else {
        $priority = 1;
    }

    // Insert into requests table
    $sql1 = "INSERT INTO requests (user_id, request_type, description, status, created_at)
             VALUES ('$user_id', 'Medical', '$symptoms', 'Pending', NOW())";

    if ($conn->query($sql1) === TRUE) {

        $request_id = $conn->insert_id;

        // Insert into medical_requests table
        $sql2 = "INSERT INTO medical_requests (request_id, symptoms, priority_score, doctor_assigned)
                 VALUES ('$request_id', '$symptoms', '$priority', NULL)";

        if ($conn->query($sql2) === TRUE) {

            // 🔔 INSERT NOTIFICATION
            $message = "Your medical request has been submitted successfully";
            $status = "Pending";

            $notif_sql = "INSERT INTO notifications (user_id, message, status, created_at)
                          VALUES ('$user_id', '$message', '$status', NOW())";

            $conn->query($notif_sql);

            $success_msg = "Medical request submitted successfully!";
        } else {
            $error_msg = "Error (medical_requests): " . $conn->error;
        }

    } else {
        $error_msg = "Error (requests): " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Medical Assistance</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body { background-color:#f4f6f9; }

.card{
    border-radius:15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.mic-btn {
    font-size: 20px;
}
</style>
</head>

<body>

<!-- ✅ NAVBAR (UPDATED) -->
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
<li class="nav-item"><a class="nav-link active" href="medical_help.php">Medical Help</a></li>
<li class="nav-item"><a class="nav-link" href="track_request.php">Track Request</a></li>
<li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
<li class="nav-item"><a class="nav-link text-warning fw-bold" href="logout.php">Logout</a></li>
</ul>
</div>
</div>
</nav>

<div class="container mt-5">

<h2 class="text-center mb-3">Medical Assistance</h2>
<h5 class="text-center mb-4">Welcome, <?= $_SESSION['name'] ?></h5>

<!-- Messages -->
<?php if (isset($success_msg)) { ?>
<div class="alert alert-success"><?= $success_msg ?></div>
<?php } ?>

<?php if (isset($error_msg)) { ?>
<div class="alert alert-danger"><?= $error_msg ?></div>
<?php } ?>

<!-- Info -->
<div class="card p-4 mb-4">
<p>
This system allows senior citizens to request medical assistance easily. 
You can either type or use voice input. Machine Learning prioritizes requests, 
while final decisions are taken by a department officer.
</p>
</div>

<!-- Form -->
<div class="card p-4">
<h5 class="mb-3">Submit Medical Request</h5>

<form method="POST">

<!-- Voice-enabled textarea -->
<div class="mb-3">
<label class="form-label">Symptoms / Issue</label>

<div class="input-group">
<textarea id="symptoms" name="symptoms" class="form-control" rows="3" required></textarea>

<button type="button" class="btn btn-secondary mic-btn" onclick="startVoice()">
🎤
</button>
</div>

<small class="text-muted">Click the mic and speak your symptoms</small>
</div>

<!-- Severity -->
<div class="mb-3">
<label class="form-label">Severity</label>
<select name="severity" class="form-select">
<option value="Low">Low</option>
<option value="Medium">Medium</option>
<option value="High">High</option>
</select>
</div>

<button type="submit" class="btn btn-danger w-100">
Submit Request
</button>

</form>
</div>

</div>

<!-- Voice Script -->
<script>
function startVoice() {

    if (!('webkitSpeechRecognition' in window)) {
        alert("Voice input not supported in this browser");
        return;
    }

    var recognition = new webkitSpeechRecognition();
    recognition.lang = "en-IN";
    recognition.start();

    recognition.onresult = function(event) {
        document.getElementById("symptoms").value = event.results[0][0].transcript;
    };

    recognition.onerror = function() {
        alert("Voice recognition error");
    };
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>