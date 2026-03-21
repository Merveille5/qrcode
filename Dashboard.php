<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Dashboard Présence QR</title>

<!-- AdminLTE -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
<ul class="navbar-nav">
<li class="nav-item">
<a class="nav-link" data-widget="pushmenu" href="#">
<i class="fas fa-bars"></i>
</a>
</li>
</ul>
</nav>

<!-- Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">

<a href="#" class="brand-link">
<span class="brand-text font-weight-light">QR Presence</span>
</a>

<div class="sidebar">
<nav class="mt-2">
<ul class="nav nav-pills nav-sidebar flex-column">

<li class="nav-item">
<a href="#" class="nav-link active">
<i class="nav-icon fas fa-chart-line"></i>
<p>Dashboard</p>
</a>
</li>

<li class="nav-item">
<a href="#" class="nav-link">
<i class="nav-icon fas fa-users"></i>
<p>Employés</p>
</a>
</li>

<li class="nav-item">
<a href="#" class="nav-link">
<i class="nav-icon fas fa-clock"></i>
<p>Présences</p>
</a>
</li>

</ul>
</nav>
</div>
</aside>

<!-- Content -->
<div class="content-wrapper">

<section class="content">
<div class="container-fluid">

<!-- KPI Cards -->
<div class="row">

<div class="col-lg-3 col-6">
<div class="small-box bg-info">
<div class="inner">
<h3>150</h3>
<p>Employés</p>
</div>
<div class="icon">
<i class="fas fa-users"></i>
</div>
</div>
</div>

<div class="col-lg-3 col-6">
<div class="small-box bg-success">
<div class="inner">
<h3>120</h3>
<p>Présents</p>
</div>
<div class="icon">
<i class="fas fa-user-check"></i>
</div>
</div>
</div>

<div class="col-lg-3 col-6">
<div class="small-box bg-danger">
<div class="inner">
<h3>20</h3>
<p>Absents</p>
</div>
<div class="icon">
<i class="fas fa-user-times"></i>
</div>
</div>
</div>

<div class="col-lg-3 col-6">
<div class="small-box bg-warning">
<div class="inner">
<h3>10</h3>
<p>Retards</p>
</div>
<div class="icon">
<i class="fas fa-clock"></i>
</div>
</div>
</div>

</div>

<!-- Charts -->
<div class="row">

<!-- Bar chart -->
<div class="col-md-6">
<div class="card">
<div class="card-header">
<h3 class="card-title">Présence par département</h3>
</div>

<div class="card-body">
<canvas id="deptChart"></canvas>
</div>

</div>
</div>

<!-- Pie chart -->
<div class="col-md-6">
<div class="card">
<div class="card-header">
<h3 class="card-title">Statut de présence</h3>
</div>

<div class="card-body">
<canvas id="presenceChart"></canvas>
</div>

</div>
</div>

</div>

<!-- Line chart -->
<div class="row">
<div class="col-md-12">

<div class="card">
<div class="card-header">
<h3 class="card-title">Évolution des présences</h3>
</div>

<div class="card-body">
<canvas id="evolutionChart"></canvas>
</div>

</div>

</div>
</div>

</div>
</section>
</div>

</div>

<!-- AdminLTE JS -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>

/* Graphique Département */

new Chart(document.getElementById("deptChart"), {

type: "bar",

data: {
labels: ["IT","RH","Finance","Marketing"],

datasets: [{
label: "Présence",
data: [40,20,30,25]
}]
}

})

/* Pie chart */

new Chart(document.getElementById("presenceChart"), {

type: "pie",

data: {
labels: ["Présents","Absents","Retards"],

datasets: [{
data: [120,20,10]
}]
}

})

/* Line chart */

new Chart(document.getElementById("evolutionChart"), {

type: "line",

data: {

labels: ["Lundi","Mardi","Mercredi","Jeudi","Vendredi"],

datasets: [{

label: "Présence",

data: [90,85,88,92,80],

fill: false

}]

}

})

</script>

</body>
</html>