<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Aries\MiniFrameworkStore\Models\Product;
use Aries\MiniFrameworkStore\Models\Category;

$products = new Product();
$categories = new Category();

// Get category ID from URL
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get category details
$category = $categories->getById($categoryId);

if (!$category) {
    header('Location: index.php');
    exit();
}

// Get products in this category
$categoryProducts = $products->getByCategory($categoryId);

?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1><?php echo htmlspecialchars($category['name']); ?></h1>
            <?php if (!empty($category['description'])): ?>
                <p class="lead"><?php echo htmlspecialchars($category['description']); ?></p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row mt-4">
        <?php if (empty($categoryProducts)): ?>
            <div class="col-12">
                <p class="text-center">No products found in this category.</p>
            </div>
        <?php else: ?>
            <?php foreach ($categoryProducts as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <img src="<?php echo $product['image_path'] ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo formatCurrency($product['price']); ?></h6>
                            <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="d-flex gap-2">
                                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">View Details</a>
                                <a href="#" class="btn btn-success add-to-cart" data-productid="<?php echo $product['id']; ?>" data-quantity="1">Add to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php template('footer.php'); ?>