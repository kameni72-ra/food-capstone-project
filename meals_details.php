<?php
require_once 'config.php';

$mealId = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM meals WHERE meal_id = ?");
$stmt->execute([$mealId]);
$meal = $stmt->fetch();

if (!$meal) {
    echo '<div class="alert alert-error">Meal not found</div>';
    exit;
}

// Get ingredients with measures
$stmt = $pdo->prepare("
    SELECT i.name, mi.measure 
    FROM meal_ingredients mi 
    JOIN ingredients i ON mi.ingredient_id = i.id 
    WHERE mi.meal_id = ?
    ORDER BY i.name
");
$stmt->execute([$mealId]);
$ingredients = $stmt->fetchAll();
?>

<div class="meal-details">
    <?php if ($meal['thumbnail']): ?>
        <img src="<?php echo htmlspecialchars($meal['thumbnail']); ?>" 
             alt="<?php echo htmlspecialchars($meal['name']); ?>" 
             style="max-width: 100%; height: auto; border-radius: 10px; margin-bottom: 1rem;">
    <?php endif; ?>
    
    <h2><?php echo htmlspecialchars($meal['name']); ?></h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0;">
        <?php if ($meal['category']): ?>
            <div><strong>Category:</strong> <?php echo htmlspecialchars($meal['category']); ?></div>
        <?php endif; ?>
        
        <?php if ($meal['area']): ?>
            <div><strong>Cuisine:</strong> <?php echo htmlspecialchars($meal['area']); ?></div>
        <?php endif; ?>
        
        <?php if ($meal['created_at']): ?>
            <div><strong>Added:</strong> <?php echo date('M j, Y', strtotime($meal['created_at'])); ?></div>
        <?php endif; ?>
    </div>

    <?php if (!empty($ingredients)): ?>
        <div style="margin: 1.5rem 0;">
            <h3>Ingredients</h3>
            <ul style="columns: 2; list-style: none; padding: 0;">
                <?php foreach ($ingredients as $ingredient): ?>
                    <li style="padding: 0.25rem 0; break-inside: avoid;">
                        <strong><?php echo htmlspecialchars($ingredient['name']); ?></strong>
                        <?php if ($ingredient['measure']): ?>
                            - <?php echo htmlspecialchars($ingredient['measure']); ?>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($meal['instructions']): ?>
        <div style="margin: 1.5rem 0;">
            <h3>Instructions</h3>
            <div style="white-space: pre-line; line-height: 1.6; background: #f8f9fa; padding: 1rem; border-radius: 5px;">
                <?php echo htmlspecialchars($meal['instructions']); ?>
            </div>
        </div>
    <?php endif; ?>

    <div style="margin: 1.5rem 0; display: flex; gap: 1rem;">
        <?php if ($meal['youtube_url']): ?>
            <a href="<?php echo htmlspecialchars($meal['youtube_url']); ?>" 
               target="_blank" class="btn btn-danger">Watch on YouTube</a>
        <?php endif; ?>
        
        <?php if ($meal['source_url']): ?>
            <a href="<?php echo htmlspecialchars($meal['source_url']); ?>" 
               target="_blank" class="btn">View Original Recipe</a>
        <?php endif; ?>
    </div>
</div>