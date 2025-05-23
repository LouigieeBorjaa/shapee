<?php
require_once 'helpers/functions.php';
require_once 'vendor/autoload.php';

use Aries\MiniFrameworkStore\Models\Product;

template('header.php');

$product = new Product();
$search = $_GET['search'] ?? '';
$category = isset($_GET['category']) && $_GET['category'] !== '' ? (int)$_GET['category'] : '';
$products = $product->getAll($search, $category);
$categories = $product->getCategories();
?>

<!-- Featured Categories Section -->
<section class="featured-categories py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Shop by Category</h2>
            <p class="section-subtitle">Browse our wide range of products</p>
        </div>
        <div class="row g-4">
            <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                <div class="col-6 col-md-3">
                    <a href="category.php?id=<?php echo $cat['id']; ?>" class="category-card text-decoration-none">
                        <div class="card border-0 rounded-4 h-100">
                            <div class="card-body text-center p-4">
                                <div class="category-icon mb-3">
                                    <i class="bi bi-tag fs-1"></i>
                                </div>
                                <h5 class="card-title mb-0"><?php echo htmlspecialchars($cat['name']); ?></h5>                               
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Main Content -->
<div class="container-fluid px-4 py-5">
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

    <!-- Search and Filter Section -->
    <div class="search-section mb-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <form action="" method="GET" class="search-form">
                        <div class="input-group input-group-lg shadow-sm">
                            <span class="input-group-text border-0 bg-white">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" class="form-control border-0" name="search" 
                                   placeholder="What are you looking for?" 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <select class="form-select border-0" name="category" style="max-width: 200px;">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" 
                                            <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-dark px-4">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    <div class="container">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="bi bi-search display-1 text-muted mb-3"></i>
                    <h3 class="mb-3">No Products Found</h3>
                    <p class="text-muted">Try adjusting your search criteria</p>
                    <button class="btn btn-outline-primary mt-3" onclick="clearSearch()">
                        <i class="bi bi-x-circle me-2"></i>Clear Search
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="product-card h-100">
                            <a href="product.php?slug=<?php echo htmlspecialchars($product['slug']); ?>" 
                               class="product-link text-decoration-none">
                                <div class="product-image position-relative">
                                    <?php if (!empty($product['image_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['image_path']); ?>" 
                                             class="img-fluid rounded-4" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <?php else: ?>
                                        <div class="no-image rounded-4">
                                            <i class="bi bi-image"></i>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($product['price'] < 50): ?>
                                        <div class="product-badge">Sale</div>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info p-3">
                                    <div class="category mb-2">
                                        <small class="text-muted"><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></small>
                                    </div>
                                    <h5 class="product-title mb-2 text-dark">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </h5>
                                    <div class="product-price mb-3">
                                        <span class="h5 mb-0"><?php echo formatCurrency($product['price']); ?></span>
                                        <?php if ($product['price'] < 50): ?>
                                            <small class="text-muted text-decoration-line-through ms-2">
                                                <?php echo formatCurrency($product['price'] * 1.2); ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                            <div class="product-actions">
                                <button class="btn btn-light btn-sm rounded-circle" title="Add to Wishlist">
                                    <i class="bi bi-heart"></i>
                                </button>
                                <button class="btn btn-light btn-sm rounded-circle" title="Share">
                                    <i class="bi bi-share"></i>
                                </button>
                            </div>
                            <form action="cart-process.php" method="POST" class="d-grid px-3 pb-3">
                                <input type="hidden" name="action" value="add">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="redirect" value="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                <button type="submit" class="btn btn-outline-dark w-100">
                                    <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function clearSearch() {
    window.location.href = window.location.pathname;
}
</script>

<?php template('footer.php'); ?>