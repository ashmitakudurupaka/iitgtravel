<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    
     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


    <!-- Custom Style CSS -->
    <link rel="stylesheet" type="text/css" href="../css/style.css" >
    <title>IITG Travel guide</title>
  </head>

  <body>
     <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="../index.php">IITG Travel Guide</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="dashboard.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="current_trip.php">Current trip</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="past_trips.php">Planned trips</a>
         <li class="nav-item">
            <a class="nav-link" href="plan_trip.php">Plan a trip</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="edit_profile.php">edit profile</a>
          </li>
        </ul>

        <div class="d-flex">
          <a href="../index.php" class="btn btn-primary">Log out</a>
        </div>
      </div>
    </div>
  </nav>