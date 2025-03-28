<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SkyWitness - UFO Sightings') ?></title>
    
    <!-- Pico CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    
    <!-- Phosphor ikony - opravený CDN -->
    <link
      rel="stylesheet"
      type="text/css"
      href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css"
    />
    
    <style>
        /* Vlastní úpravy */
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            gap: 1rem;
        }
        
        nav ul {
            margin-bottom: 0;
        }
        
        .theme-switcher {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .theme-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.25rem;
            color: var(--muted-color);
            padding: 0.5rem;
        }
        
        .theme-btn:hover {
            color: var(--color);
        }
        
        .ph {
            display: inline-block;
            vertical-align: middle;
        }
    </style>
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
            // Funkce pro změnu tématu
            function setTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
            }
            
            // Načtení uloženého tématu
            const savedTheme = localStorage.getItem('theme') || 'light';
            setTheme(savedTheme);
            
            // Přepínače
            document.getElementById('light-btn').addEventListener('click', () => setTheme('light'));
            document.getElementById('dark-btn').addEventListener('click', () => setTheme('dark'));
        </script>