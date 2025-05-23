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

<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item">
                <a href="category.php?id=<?php echo $productDetails['category_id']; ?>" class="text-decoration-none">
                    <?php echo htmlspecialchars($productDetails['category_name'] ?? 'Uncategorized'); ?>
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($productDetails['name']); ?></li>
        </ol>
    </nav>

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
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-gallery">
                <div class="main-image mb-3">
                    <?php if (!empty($productDetails['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($productDetails['image_path']); ?>" 
                             class="img-fluid rounded-4" 
                             alt="<?php echo htmlspecialchars($productDetails['name']); ?>">
                    <?php else: ?>
                        <div class="no-image rounded-4 d-flex align-items-center justify-content-center">
                            <i class="bi bi-image display-1 text-muted"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <?php if ($productDetails['price'] < 50): ?>
                    <div class="product-badge">Sale</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="product-details">
                <h1 class="product-title mb-3"><?php echo htmlspecialchars($productDetails['name']); ?></h1>
                
                <div class="product-meta mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-primary"><?php echo htmlspecialchars($productDetails['category_name'] ?? 'Uncategorized'); ?></span>
                        <div class="rating">
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-fill text-warning"></i>
                            <i class="bi bi-star-half text-warning"></i>
                            <span class="ms-2 text-muted">(4.5)</span>
                        </div>
                    </div>
                </div>

                <div class="product-price mb-4">
                    <div class="d-flex align-items-baseline gap-2">
                        <h2 class="text-primary mb-0"><?php echo formatCurrency($productDetails['price']); ?></h2>
                        <?php if ($productDetails['price'] < 50): ?>
                            <span class="text-muted text-decoration-line-through">
                                <?php echo formatCurrency($productDetails['price'] * 1.2); ?>
                            </span>
                            <span class="badge bg-danger ms-2">Save 20%</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-description mb-4">
                    <h5 class="mb-3">Description</h5>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($productDetails['description'])); ?></p>
                </div>

                <form action="cart-process.php" method="POST" class="product-actions">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="product_id" value="<?php echo $productDetails['id']; ?>">
                    <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                    
                    <div class="d-flex gap-3 mb-4">
                        <div class="quantity-control">
                            <div class="input-group">
                                <button type="button" class="btn btn-outline-secondary" id="decreaseQuantity">
                                    <i class="bi bi-dash"></i>
                                </button>
                                <input type="number" name="quantity" class="form-control text-center" 
                                       id="quantity" value="1" min="1">
                                <button type="button" class="btn btn-outline-secondary" id="increaseQuantity">
                                    <i class="bi bi-plus"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-cart-plus me-2"></i>Add to Cart
                        </button>
                    </div>

                    <div class="product-features">
                        <div class="d-flex gap-4">
                            <div class="feature">
                                <i class="bi bi-truck text-primary"></i>
                                <span>Free Shipping</span>
                            </div>
                            <div class="feature">
                                <i class="bi bi-shield-check text-primary"></i>
                                <span>Secure Payment</span>
                            </div>
                            <div class="feature">
                                <i class="bi bi-arrow-return-left text-primary"></i>
                                <span>Easy Returns</span>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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