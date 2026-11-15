<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['spot_name']);
    $location = trim($_POST['location']);
    $category = trim($_POST['category']);
    $description = trim($_POST['description']);
    $fee = floatval($_POST['entrance_fee']);
    $hours = trim($_POST['opening_hours']);
    $has_indoor = isset($_POST['has_indoor_nav']) ? 1 : 0;
    $indoor_details = trim($_POST['indoor_nav_details']);

    $image_name = 'default.jpg';
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/" . $image_name);
    }

    $stmt = $conn->prepare("INSERT INTO tourism_spots (spot_name, location, category, description, entrance_fee, opening_hours, image, has_indoor_nav, indoor_nav_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssdssis", $name, $location, $category, $description, $fee, $hours, $image_name, $has_indoor, $indoor_details);
    $stmt->execute();

    header("Location: dashboard.php");
    exit;
}

include '../includes/header.php';
?>

<div class="admin-form">
    <h2>Add New Tourist Destination</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Destination Name:</label>
            <input type="text" name="spot_name" required>
        </div>
        <div class="form-group">
            <label>Location:</label>
            <input type="text" name="location" required>
        </div>
        <div class="form-group">
            <label>Category:</label>
            <select name="category" required>
                <option value="Waterfalls">Waterfalls</option>
                <option value="Beaches">Beaches</option>
                <option value="Caves & Parks">Caves & Parks</option>
                <option value="Rock Formations">Rock Formations</option>
                <option value="Historical Sites">Historical Sites</option>
            </select>
        </div>
        <div class="form-group">
            <label>Entrance Fee (PHP):</label>
            <input type="number" step="0.01" name="entrance_fee" required>
        </div>
        <div class="form-group">
            <label>Opening Hours:</label>
            <input type="text" name="opening_hours" placeholder="e.g., 8:00 AM - 5:00 PM">
        </div>
        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label>Spot Image:</label>
            <input type="file" name="image" accept="image/*">
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="has_indoor_nav" value="1"> Enable Indoor Navigation / Section Guide</label>
        </div>
        <div class="form-group">
            <label>Indoor Routing / Chamber Notes:</label>
            <textarea name="indoor_nav_details" rows="3" placeholder="Specify internal room sequence or cave chambers..."></textarea>
        </div>
        <button type="submit" class="btn">Save Destination</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
