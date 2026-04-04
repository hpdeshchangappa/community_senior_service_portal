<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Senior Support Portal</title>

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background-color:#f8f9fa;
}

.hero{
padding:80px 20px;
text-align:center;
}

.card{
border-radius:15px;
}

</style>

</head>

<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
<div class="container">

<a class="navbar-brand" href="#">Senior Support Portal</a>

<button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#menu">
<span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="menu">

<ul class="navbar-nav ms-auto">

<li class="nav-item">
<a class="nav-link active" href="index.php">Home</a>
</li>

<li class="nav-item">
<a class="nav-link" href="login.php">Login</a>
</li>

<li class="nav-item">
<a class="nav-link" href="register.php">Register</a>
</li>

</ul>

</div>
</div>
</nav>

<!-- Hero Section -->
<div class="container hero">

<h1 class="mb-3">Welcome to Senior Support Portal</h1>

<p class="lead">
A platform designed to help senior citizens easily request public services and medical assistance.
</p>

<a href="login.php" class="btn btn-primary btn-lg m-2">Login</a>
<a href="register.php" class="btn btn-success btn-lg m-2">Register</a>

</div>

<!-- Services Section -->
<div class="container mb-5">

<h2 class="text-center mb-4">Our Services</h2>

<div class="row g-4">

<div class="col-md-4">
<div class="card shadow text-center p-4">

<h4>Public Service Requests</h4>

<p>
Request assistance for water supply, electricity issues, garbage collection and more.
</p>

</div>
</div>

<div class="col-md-4">
<div class="card shadow text-center p-4">

<h4>Medical Assistance</h4>

<p>
Senior citizens can request emergency medical help or healthcare support.
</p>

</div>
</div>

<div class="col-md-4">
<div class="card shadow text-center p-4">

<h4>Track Requests</h4>

<p>
Users can track the status of submitted service and medical requests.
</p>

</div>
</div>

</div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white text-center p-3">

<p class="mb-0">
© 2026 Senior Support Portal
</p>

</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>