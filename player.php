<?php
// Include database connection
require 'db_connect.php';

// Get the channel code from the URL
$code = $_GET['code'] ?? '';

if (empty($code)) {
    die("Channel code is missing!");
}

// Fetch channel details from the database
try {
    $stmt = $pdo->prepare("SELECT * FROM channels WHERE code = ?");
    $stmt->execute([$code]);
    $channel = $stmt->fetch();

    if (!$channel) {
        die("Channel not found!");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($channel['name']) ?> - Player</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styles */
        body {
            background-color: #000;
            color: #fff;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            background-color: rgba(0, 0, 0, 0.8);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .logo img {
            height: 50px;
        }

        .join-telegram {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            background-color: #0088cc;
            color: #fff;
            text-decoration: none;
            border-radius: 30px;
            font-size: 16px;
            transition: all 0.3s ease;
            margin-left: 20px;
        }

        .join-telegram:hover {
            background-color: #006699;
            transform: scale(1.1);
        }

        .join-telegram i {
            margin-right: 10px;
        }

        /* Popup Styles */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            justify-content: center;
            align-items: center;
            z-index: 1001;
        }

        .popup-content {
            background-color: rgba(27, 21, 21, 0.49);
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .popup-content h2 {
            margin-bottom: 20px;
            font-size: 18px;
            color: #fdfdfd;
        }

        .join-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0088cc;
            color: #fff;
            text-decoration: none;
            border-radius: 30px;
            font-size: 16px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .join-button:hover {
            background-color: #006699;
            transform: scale(1.1);
        }

        .already-joined {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .already-joined:hover {
            background-color: #45a049;
            transform: scale(1.1);
        }

        /* Player Container */
        #player-container {
            width: 100%;
            max-width: 1200px;
            height: 70vh;
            margin-top: 80px; /* Space for navbar */
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.1);
        }

        /* Quality Selector */
        .quality-selector {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1002;
            background: rgba(0, 0, 0, 0.8);
            padding: 15px;
            border-radius: 10px;
            display: none;
        }

        .quality-option {
            color: white;
            padding: 8px 15px;
            margin: 5px 0;
            cursor: pointer;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .quality-option:hover {
            background: #0088cc;
        }

        .quality-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1002;
            background: #0088cc;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            #player-container {
                height: 50vh;
                margin-top: 60px;
            }

            .popup-content {
                width: 90%;
                padding: 15px;
            }

            .popup-content h2 {
                font-size: 16px;
            }

            .join-button, .already-joined {
                padding: 8px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Popup Ticket -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <div class="logo">
                <img src="logo.png" alt="Logo">
            </div>
            <h2>To access this website, please join our official Telegram channel.</h2>
            <a href="https://t.me/fn_network_back" target="_blank" class="join-button">
                <i class="fa-brands fa-telegram"></i> Join Telegram Channel
            </a>
            <button id="alreadyJoined" class="already-joined">Already Joined</button>
        </div>
    </div>

    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="logo">
            <img src="logo.png" alt="Logo">
        </div>
        <a href="https://t.me/fn_network_back" target="_blank" class="join-telegram">
            <i class="fa-brands fa-telegram"></i> Join Telegram Channel
        </a>
    </nav>

    <!-- Quality Selector -->
    <div class="quality-toggle" onclick="toggleQualityMenu()">
        <i class="fas fa-cog"></i>
        Quality
    </div>
    
    <div class="quality-selector" id="qualityMenu">
        <div class="quality-option" onclick="changeQuality('auto')">Auto</div>
        <div class="quality-option" onclick="changeQuality('1080')">1080p</div>
        <div class="quality-option" onclick="changeQuality('720')">720p</div>
        <div class="quality-option" onclick="changeQuality('480')">480p</div>
        <div class="quality-option" onclick="changeQuality('360')">360p</div>
    </div>

    <!-- Video Player -->
    <div id="player-container"></div>

    <!-- JW Player Script -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script src="https://content.jwplatform.com/libraries/SAHhwvZq.js"></script>
    <script>
        // Initialize JW Player
        const player = jwplayer("player-container").setup({
            sources: [{
                file: "<?= $channel['link'] ?>",
                label: "Auto"
            }, {
                file: "<?= $channel['link'] ?>_1080",
                label: "1080p"
            }, {
                file: "<?= $channel['link'] ?>_720",
                label: "720p"
            }, {
                file: "<?= $channel['link'] ?>_480",
                label: "480p"
            }, {
                file: "<?= $channel['link'] ?>_360",
                label: "360p"
            }],
            width: "100%",
            height: "100%",
            autostart: true,
            primary: "html5",
            defaultQuality: 'auto',
            qualityLabels: {
                1080: "1080p",
                720: "720p",
                480: "480p",
                360: "360p"
            }
        });

        // Quality control functions
        function toggleQualityMenu() {
            const menu = document.getElementById('qualityMenu');
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }

        function changeQuality(quality) {
            player.setCurrentQuality(quality === 'auto' ? -1 : quality);
            document.getElementById('qualityMenu').style.display = 'none';
        }

        // Update quality indicator
        player.on('levels', (levels) => {
            const currentLevel = player.getCurrentQuality();
            document.querySelector('.quality-toggle span').textContent = 
                currentLevel === -1 ? 'Auto' : levels[currentLevel].label;
        });

        // Show Popup on Page Load
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.getElementById('popup');
            const alreadyJoinedButton = document.getElementById('alreadyJoined');

            // Show the popup when the page loads
            popup.style.display = 'flex';

            // Hide the popup when "Already Joined" is clicked
            alreadyJoinedButton.addEventListener('click', function() {
                popup.style.display = 'none';
            });
        });
    </script>
</body>
</html>
