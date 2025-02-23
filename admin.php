<?php
// Start session for authentication
session_start();

// Hardcoded admin credentials (replace with secure storage in production)
$admin_username = 'fn';
$admin_password = 'fn';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['loggedin'] = true;
    } else {
        $error = "Invalid credentials!";
    }
}

// Redirect to login if not authenticated
if (!isset($_SESSION['loggedin'])) {
    header('Location: login.php');
    exit;
}

// Include database connection
require 'db_connect.php';

// Fetch all channels from the database
try {
    $stmt = $pdo->query("SELECT * FROM channels");
    $channels = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle channel actions (add, edit, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_channel'])) {
        $code = $_POST['code'];
        $name = $_POST['name'];
        $link = $_POST['link'];
        $icon = $_POST['icon'];

        try {
            $stmt = $pdo->prepare("INSERT INTO channels (code, name, link, icon) VALUES (?, ?, ?, ?)");
            $stmt->execute([$code, $name, $link, $icon]);
            header('Location: admin.php'); // Refresh the page
            exit;
        } catch (PDOException $e) {
            $error = "Error adding channel: " . $e->getMessage();
        }
    } elseif (isset($_POST['edit_channel'])) {
        $id = $_POST['id'];
        $code = $_POST['code'];
        $name = $_POST['name'];
        $link = $_POST['link'];
        $icon = $_POST['icon'];

        try {
            $stmt = $pdo->prepare("UPDATE channels SET code = ?, name = ?, link = ?, icon = ? WHERE id = ?");
            $stmt->execute([$code, $name, $link, $icon, $id]);
            header('Location: admin.php'); // Refresh the page
            exit;
        } catch (PDOException $e) {
            $error = "Error updating channel: " . $e->getMessage();
        }
    } elseif (isset($_POST['delete_channel'])) {
        $id = $_POST['id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM channels WHERE id = ?");
            $stmt->execute([$id]);
            header('Location: admin.php'); // Refresh the page
            exit;
        } catch (PDOException $e) {
            $error = "Error deleting channel: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Fnflix</title>
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

        /* Admin Panel Styles */
        .admin-container {
            max-width: 1200px;
            margin: 80px auto 20px;
            padding: 20px;
            width: 100%;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .channel-list {
            margin-top: 20px;
        }

        .channel-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background: #1a1a1a;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .channel-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 5px;
        }

        .channel-item h3 {
            margin: 0 10px;
            flex-grow: 1;
        }

        .channel-item button {
            margin-left: 10px;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .channel-item button.edit {
            background-color: #0088cc;
            color: #fff;
        }

        .channel-item button.delete {
            background-color: #cc0000;
            color: #fff;
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
            border-radius: 10px;
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
            border-radius: 5px;
            font-size: 16px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .join-button:hover {
            background-color: #006699;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }

        .already-joined {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .already-joined:hover {
            background-color: #45a049;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
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
    </nav>

    <!-- Admin Panel -->
    <div class="admin-container">
        <h1>Admin Panel</h1>

        <!-- Add Channel Form -->
        <form method="POST" action="admin.php">
            <div class="form-group">
                <label for="code">Code</label>
                <input type="text" id="code" name="code" required>
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="link">Link</label>
                <input type="url" id="link" name="link" required>
            </div>
            <div class="form-group">
                <label for="icon">Icon URL</label>
                <input type="url" id="icon" name="icon" required>
            </div>
            <button type="submit" name="add_channel">Add Channel</button>
        </form>

        <!-- Channel List -->
        <div class="channel-list">
            <?php foreach ($channels as $channel): ?>
            <div class="channel-item">
                <img src="<?= $channel['icon'] ?>" alt="<?= $channel['name'] ?>">
                <h3><?= $channel['name'] ?></h3>
                <form method="POST" action="admin.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $channel['id'] ?>">
                    <button type="submit" name="delete_channel" class="delete">Delete</button>
                </form>
                <button onclick="editChannel(<?= $channel['id'] ?>, '<?= $channel['code'] ?>', '<?= $channel['name'] ?>', '<?= $channel['link'] ?>', '<?= $channel['icon'] ?>')" class="edit">Edit</button>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Edit Channel Modal -->
    <div id="editModal" class="popup">
        <div class="popup-content">
            <h2>Edit Channel</h2>
            <form method="POST" action="admin.php">
                <input type="hidden" id="editId" name="id">
                <div class="form-group">
                    <label for="editCode">Code</label>
                    <input type="text" id="editCode" name="code" required>
                </div>
                <div class="form-group">
                    <label for="editName">Name</label>
                    <input type="text" id="editName" name="name" required>
                </div>
                <div class="form-group">
                    <label for="editLink">Link</label>
                    <input type="url" id="editLink" name="link" required>
                </div>
                <div class="form-group">
                    <label for="editIcon">Icon URL</label>
                    <input type="url" id="editIcon" name="icon" required>
                </div>
                <button type="submit" name="edit_channel">Save Changes</button>
                <button type="button" onclick="closeEditModal()">Cancel</button>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
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

        // Edit Channel Modal
        function editChannel(id, code, name, link, icon) {
            document.getElementById('editId').value = id;
            document.getElementById('editCode').value = code;
            document.getElementById('editName').value = name;
            document.getElementById('editLink').value = link;
            document.getElementById('editIcon').value = icon;
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
    </script>
</body>
</html>
