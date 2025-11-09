<?php
require_once 'config.php';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 30;
$offset = ($page - 1) * $perPage;

// Search functionality
$search = $_GET['search'] ?? '';
$whereClause = '';
$params = [];

if (!empty($search)) {
    $whereClause = "WHERE name LIKE ?";
    $params[] = "%$search%";
}

// Get total count
$countQuery = "SELECT COUNT(*) as count FROM ingredients $whereClause";
$stmt = $pdo->prepare($countQuery);
$stmt->execute($params);
$totalIngredients = $stmt->fetch()['count'];
$totalPages = ceil($totalIngredients / $perPage);

// Get ingredients with pagination
$query = "SELECT * FROM ingredients $whereClause ORDER BY name LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($query);
foreach ($params as $key => $value) {
    $stmt->bindValue($key + 1, $value);
}
$stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
$stmt->execute();
$ingredients = $stmt->fetchAll();

// Function to get meals that use an ingredient
function getIngredientUsage($pdo, $ingredientId) {
    $stmt = $pdo->prepare("
        SELECT m.meal_id, m.name, mi.measure 
        FROM meal_ingredients mi 
        JOIN meals m ON mi.meal_id = m.meal_id 
        WHERE mi.ingredient_id = ?
        LIMIT 5
    ");
    $stmt->execute([$ingredientId]);
    return $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingredients - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .ingredient-card {
            transition: transform 0.2s;
        }
        .ingredient-card:hover {
            transform: translateY(-2px);
        }
        .usage-list {
            max-height: 120px;
            overflow-y: auto;
            margin: 0.5rem 0;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
            font-size: 0.9rem;
        }
        .usage-list ul {
            margin: 0;
            padding-left: 1.2rem;
        }
        .alphabet-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 1rem 0;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .alphabet-nav a {
            padding: 0.5rem 0.75rem;
            text-decoration: none;
            color: #667eea;
            border: 1px solid #667eea;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .alphabet-nav a:hover,
        .alphabet-nav a.active {
            background: #667eea;
            color: white;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        .stat-card {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Ingredients Library</h1>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $totalIngredients; ?></div>
                <div>Total Ingredients</div>
            </div>
            <?php
            // Get ingredients used in meals count
            $stmt = $pdo->query("SELECT COUNT(DISTINCT ingredient_id) as count FROM meal_ingredients");
            $usedIngredients = $stmt->fetch()['count'];
            ?>
            <div class="stat-card">
                <div class="stat-number"><?php echo $usedIngredients; ?></div>
                <div>Used in Meals</div>
            </div>
            <?php
            // Get most used ingredient
           $stmt = $pdo->query("
           SELECT i.name, COUNT(*) as usage_count 
           FROM meal_ingredients mi 
           JOIN ingredients i ON mi.ingredient_id = i.id 
           GROUP BY mi.ingredient_id, i.name
           ORDER BY usage_count DESC 
           LIMIT 1
           ");
           $mostUsed = $stmt->fetch();
           ?>
           <div class="stat-card">
                <div class="stat-number"><?php echo $mostUsed ? $mostUsed['usage_count'] : 0; ?></div>
                <div>Most Used: <?php echo $mostUsed ? htmlspecialchars($mostUsed['name']) : 'N/A'; ?></div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search ingredients..." 
                       value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                <button type="submit" class="search-btn">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="ingredients.php" class="btn">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Alphabet Navigation -->
        <div class="alphabet-nav">
            <?php 
            $letters = range('A', 'Z');
            foreach ($letters as $letter): 
                $isActive = isset($_GET['letter']) && $_GET['letter'] === $letter;
            ?>
                <a href="?letter=<?php echo $letter; ?>" 
                   class="<?php echo $isActive ? 'active' : ''; ?>">
                    <?php echo $letter; ?>
                </a>
            <?php endforeach; ?>
            <a href="ingredients.php" class="<?php echo !isset($_GET['letter']) ? 'active' : ''; ?>">All</a>
        </div>

        <!-- Ingredients Grid -->
        <div class="cards-grid">
            <?php foreach ($ingredients as $ingredient): 
                $usage = getIngredientUsage($pdo, $ingredient['id']);
            ?>
            <div class="card ingredient-card">
                <div class="card-header">
                    <h3><?php echo htmlspecialchars($ingredient['name']); ?></h3>
                </div>
                
                <div class="card-body">
                    <p><strong>ID:</strong> <?php echo $ingredient['id']; ?></p>
                    
                    <?php if (!empty($usage)): ?>
                        <div class="usage-list">
                            <strong>Used in:</strong>
                            <ul>
                                <?php foreach ($usage as $meal): ?>
                                    <li>
                                        <?php echo htmlspecialchars($meal['name']); ?>
                                        <?php if ($meal['measure']): ?>
                                            <br><small>(<?php echo htmlspecialchars($meal['measure']); ?>)</small>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <p><em>Not currently used in any meals</em></p>
                    <?php endif; ?>
                </div>
                
                <div class="card-footer">
                    <div style="display: flex; gap: 0.5rem; justify-content: space-between;">
                        <span>
                            <strong>Usage:</strong> <?php echo count($usage); ?> meals
                        </span>
                        <?php if (!empty($usage)): ?>
                            <button onclick="showIngredientDetails(<?php echo $ingredient['id']; ?>)" 
                                    class="btn btn-success">View Details</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Message -->
        <?php if (empty($ingredients)): ?>
            <div class="alert alert-error" style="text-align: center;">
                No ingredients found<?php echo !empty($search) ? ' for "' . htmlspecialchars($search) . '"' : ''; ?>.
            </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div style="text-align: center; margin-top: 2rem;">
            <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn">Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?page=<?php echo $i; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn <?php echo $i === $page ? 'active' : ''; ?>" 
                   style="margin: 0 0.25rem;">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                   class="btn">Next</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Ingredient Details Modal -->
    <div id="ingredientModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; overflow-y: auto;">
        <div style="background: white; margin: 2rem auto; padding: 2rem; border-radius: 10px; max-width: 800px; position: relative;">
            <button onclick="closeIngredientDetails()" style="position: absolute; top: 1rem; right: 1rem; background: #e74c3c; color: white; border: none; border-radius: 50%; width: 30px; height: 30px; cursor: pointer;">×</button>
            <div id="ingredientModalContent"></div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <script src="navigation.js"></script>
    <script>
        function showIngredientDetails(ingredientId) {
            fetch('ingredient_details.php?id=' + ingredientId)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('ingredientModalContent').innerHTML = html;
                    document.getElementById('ingredientModal').style.display = 'block';
                });
        }

        function closeIngredientDetails() {
            document.getElementById('ingredientModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('ingredientModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeIngredientDetails();
            }
        });

        // Alphabet navigation filtering
        document.querySelectorAll('.alphabet-nav a').forEach(link => {
            link.addEventListener('click', function(e) {
                if (this.getAttribute('href') === 'ingredients.php') {
                    return; // Allow "All" link to work normally
                }
                e.preventDefault();
                const letter = this.textContent;
                window.location.href = `ingredients.php?letter=${letter}`;
            });
        });
    </script>
</body>
</html>