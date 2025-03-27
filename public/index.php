<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SkyWitness - UFO Sightings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f0f8ff;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1e90ff;
            text-align: center;
        }
        .status {
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
            background: #e6f7ff;
            border-left: 4px solid #1e90ff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to SkyWitness</h1>
        <p>Your UFO sightings database</p>
        
        <div class="status">
            <?php
            try {
                $db = new PDO('pgsql:host=db;dbname=skywitness', 'postgres', 'postgres');
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                echo "Database connection successful! 🚀";
            } catch(PDOException $e) {
                echo "Database connection failed: " . $e->getMessage();
            }
            ?>
        </div>
        
        <h2>Hello World from SkyWitness!</h2>
        <p>This is a basic setup for your project. Next steps:</p>
        <ul>
            <li>Create XML data files</li>
            <li>Implement PHP classes</li>
            <li>Add database functionality</li>
            <li>Create user interface</li>
        </ul>
    </div>
</body>
</html>