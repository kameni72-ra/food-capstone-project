<?php
require_once 'config.php';

$fdc_id = $_GET['fdc_id'] ?? '';

if (!empty($fdc_id)) {
    // Get food details
    $stmt = $pdo->prepare("SELECT DISTINCT description, data_type, publication_date FROM fdc_foodsandnutrients WHERE fdc_id = ? LIMIT 1");
    $stmt->execute([$fdc_id]);
    $food = $stmt->fetch();
    
    // Get all nutrients for this food
    $stmt = $pdo->prepare("SELECT nutrient_name, amount, unit_name, derivation_description FROM fdc_foodsandnutrients WHERE fdc_id = ?");
    $stmt->execute([$fdc_id]);
    $nutrients = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrient Details - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <?php if (!empty($food)): ?>
            <h1><?php echo htmlspecialchars($food['description']); ?></h1>
            <div class="card">
                <div class="card-header">
                    <h3>Food Information</h3>
                </div>
                <div class="card-body">
                    <p><strong>FDC ID:</strong> <?php echo $fdc_id; ?></p>
                    <p><strong>Data Type:</strong> <?php echo htmlspecialchars($food['data_type']); ?></p>
                    <p><strong>Publication Date:</strong> <?php echo $food['publication_date']; ?></p>
                </div>
            </div>
            
            <div style="margin-top: 2rem;">
                <h3>Nutrient Composition</h3>
                <div class="cards-grid">
                    <?php foreach ($nutrients as $nutrient): ?>
                    <div class="card">
                        <div class="card-body">
                            <h4><?php echo htmlspecialchars($nutrient['nutrient_name']); ?></h4>
                            <p><strong>Amount:</strong> <?php echo $nutrient['amount']; ?> <?php echo $nutrient['unit_name']; ?></p>
                            <p><strong>Method:</strong> <?php echo htmlspecialchars($nutrient['derivation_description']); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-error">Food not found.</div>
        <?php endif; ?>
        
        <a href="search.php" class="btn" style="margin-top: 2rem;">Back to Search</a>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>