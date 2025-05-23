<?php
require_once 'helpers/functions.php';
require_once 'vendor/autoload.php';

use Aries\MiniFrameworkStore\Models\Product;

$product = new Product();
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

$productDetails = $product->getBySlug($slug);

if (!$productDetails) {
    header('Location: index.php');
    exit;
}

template('header.php');
?>

<div class="container mt-4">
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <?php if (!empty($productDetails['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($productDetails['image_path']); ?>" 
                     class="img-fluid rounded" 
                     alt="<?php echo htmlspecialchars($productDetails['name']); ?>">
            <?php else: ?>
                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 400px;">
                    <span class="text-muted">No image available</span>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h1 class="mb-3"><?php echo htmlspecialchars($productDetails['name']); ?></h1>
            <p class="text-muted mb-2">
                Category: <?php echo htmlspecialchars($productDetails['category_name'] ?? 'Uncategorized'); ?>
            </p>
            <h3 class="text-primary mb-4"><?php echo formatCurrency($productDetails['price']); ?></h3>
            <div class="mb-4">
                <h5>Description</h5>
                <p><?php echo nl2br(htmlspecialchars($productDetails['description'])); ?></p>
            </div>
            <form action="cart-process.php" method="POST" class="d-flex gap-3">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $productDetails['id']; ?>">
                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                
                <div class="input-group" style="width: 150px;">
                    <button type="button" class="btn btn-outline-secondary" id="decreaseQuantity">-</button>
                    <input type="number" name="quantity" class="form-control text-center" id="quantity" value="1" min="1">
                    <button type="button" class="btn btn-outline-secondary" id="increaseQuantity">+</button>
                </div>
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decreaseQuantity');
    const increaseBtn = document.getElementById('increaseQuantity');

    decreaseBtn.addEventListener('click', () => {
        const currentValue = parseInt(quantityInput.value);
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    });

    increaseBtn.addEventListener('click', () => {
        const currentValue = parseInt(quantityInput.value);
        quantityInput.value = currentValue + 1;
    });
});
</script>

<?php template('footer.php'); ?>