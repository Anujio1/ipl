<?php
// Include database connection
require 'db_connect.php';

// Fetch all channels from the database
try {
    $stmt = $pdo->query("SELECT * FROM channels");
    $channels = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fnflix - Channels</title>
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
            overflow-x: hidden;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes float {
            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes pulse {
            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
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

        /* Channel Grid */
        .channel-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
            padding: 20px;
            margin-top: 80px;
            /* Space for navbar */
            width: 100%;
            max-width: 1200px;
        }

        .channel-card {
            background: #1a1a1a;
            border-radius: 20px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeIn 0.5s ease-in-out;
            font-family: 'Trebuchet MS', sans-serif;
        }

        .channel-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(255, 255, 255, 0.1);
        }

        .channel-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .channel-card h3 {
            padding: 10px;
            text-align: center;
            margin: 0;
            font-size: 18px;
            color: #fff;
            font-family: 'Comic Sans MS', cursive, sans-serif;
            letter-spacing: 1px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .channel-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 10px;
                padding: 10px;
            }

            .channel-card img {
                height: 120px;
            }

            .channel-card h3 {
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

    <!-- Channel Grid -->
    <div class="channel-grid">
        <?php foreach ($channels as $channel): ?>
        <a href="player.php?code=<?= $channel['code'] ?>" class="channel-card">
            <img src="<?= $channel['icon'] ?>" alt="<?= $channel['name'] ?>">
            <h3><?= $channel['name'] ?></h3>
        </a>
        <?php endforeach; ?>
    </div>

    <!-- JavaScript -->
    <script>
        // Show Popup on Page Load
        document.addEventListener('DOMContentLoaded', function () {
            const popup = document.getElementById('popup');
            const alreadyJoinedButton = document.getElementById('alreadyJoined');

            // Show the popup when the page loads
            popup.style.display = 'flex';

            // Hide the popup when "Already Joined" is clicked
            alreadyJoinedButton.addEventListener('click', function () {
                popup.style.display = 'none';
            });
        });
    </script>
</body>

</html>
