<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SkyWitness - UFO Sightings') ?></title>

    <link rel="icon" href="/assets/images/flying-saucer.svg" type="image/svg+xml">

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Open Props Core -->
    <link rel="stylesheet" href="https://unpkg.com/open-props"/>
    
    <!-- Optional imports based on your needs -->
    <link rel="stylesheet" href="https://unpkg.com/open-props/normalize.min.css"/>
    <link rel="stylesheet" href="https://unpkg.com/open-props/buttons.min.css"/>
    
    <!-- Phosphor icons -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css"/>
    
    <!-- Your custom styles -->
    <link rel="stylesheet" href="/assets/css/home-stats.css">
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/add-sighting.css">
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/sightings.css">
    <link rel="stylesheet" href="/assets/css/sighting-detail.css">
</head>
<body>
    <main class="container">
        <header>
            <h1>
                <a href="/">
                    <i class="ph ph-flying-saucer"></i> SkyWitness
                </a>
            </h1>
            
            <nav>
                <ul>
                    <li><a href="/"><i class="ph ph-house"></i> Home</a></li>
                    <li><a href="/sightings.php"><i class="ph ph-binoculars"></i> Sightings</a></li>
                    <li><a href="/add_sighting.php"><i class="ph ph-code"></i> Add a Sighting</a></li>
                </ul>
            </nav>
            
            <div class="theme-switcher">
                <button class="theme-btn" id="theme-toggle" title="Switch theme">
                    <i class="ph ph-sun" id="theme-icon"></i>
                </button>
            </div>
        </header>

        <script src="/assets/js/theme.js"></script>