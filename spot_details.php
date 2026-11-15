<?php
include 'config/database.php';
include 'includes/header.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $conn->prepare("SELECT * FROM tourism_spots WHERE spot_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$spot = $stmt->get_result()->fetch_assoc();

if (!$spot) {
    echo "<h2>Destination not found.</h2><a href='index.php'>&larr; Back to directory</a>";
    include 'includes/footer.php';
    exit;
}
?>

<a href="index.php" class="btn-sm btn-secondary">&larr; Back to Directory</a>

<div class="spot-detail-card">
    <span class="badge"><?= htmlspecialchars($spot['category']) ?></span>
    <h1><?= htmlspecialchars($spot['spot_name']) ?></h1>
    <p style="color:#666;">📍 <?= htmlspecialchars($spot['location']) ?></p>

    <img src="assets/images/<?= htmlspecialchars($spot['image']) ?>" class="spot-detail-img" alt="<?= htmlspecialchars($spot['spot_name']) ?>" onerror="this.src='https://via.placeholder.com/800x400?text=Destination+Image'">

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <p><strong>Standard Entrance Fee:</strong> ₱<?= number_format($spot['entrance_fee'], 2) ?></p>
        <p><strong>Opening Hours:</strong> <?= htmlspecialchars($spot['opening_hours']) ?></p>
    </div>

    <h3>Description</h3>
    <p><?= nl2br(htmlspecialchars($spot['description'])) ?></p>

    <!-- Interactive Group Budget Calculator -->
    <div style="background: #f8f9fa; border: 1px solid #ddd; padding: 20px; border-radius: 8px; margin-top: 25px;">
        <h3>🧮 Trip Entrance Cost Calculator</h3>
        <p style="margin-bottom: 12px; color: #555;">Calculate your group's total entrance expense before visiting:</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 15px;">
            <div class="form-group">
                <label>Regular Visitors:</label>
                <input type="number" id="regularCount" value="1" min="0" oninput="calculateTripCost()">
            </div>
            <div class="form-group">
                <label>Students / Seniors (20% Off):</label>
                <input type="number" id="discountCount" value="0" min="0" oninput="calculateTripCost()">
            </div>
            <div class="form-group">
                <label>Local Tour Guide (₱300):</label>
                <select id="includeGuide" onchange="calculateTripCost()">
                    <option value="0">No Guide</option>
                    <option value="300">Include Guide (+₱300)</option>
                </select>
            </div>
        </div>

        <div style="margin-top: 10px; padding: 12px; background: #d8f3dc; color: #1b4332; border-radius: 4px; font-size: 1.1em;">
            <strong>Estimated Total Cost: ₱<span id="totalCostDisplay"><?= number_format($spot['entrance_fee'], 2) ?></span></strong>
        </div>
    </div>

    <!-- Indoor Navigation Guide -->
    <?php if ($spot['has_indoor_nav']): ?>
        <div class="indoor-nav-box">
            <h3>🗺️ Indoor & Cave Section Navigation Guide</h3>
            <p style="margin-bottom: 10px;">Recommended interior route and chamber progression for visitors:</p>
            
            <div class="route-nodes">
                <span class="node">📍 1. Reception / Entrance</span> &rarr;
                <span class="node">📍 2. Main Chamber</span> &rarr;
                <span class="node">📍 3. Natural Bridge Arch</span> &rarr;
                <span class="node">📍 4. Subterranean River Dock</span>
            </div>

            <h4 style="margin-top: 15px;">Interior Route Breakdown:</h4>
            <p><?= nl2br(htmlspecialchars($spot['indoor_nav_details'] ?? 'Follow indoor trail markers and guided station indicators.')) ?></p>
        </div>
    <?php endif; ?>
</div>

<script>
const baseFee = <?= floatval($spot['entrance_fee']) ?>;

function calculateTripCost() {
    const regular = parseInt(document.getElementById('regularCount').value) || 0;
    const discounted = parseInt(document.getElementById('discountCount').value) || 0;
    const guideFee = parseFloat(document.getElementById('includeGuide').value) || 0;

    const regularTotal = regular * baseFee;
    const discountTotal = discounted * (baseFee * 0.80); // 20% discount
    const grandTotal = regularTotal + discountTotal + guideFee;

    document.getElementById('totalCostDisplay').innerText = grandTotal.toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}
</script>

<?php include 'includes/footer.php'; ?>