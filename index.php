<?php 
include 'config/database.php';
include 'includes/header.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';

// Build Query with Search and Category Filter
$sql = "SELECT * FROM tourism_spots WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (spot_name LIKE ? OR location LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

// Multi-Criteria Sorting Logic
switch ($sort) {
    case 'name_asc':
        $sql .= " ORDER BY spot_name ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY spot_name DESC";
        break;
    case 'fee_asc':
        $sql .= " ORDER BY entrance_fee ASC";
        break;
    case 'fee_desc':
        $sql .= " ORDER BY entrance_fee DESC";
        break;
    default:
        $sql .= " ORDER BY spot_id DESC";
        break;
}

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$spots = $stmt->get_result();

// Perform Calculations (Count, Average Fee, Total Tour Cost, Max Fee)
$calc_query = $conn->query("SELECT 
    COUNT(*) as total_spots, 
    COUNT(DISTINCT category) as total_cats,
    AVG(entrance_fee) as avg_fee,
    SUM(entrance_fee) as total_tour_cost,
    MAX(entrance_fee) as max_fee
    FROM tourism_spots");
$stats = $calc_query->fetch_assoc();
?>

<section class="hero">
    <h1>Explore top destinations in Samar</h1>
    <p>Discover waterfalls, beaches, rock formations, and caves across local communities.</p>
    <form action="index.php" method="GET" class="search-form">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search by destination name or location...">
        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
        <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
        <button type="submit" class="btn">Search</button>
    </form>
</section>

<!-- Calculations Dashboard -->
<section class="stats-grid">
    <div class="stat-card">
        <h2><?= $stats['total_spots'] ?></h2>
        <p>Destinations Registered</p>
    </div>
    <div class="stat-card">
        <h2><?= $stats['total_cats'] ?></h2>
        <p>Unique Categories</p>
    </div>
    <div class="stat-card">
        <h2>₱<?= number_format($stats['avg_fee'] ?? 0, 2) ?></h2>
        <p>Average Entrance Fee</p>
    </div>
    <div class="stat-card">
        <h2>₱<?= number_format($stats['total_tour_cost'] ?? 0, 2) ?></h2>
        <p>All-Spots Tour Total</p>
    </div>
</section>

<!-- Filter & Multi-Criteria Sorting Bar -->
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px; background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.08);">
    <div>
        <strong style="margin-right: 10px;">Category:</strong>
        <a href="index.php?sort=<?= urlencode($sort) ?>" class="btn-sm btn-secondary">All</a>
        <a href="index.php?category=Waterfalls&sort=<?= urlencode($sort) ?>" class="btn-sm">Waterfalls</a>
        <a href="index.php?category=Beaches&sort=<?= urlencode($sort) ?>" class="btn-sm">Beaches</a>
        <a href="index.php?category=Caves %26 Parks&sort=<?= urlencode($sort) ?>" class="btn-sm">Caves & Parks</a>
    </div>

    <form action="index.php" method="GET" style="display: flex; align-items: center; gap: 8px;">
        <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
        <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
        <label for="sort"><strong>Sort By:</strong></label>
        <select name="sort" id="sort" onchange="this.form.submit()" style="padding: 6px 10px; border-radius: 4px; border: 1px solid #ccc;">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest Added</option>
            <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name (A - Z)</option>
            <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Name (Z - A)</option>
            <option value="fee_asc" <?= $sort === 'fee_asc' ? 'selected' : '' ?>>Entrance Fee (Low to High)</option>
            <option value="fee_desc" <?= $sort === 'fee_desc' ? 'selected' : '' ?>>Entrance Fee (High to Low)</option>
        </select>
    </form>
</div>

<div class="card-grid">
    <?php if ($spots->num_rows > 0): ?>
        <?php while ($spot = $spots->fetch_assoc()): ?>
            <div class="card">
                <img src="assets/images/<?= htmlspecialchars($spot['image']) ?>" alt="<?= htmlspecialchars($spot['spot_name']) ?>" onerror="this.src='https://via.placeholder.com/300x180?text=Tourism+Spot'">
                <div class="card-body">
                    <span class="badge"><?= htmlspecialchars($spot['category']) ?></span>
                    <h3><?= htmlspecialchars($spot['spot_name']) ?></h3>
                    <p style="color:#555; margin: 5px 0;">📍 <?= htmlspecialchars($spot['location']) ?></p>
                    <p style="margin-bottom: 15px;"><strong>Entrance Fee:</strong> ₱<?= number_format($spot['entrance_fee'], 2) ?></p>
                    <a href="spot_details.php?id=<?= $spot['spot_id'] ?>" class="btn" style="margin-top: auto; text-align:center;">View Details</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No tourism destinations found matching your criteria.</p>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>