<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'SkyWitness - UFO Sightings'; ?></title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
        .xml-output {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
            max-height: 300px;
            overflow-y: auto;
        }
        .sighting-card {
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .sighting-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-ufo me-2"></i>SkyWitness
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/sightings.php">Sightings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/xml-operations.php">XML Operations</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/test-db.php">DB Test</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">