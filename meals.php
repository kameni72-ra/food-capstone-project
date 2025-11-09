<?php
require_once 'config.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

// Get total count
$stmt = $pdo->query("SELECT COUNT(*) as count FROM meals");
$totalMeals = $stmt->fetch()['count'];
$totalPages = ceil($totalMeals / $perPage);

// Get meals with pagination
$stmt = $pdo->prepare("SELECT * FROM meals ORDER BY name LIMIT ? OFFSET ?");
$stmt->bindValue(1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$meals = $stmt->fetchAll();

// Function to get ingredients for a meal
function getMealIngredients($pdo, $mealId) {
    $stmt = $pdo->prepare("
        SELECT i.name, mi.measure 
        FROM meal_ingredients mi 
        JOIN ingredients i ON mi.ingredient_id = i.id 
        WHERE mi.meal_id = ?
    ");
    $stmt->execute([$mealId]);
    return $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meals - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .meal-card {
            position: relative;
        }
        .meal-thumbnail {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .ingredients-list {
            max-height: 150px;
            overflow-y: auto;
            margin: 1rem 0;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .ingredients-list ul {
            margin: 0;
            padding-left: 1.5rem;
        }
        .meal-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .instructions-preview {
            max-height: 100px;
            overflow: hidden;
            position: relative;
        }
        .instructions-preview::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 30px;
            background: linear-gradient(transparent, white);
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Meals & Recipes</h1>
        <p>Total meals: <strong><?php echo $totalMeals; ?></strong></p>

        <!-- Search and Filter Section -->
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search meals..." 
                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" class="search-input">
                <select name="category" class="form-control" style="max-width: 200px;">
                    <option value="">All Categories</option>
                    <?php
                    $categories = $pdo->query("SELECT DISTINCT category FROM meals WHERE category IS NOT NULL ORDER BY category")->fetchAll();
                    foreach ($categories as $cat): 
                    ?>
                        <option value="<?php echo htmlspecialchars($cat['category']); ?>" 
                            <?php echo ($_GET['category'] ?? '') === $cat['category'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="search-btn">Filter</button>
            </form>
        </div>

        <!-- Meals Grid -->
        <div class="cards-grid">
            <?php foreach ($meals as $meal): 
                $ingredients = getMealIngredients($pdo, $meal['meal_id']);
            ?>
            <div class="card meal-card">
                <?php if ($meal['thumbnail']): ?>
                    <img src="<?php echo htmlspecialchars($meal['thumbnail']); ?>" 
                         alt="<?php echo htmlspecialchars($meal['name']); ?>" 
                         class="meal-thumbnail">
                <?php endif; ?>
                
                <div class="card-header">
                    <h3><?php echo htmlspecialchars($meal['name']); ?></h3>
                </div>
                
                <div class="card-body">
                    <?php if ($meal['category']): ?>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($meal['category']); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($meal['area']): ?>
                        <p><strong>Cuisine:</strong> <?php echo htmlspecialchars($meal['area']); ?></p>
                    <?php endif; ?>

                    <?php if (!empty($ingredients)): ?>
                        <div class="ingredients-list">
                            <strong>Ingredients:</strong>
                            <ul>
                                <?php foreach ($ingredients as $ingredient): ?>
                                    <li><?php echo htmlspecialchars($ingredient['name']); ?> - <?php echo htmlspecialchars($ingredient['measure']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($meal['instructions']): ?>
                        <div class="instructions-preview">
                            <strong>Instructions:</strong>
                            <p><?php echo substr(htmlspecialchars($meal['instructions']), 0, 150); ?>...</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer">
                    <div class="meal-actions">
                        <?php if ($meal['youtube_url']): ?>
                            <a href="<?php echo htmlspecialchars($meal['youtube_url']); ?>" 
                               target="_blank" class="btn btn-danger">Watch Video</a>
                        <?php endif; ?>
                        
                        <?php if ($meal['source_url']): ?>
                            <a href="<?php echo htmlspecialchars($meal['source_url']); ?>" 
                               target="_blank" class="btn">View Recipe</a>
                        <?php endif; ?>
                        
                        <button onclick="showMealDetails(<?php echo $meal['meal_id']; ?>)" 
                                class="btn btn-success">View Details</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div style="text-align: center; margin-top: 2rem;">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>" class="btn">Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="btn <?php echo $i === $page ? 'active' : ''; ?>" 
                   style="margin: 0 0.25rem;">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>" class="btn">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Meal Details Modal -->
    <div id="mealModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; overflow-y: auto;">
        <div style="background: white; margin: 2rem auto; padding: 2rem; border-radius: 10px; max-width: 800px; position: relative;">
            <button onclick="closeMealDetails()" style="position: absolute; top: 1rem; right: 1rem; background: #e74c3c; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer;">×</button>
            <div id="mealModalContent"></div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="navigation.js"></script>
    <script>
        function showMealDetails(mealId) {
            fetch('meal_details.php?id=' + mealId)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('mealModalContent').innerHTML = html;
                    document.getElementById('mealModal').style.display = 'block';
                });
        }

        function closeMealDetails() {
            document.getElementById('mealModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('mealModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMealDetails();
            }
        });
    </script>
</body>
</html>