<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM tourism_spots WHERE spot_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$spot = $stmt->get_result()->fetch_assoc();

if (!$spot) {
    header("Location: dashboard.php");
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
    $image_name = $spot['image'];

    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../assets/images/" . $image_name);
    }

    $update_stmt = $conn->prepare("UPDATE tourism_spots SET spot_name=?, location=?, category=?, description=?, entrance_fee=?, opening_hours=?, image=?, has_indoor_nav=?, indoor_nav_details=? WHERE spot_id=?");
    $update_stmt->bind_param("ssssdssisi", $name, $location, $category, $description, $fee, $hours, $image_name, $has_indoor, $indoor_details, $id);
    $update_stmt->execute();

    header("Location: dashboard.php");
    exit;
}

include '../includes/header.php';
?>

<div class="admin-form">
    <h2>Edit Tourist Destination</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Destination Name:</label>
            <input type="text" name="spot_name" value="<?= htmlspecialchars($spot['spot_name']) ?>" required>
        </div>
        <div class="form-group">
            <label>Location:</label>
            <input type="text" name="location" value="<?= htmlspecialchars($spot['location']) ?>" required>
        </div>
        <div class="form-group">
            <label>Category:</label>
            <select name="category" required>
                <?php 
                $cats = ['Waterfalls', 'Beaches', 'Caves & Parks', 'Rock Formations', 'Historical Sites'];
                foreach ($cats as $cat) {
                    $selected = ($spot['category'] === $cat) ? 'selected' : '';
                    echo "<option value='$cat' $selected>$cat</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Entrance Fee (PHP):</label>
            <input type="number" step="0.01" name="entrance_fee" value="<?= $spot['entrance_fee'] ?>" required>
        </div>
        <div class="form-group">
            <label>Opening Hours:</label>
            <input type="text" name="opening_hours" value="<?= htmlspecialchars($spot['opening_hours']) ?>">
        </div>
        <div class="form-group">
            <label>Description:</label>
            <textarea name="description" rows="4" required><?= htmlspecialchars($spot['description']) ?></textarea>
        </div>
        <div class="form-group">
            <label>Change Image (optional):</label>
            <input type="file" name="image" accept="image/*">
            <small>Current: <?= htmlspecialchars($spot['image']) ?></small>
        </div>
        <div class="form-group">
            <label><input type="checkbox" name="has_indoor_nav" value="1" <?= $spot['has_indoor_nav'] ? 'checked' : '' ?>> Enable Indoor Navigation Guide</label>
        </div>
        <div class="form-group">
            <label>Indoor Routing / Chamber Notes:</label>
            <textarea name="indoor_nav_details" rows="3"><?= htmlspecialchars($spot['indoor_nav_details'] ?? '') ?></textarea>
        </div>
        <button type="submit" class="btn">Update Destination</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
