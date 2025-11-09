<?php
require_once 'config.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 20;
$offset = ($page - 1) * $perPage;

// Get total count
$stmt = $pdo->query("SELECT COUNT(*) as count FROM foods");
$totalFoods = $stmt->fetch()['count'];
$totalPages = ceil($totalFoods / $perPage);

// Get foods with pagination
$stmt = $pdo->prepare("SELECT * FROM foods ORDER BY food_name LIMIT ? OFFSET ?");
$stmt->bindValue(1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$foods = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foods - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Foods Database</h1>
        <p>Total foods: <?php echo $totalFoods; ?></p>

        <!-- Foods Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Calories</th>
                    <th>Protein (g)</th>
                    <th>Carbs (g)</th>
                    <th>Fat (g)</th>
                    <th>Serving</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($foods as $food): ?>
                <tr>
                    <td><?php echo htmlspecialchars($food['food_name']); ?></td>
                    <td><?php echo $food['brand_name'] ? htmlspecialchars($food['brand_name']) : 'N/A'; ?></td>
                    <td><?php echo $food['nf_calories']; ?></td>
                    <td><?php echo $food['nf_protein']; ?></td>
                    <td><?php echo $food['nf_total_carbohydrate']; ?></td>
                    <td><?php echo $food['nf_total_fat']; ?></td>
                    <td><?php echo $food['serving_qty'] . ' ' . $food['serving_unit']; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div style="text-align: center; margin-top: 2rem;">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="btn <?php echo $i === $page ? 'active' : ''; ?>" 
                   style="margin: 0 0.25rem;">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
    <script src="navigation.js"></script>
</body>
</html>