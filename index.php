<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <!-- Hero Section -->
        <section class="hero">
            <h1>Welcome to Capstone 2025 Team 3</h1>
            <p>Your comprehensive nutrition and meal planning solution</p>
            <div class="search-section">
                <form action="search.php" method="GET" class="search-form">
                    <input type="search" name="q" placeholder="Search for foods, ingredients, or meals..." class="search-input" required>
                    <button type="submit" class="search-btn">Search</button>
                </form>
            </div>
        </section>

        <!-- Quick Stats -->
        <div class="cards-grid">
            <div class="card">
                <div class="card-header">
                    <h3>Foods Database</h3>
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM foods");
                    $foodCount = $stmt->fetch()['count'];
                    ?>
                    <p>Total Foods: <strong><?php echo $foodCount; ?></strong></p>
                    <p>Browse our comprehensive food nutrition database</p>
                </div>
                <div class="card-footer">
                    <a href="foods.php" class="btn">View All Foods</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Ingredients</h3>
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM ingredients");
                    $ingredientCount = $stmt->fetch()['count'];
                    ?>
                    <p>Total Ingredients: <strong><?php echo $ingredientCount; ?></strong></p>
                    <p>Explore our extensive ingredient library</p>
                </div>
                <div class="card-footer">
                    <a href="ingredients.php" class="btn">View Ingredients</a>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Meals & Recipes</h3>
                </div>
                <div class="card-body">
                    <?php
                    $stmt = $pdo->query("SELECT COUNT(*) as count FROM meals");
                    $mealCount = $stmt->fetch()['count'];
                    ?>
                    <p>Total Meals: <strong><?php echo $mealCount; ?></strong></p>
                    <p>Discover delicious recipes and meal plans</p>
                </div>
                <div class="card-footer">
                    <a href="meals.php" class="btn">View Meals</a>
                </div>
            </div>
        </div>

        <!-- Recent Foods -->
        <section style="margin-top: 3rem;">
            <h2>Recently Added Foods</h2>
            <div class="cards-grid">
                <?php
                $stmt = $pdo->query("SELECT * FROM foods ORDER BY consumed_at DESC LIMIT 20");
                $recentFoods = $stmt->fetchAll();
                
                foreach ($recentFoods as $food): 
                ?>
                <div class="card">
                    <div class="card-header">
                        <h4><?php echo htmlspecialchars($food['food_name']); ?></h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Calories:</strong> <?php echo $food['nf_calories']; ?></p>
                        <p><strong>Protein:</strong> <?php echo $food['nf_protein']; ?>g</p>
                        <p><strong>Carbs:</strong> <?php echo $food['nf_total_carbohydrate']; ?>g</p>
                        <p><strong>Fat:</strong> <?php echo $food['nf_total_fat']; ?>g</p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <?php include 'footer.php'; ?>
    <script src="navigation.js"></script>
</body>
</html>