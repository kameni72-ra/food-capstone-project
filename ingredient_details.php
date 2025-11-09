<?php
require_once 'config.php';

$ingredientId = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM ingredients WHERE id = ?");
$stmt->execute([$ingredientId]);
$ingredient = $stmt->fetch();

if (!$ingredient) {
    echo '<div class="alert alert-error">Ingredient not found</div>';
    exit;
}

// Get meals that use this ingredient
$stmt = $pdo->prepare("
    SELECT m.meal_id, m.name, m.category, m.thumbnail, mi.measure 
    FROM meal_ingredients mi 
    JOIN meals m ON mi.meal_id = m.meal_id 
    WHERE mi.ingredient_id = ?
    ORDER BY m.name
");
$stmt->execute([$ingredientId]);
$meals = $stmt->fetchAll();

// Get usage statistics
$stmt = $pdo->prepare("SELECT COUNT(*) as usage_count FROM meal_ingredients WHERE ingredient_id = ?");
$stmt->execute([$ingredientId]);
$usageCount = $stmt->fetch()['usage_count'];
?>

<div class="ingredient-details">
    <h2><?php echo htmlspecialchars($ingredient['name']); ?></h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0;">
        <div class="stat-card">
            <div class="stat-number"><?php echo $usageCount; ?></div>
            <div>Total Usage</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $ingredientId; ?></div>
            <div>Ingredient ID</div>
        </div>
    </div>

    <?php if (!empty($meals)): ?>
        <div style="margin: 1.5rem 0;">
            <h3>Used in These Meals</h3>
            <div class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));">
                <?php foreach ($meals as $meal): ?>
                    <div class="card" style="cursor: pointer;" onclick="window.parent.showMealDetails(<?php echo $meal['meal_id']; ?>)">
                        <?php if ($meal['thumbnail']): ?>
                            <img src="<?php echo htmlspecialchars($meal['thumbnail']); ?>" 
                                 alt="<?php echo htmlspecialchars($meal['name']); ?>" 
                                 style="width: 100%; height: 150px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body">
                            <h4><?php echo htmlspecialchars($meal['name']); ?></h4>
                            <?php if ($meal['category']): ?>
                                <p><small><?php echo htmlspecialchars($meal['category']); ?></small></p>
                            <?php endif; ?>
                            <?php if ($meal['measure']): ?>
                                <p><strong>Amount:</strong> <?php echo htmlspecialchars($meal['measure']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-error">
            This ingredient is not currently used in any meals.
        </div>
    <?php endif; ?>
</div>