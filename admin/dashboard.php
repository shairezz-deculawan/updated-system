<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../login.php");
    exit;
}

$message = '';

// 1. SERIALIZE DATA FOR STORAGE (Export to JSON file / Download)
if (isset($_GET['serialize_export'])) {
    $res = $conn->query("SELECT spot_name, location, category, description, entrance_fee, image, opening_hours, has_indoor_nav, indoor_nav_details FROM tourism_spots");
    $data_array = [];
    while ($row = $res->fetch_assoc()) {
        $data_array[] = $row;
    }
    
    // Serialize PHP array into formatted JSON string
    $serialized_json = json_encode($data_array, JSON_PRETTY_PRINT);
    
    // Save local backup copy in project folder
    file_put_contents('../tourism_serialized_backup.json', $serialized_json);
    
    // Trigger browser file download
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="tourism_spots_serialized_' . date('Y-m-d') . '.json"');
    echo $serialized_json;
    exit;
}

// 2. DESERIALIZE DATA FOR RETRIEVAL (Import from JSON file)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['json_file'])) {
    if (!empty($_FILES['json_file']['tmp_name'])) {
        $raw_json = file_get_contents($_FILES['json_file']['tmp_name']);
        // Deserialize JSON string back into PHP associative array
        $decoded_records = json_decode($raw_json, true);

        if (is_array($decoded_records)) {
            $stmt = $conn->prepare("INSERT INTO tourism_spots (spot_name, location, category, description, entrance_fee, image, opening_hours, has_indoor_nav, indoor_nav_details) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $count = 0;
            foreach ($decoded_records as $item) {
                $stmt->bind_param(
                    "ssssdssis",
                    $item['spot_name'],
                    $item['location'],
                    $item['category'],
                    $item['description'],
                    $item['entrance_fee'],
                    $item['image'],
                    $item['opening_hours'],
                    $item['has_indoor_nav'],
                    $item['indoor_nav_details']
                );
                $stmt->execute();
                $count++;
            }
            $message = "✅ Successfully deserialized and restored $count records from JSON storage!";
        } else {
            $message = "❌ Invalid JSON file format.";
        }
    }
}

// Sorting criteria in Admin Table
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id_desc';
$order_by = "spot_id DESC";
if ($sort === 'name_asc') $order_by = "spot_name ASC";
if ($sort === 'fee_desc') $order_by = "entrance_fee DESC";
if ($sort === 'fee_asc') $order_by = "entrance_fee ASC";

$spots = $conn->query("SELECT * FROM tourism_spots ORDER BY $order_by");

// Admin Calculations
$calc = $conn->query("SELECT COUNT(*) as total, AVG(entrance_fee) as avg_fee, MAX(entrance_fee) as max_fee FROM tourism_spots")->fetch_assoc();

include '../includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px;">
    <h1>Admin Management Dashboard</h1>
    <div>
        <a href="add_spot.php" class="btn">+ Add New Destination</a>
        <a href="dashboard.php?serialize_export=1" class="btn btn-secondary">💾 Serialize & Export JSON</a>
    </div>
</div>

<?php if ($message): ?>
    <div style="background: #d8f3dc; color: #1b4332; padding: 12px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;">
        <?= $message ?>
    </div>
<?php endif; ?>

<!-- Admin Statistics & Calculations -->
<section class="stats-grid">
    <div class="stat-card">
        <h3><?= $calc['total'] ?></h3>
        <p>Total Records</p>
    </div>
    <div class="stat-card">
        <h3>₱<?= number_format($calc['avg_fee'] ?? 0, 2) ?></h3>
        <p>Average Fee</p>
    </div>
    <div class="stat-card">
        <h3>₱<?= number_format($calc['max_fee'] ?? 0, 2) ?></h3>
        <p>Highest Entrance Fee</p>
    </div>
</section>

<!-- Deserialization / Restore Form -->
<div style="background: white; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.08); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
    <form method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 10px;">
        <strong>📂 Deserialize / Import JSON:</strong>
        <input type="file" name="json_file" accept=".json" required>
        <button type="submit" class="btn-sm">Restore Data</button>
    </form>

    <div>
        <strong>Sort Table:</strong>
        <a href="dashboard.php?sort=id_desc" class="btn-sm btn-secondary">Latest</a>
        <a href="dashboard.php?sort=name_asc" class="btn-sm btn-secondary">Name (A-Z)</a>
        <a href="dashboard.php?sort=fee_asc" class="btn-sm btn-secondary">Fee (Low-High)</a>
        <a href="dashboard.php?sort=fee_desc" class="btn-sm btn-secondary">Fee (High-Low)</a>
    </div>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Spot Name</th>
            <th>Location</th>
            <th>Category</th>
            <th>Fee (PHP)</th>
            <th>Indoor Nav</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($spots->num_rows > 0): ?>
            <?php while ($row = $spots->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['spot_id'] ?></td>
                    <td><strong><?= htmlspecialchars($row['spot_name']) ?></strong></td>
                    <td><?= htmlspecialchars($row['location']) ?></td>
                    <td><span class="badge"><?= htmlspecialchars($row['category']) ?></span></td>
                    <td>₱<?= number_format($row['entrance_fee'], 2) ?></td>
                    <td><?= $row['has_indoor_nav'] ? '✅ Enabled' : '❌ Disabled' ?></td>
                    <td>
                        <a href="edit_spot.php?id=<?= $row['spot_id'] ?>" class="btn-sm">Edit</a>
                        <a href="delete_spot.php?id=<?= $row['spot_id'] ?>" class="btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No tourism destination records found.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>