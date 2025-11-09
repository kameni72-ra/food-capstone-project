<?php
require_once 'config.php';

// Check if import form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['import_type'])) {
        $importType = $_POST['import_type'];
        $message = '';
        
        switch ($importType) {
            case 'foods':
                // Import foods logic here
                $message = "Foods import functionality would be implemented here";
                break;
            case 'ingredients':
                // Import ingredients logic here
                $message = "Ingredients import functionality would be implemented here";
                break;
            case 'meals':
                // Import meals logic here
                $message = "Meals import functionality would be implemented here";
                break;
        }
        
        if ($message) {
            $alertClass = 'alert-success';
            $alertMessage = $message;
        }
    }
}

// Get table counts for dashboard
$tableCounts = [];
$tables = ['foods', 'ingredients', 'meals', 'meal_ingredients', 'fdc_foodsandnutrients'];

foreach ($tables as $table) {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
    $tableCounts[$table] = $stmt->fetch()['count'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="container">
        <h1>Admin Dashboard</h1>
        
        <?php if (isset($alertMessage)): ?>
            <div class="alert <?php echo $alertClass; ?>">
                <?php echo $alertMessage; ?>
            </div>
        <?php endif; ?>

        <!-- Database Stats -->
        <section style="margin-bottom: 3rem;">
            <h2>Database Statistics</h2>
            <div class="cards-grid">
                <?php foreach ($tableCounts as $table => $count): ?>
                <div class="card">
                    <div class="card-header">
                        <h3><?php echo ucfirst(str_replace('_', ' ', $table)); ?></h3>
                    </div>
                    <div class="card-body">
                        <p style="font-size: 2rem; font-weight: bold; text-align: center;">
                            <?php echo $count; ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Import Section -->
        <section style="margin-bottom: 3rem;">
            <h2>Data Import</h2>
            <div class="card">
                <div class="card-body">
                    <form method="POST" class="search-form">
                        <div class="form-group">
                            <label class="form-label">Select data to import:</label>
                            <select name="import_type" class="form-control" required>
                                <option value="">Choose data type...</option>
                                <option value="foods">Foods Data</option>
                                <option value="ingredients">Ingredients Data</option>
                                <option value="meals">Meals Data</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Import Data</button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section>
            <h2>Quick Actions</h2>
            <div class="cards-grid">
                <div class="card">
                    <div class="card-header">
                        <h3>Manage Foods</h3>
                    </div>
                    <div class="card-body">
                        <p>View and manage food items in the database</p>
                    </div>
                    <div class="card-footer">
                        <a href="foods.php" class="btn">View Foods</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Manage Ingredients</h3>
                    </div>
                    <div class="card-body">
                        <p>View and manage ingredient items</p>
                    </div>
                    <div class="card-footer">
                        <a href="ingredients.php" class="btn">View Ingredients</a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3>Manage Meals</h3>
                    </div>
                    <div class="card-body">
                        <p>View and manage meal recipes</p>
                    </div>
                    <div class="card-footer">
                        <a href="meals.php" class="btn">View Meals</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include 'footer.php'; ?>
    <script src="navigation.js"></script>
</body>
</html>