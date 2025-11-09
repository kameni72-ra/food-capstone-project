<?php
require_once 'config.php';

$searchQuery = $_GET['q'] ?? '';
$results = [];

if (!empty($searchQuery)) {
    $searchTerm = "%$searchQuery%";
    
    // Search foods
    $stmt = $pdo->prepare("SELECT * FROM foods WHERE food_name LIKE ? OR brand_name LIKE ? LIMIT 10");
    $stmt->execute([$searchTerm, $searchTerm]);
    $foodResults = $stmt->fetchAll();
    
    // Search ingredients
    $stmt = $pdo->prepare("SELECT * FROM ingredients WHERE name LIKE ? LIMIT 10");
    $stmt->execute([$searchTerm]);
    $ingredientResults = $stmt->fetchAll();
    
    // Search meals
    $stmt = $pdo->prepare("SELECT * FROM meals WHERE name LIKE ? OR category LIKE ? LIMIT 10");
    $stmt->execute([$searchTerm, $searchTerm]);
    $mealResults = $stmt->fetchAll();
    
    // Search nutrients from FDC data
    $stmt = $pdo->prepare("
        SELECT DISTINCT fdc_id, description, data_type, publication_date 
        FROM fdc_foodsandnutrients 
        WHERE description LIKE ? OR nutrient_name LIKE ? 
        LIMIT 10
    ");
    $stmt->execute([$searchTerm, $searchTerm]);
    $nutrientResults = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Search Results</h1>
        
        <!-- Search Form -->
        <div class="search-section">
            <form action="search.php" method="GET" class="search-form">
                <input type="search" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>" 
                       placeholder="Search for foods, ingredients, meals, or nutrients..." class="search-input" required>
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>

        <?php if (!empty($searchQuery)): ?>
            <!-- Foods Results -->
            <?php if (!empty($foodResults)): ?>
                <section style="margin-top: 2rem;">
                    <h2>Foods (<?php echo count($foodResults); ?>)</h2>
                    <div class="cards-grid">
                        <?php foreach ($foodResults as $food): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3><?php echo htmlspecialchars($food['food_name']); ?></h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Brand:</strong> <?php echo $food['brand_name'] ? htmlspecialchars($food['brand_name']) : 'N/A'; ?></p>
                                <p><strong>Calories:</strong> <?php echo $food['nf_calories']; ?></p>
                                <p><strong>Serving:</strong> <?php echo $food['serving_qty'] . ' ' . $food['serving_unit']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Ingredients Results -->
            <?php if (!empty($ingredientResults)): ?>
                <section style="margin-top: 2rem;">
                    <h2>Ingredients (<?php echo count($ingredientResults); ?>)</h2>
                    <div class="cards-grid">
                        <?php foreach ($ingredientResults as $ingredient): ?>
                        <div class="card">
                            <div class="card-body">
                                <h4><?php echo htmlspecialchars($ingredient['name']); ?></h4>
                                <p>ID: <?php echo $ingredient['id']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Meals Results -->
            <?php if (!empty($mealResults)): ?>
                <section style="margin-top: 2rem;">
                    <h2>Meals (<?php echo count($mealResults); ?>)</h2>
                    <div class="cards-grid">
                        <?php foreach ($mealResults as $meal): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3><?php echo htmlspecialchars($meal['name']); ?></h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($meal['category']); ?></p>
                                <p><strong>Area:</strong> <?php echo htmlspecialchars($meal['area']); ?></p>
                                <?php if ($meal['thumbnail']): ?>
                                    <img src="<?php echo htmlspecialchars($meal['thumbnail']); ?>" alt="<?php echo htmlspecialchars($meal['name']); ?>" style="max-width: 100%; height: auto;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Nutrient Results -->
            <?php if (!empty($nutrientResults)): ?>
                <section style="margin-top: 2rem;">
                    <h2>FDC Foods & Nutrients (<?php echo count($nutrientResults); ?>)</h2>
                    <div class="cards-grid">
                        <?php foreach ($nutrientResults as $nutrient): ?>
                        <div class="card">
                            <div class="card-header">
                                <h3><?php echo htmlspecialchars($nutrient['description']); ?></h3>
                            </div>
                            <div class="card-body">
                                <p><strong>FDC ID:</strong> <?php echo $nutrient['fdc_id']; ?></p>
                                <p><strong>Data Type:</strong> <?php echo htmlspecialchars($nutrient['data_type']); ?></p>
                                <p><strong>Publication Date:</strong> <?php echo $nutrient['publication_date']; ?></p>
                                <a href="nutrient_details.php?fdc_id=<?php echo $nutrient['fdc_id']; ?>" class="btn">View Nutrient Details</a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (empty($foodResults) && empty($ingredientResults) && empty($mealResults) && empty($nutrientResults)): ?>
                <div class="alert alert-error">
                    No results found for "<?php echo htmlspecialchars($searchQuery); ?>"
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
    <script src="navigation.js"></script>
</body>
</html>