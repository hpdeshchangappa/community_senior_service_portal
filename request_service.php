<?php
session_start();
$conn = new mysqli("localhost","root","root123","senior_portal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ----------------------
// CLASSIFICATION LOGIC
// ----------------------
function classifyRequest($text){
    $text = strtolower($text);

    if(strpos($text, "water") !== false) return "Water Supply";
    if(strpos($text, "electric") !== false || strpos($text, "power") !== false) return "Electricity";
    if(strpos($text, "garbage") !== false || strpos($text, "waste") !== false) return "Sanitation";
    if(strpos($text, "pension") !== false) return "Pension";

    return "General";
}

// ----------------------
// PRIORITY DETECTION
// ----------------------
function getPriority($text){
    $text = strtolower($text);

    if(strpos($text, "urgent") !== false || strpos($text, "emergency") !== false){
        return "High";
    }
    return "Normal";
}

// ----------------------
// FORM SUBMIT
// ----------------------
if(isset($_POST['submit'])){

    $description = $conn->real_escape_string($_POST['description']);

    $request_type = classifyRequest($description);
    $priority = getPriority($description);

    $sql = "INSERT INTO requests 
            (user_id, request_type, description, priority_level, status, created_at)
            VALUES 
            ('$user_id', '$request_type', '$description', '$priority', 'Pending', NOW())";

    if($conn->query($sql)){

        // 🔔 Insert notification
        $message = "Your $request_type request has been submitted successfully";
        $status = "Pending";

        $notif_sql = "INSERT INTO notifications (user_id, message, status, created_at)
                      VALUES ('$user_id', '$message', '$status', NOW())";

        $conn->query($notif_sql);

        $success = "Request submitted successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Public Service Request</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<script>
function startVoice(){
    var recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = "en-IN";

    recognition.onresult = function(event){
        document.getElementById("description").value = event.results[0][0].transcript;
    };

    recognition.start();
}
</script>

<style>
body {
    background-color: #f8f9fa;
}

.card {
    border-radius: 15px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>

</head>

<body>

<!-- ✅ NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
<div class="container">

<a class="navbar-brand" href="dashboard.php">Senior Support Portal</a>

<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="menu">
<ul class="navbar-nav ms-auto">

<li class="nav-item"><a class="nav-link" href="dashboard.php">Home</a></li>
<li class="nav-item"><a class="nav-link active" href="request_service.php">Services</a></li>
<li class="nav-item"><a class="nav-link" href="medical_help.php">Medical Help</a></li>
<li class="nav-item"><a class="nav-link" href="track_request.php">Track Request</a></li>
<li class="nav-item"><a class="nav-link" href="notifications.php">Notifications</a></li>
<li class="nav-item"><a class="nav-link text-warning fw-bold" href="logout.php">Logout</a></li>

</ul>
</div>

</div>
</nav>

<!-- MAIN CONTENT -->
<div class="container mt-5">

<h3 class="text-center mb-4">Public Service Request</h3>

<?php
if(isset($success)){
    echo "<div class='alert alert-success text-center'>$success</div>";
}
?>

<div class="card p-4">

<form method="POST">

    <label class="form-label">Describe Your Issue</label>

    <textarea id="description" name="description" class="form-control mb-3"
        placeholder="Speak or type your issue (water, electricity, pension...)" required></textarea>

    <div class="d-flex gap-2">
        <button type="button" onclick="startVoice()" class="btn btn-warning">🎤 Speak</button>
        <button type="submit" name="submit" class="btn btn-success">Submit</button>
    </div>

</form>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>