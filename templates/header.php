<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SkyWitness - UFO Sightings') ?></title>
    
    <link rel="stylesheet" href="/assets/css/main.css">
    <link rel="stylesheet" href="/assets/css/sightings.css">
    <link rel="stylesheet" href="/assets/css/theme.css">
    
    <!-- Pico CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.colors.min.css"> 
    
    <!-- Phosphor ikony - opravený CDN -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css"/>
     
</head>
<body>
    <main class="container">
        <header>
            <h1 style="margin-bottom: 0;">
                <a href="/" style="text-decoration: none;">
                    <i class="ph ph-alien"></i> SkyWitness
                </a>
            </h1>
            
            <nav>
                <ul>
                    <li><a href="/"><i class="ph ph-house"></i> Home</a></li>
                    <li><a href="/sightings.php"><i class="ph ph-binoculars"></i> Sightings</a></li>
                    <li><a href="/xml-operations.php"><i class="ph ph-code"></i> XML Tools</a></li>
                </ul>
            </nav>
            
            <div class="theme-switcher">
                <button class="theme-btn" id="light-btn" title="Light mode">
                    <i class="ph ph-sun"></i>
                </button>
                <button class="theme-btn" id="dark-btn" title="Dark mode">
                    <i class="ph ph-moon"></i>
                </button>
            </div>
        </header>

        <script>
            function setTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
            }
            
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
            
            document.getElementById('light-btn').addEventListener('click', () => setTheme('light'));
            document.getElementById('dark-btn').addEventListener('click', () => setTheme('dark'));
        </script>
