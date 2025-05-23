<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

if(!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if(isset($_GET['action']) && isset($_GET['id'])) {
    $productId = $_GET['id'];
    if(!isset($_SESSION['wishlist'])) {
        $_SESSION['wishlist'] = [];
    }
    
    if($_GET['action'] === 'add') {
        if(!in_array($productId, $_SESSION['wishlist'])) {
            $_SESSION['wishlist'][] = $productId;
        }
    } else if($_GET['action'] === 'remove') {
        $_SESSION['wishlist'] = array_diff($_SESSION['wishlist'], [$productId]);
    }
    
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

use Aries\MiniFrameworkStore\Models\Product;
$products = new Product();

?>

<div class="container my-5">
    <div class="row">
        <div class="col-md-12">
            <h1>My Wishlist</h1>
            <?php if(empty($_SESSION['wishlist'])): ?>
                <p>Your wishlist is empty.</p>
                <a href="index.php" class="btn btn-primary">Browse Products</a>
            <?php else: ?>
                <div class="row">
                    <?php foreach($_SESSION['wishlist'] as $productId): 
                        $product = $products->getById($productId);
                        if($product): ?>
                            <div class="col-md-4 mb-4">
                                <div class="card">
                                    <img src="<?php echo $product['image_path'] ?>" class="card-img-top" alt="<?php echo $product['name'] ?>">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $product['name'] ?></h5>
                                        <h6 class="card-subtitle mb-2 text-body-secondary"><?php echo formatCurrency($product['price']) ?></h6>
                                        <p class="card-text"><?php echo substr($product['description'], 0, 100) ?>...</p>
                                        <div class="d-flex gap-2">
                                            <a href="product.php?id=<?php echo $product['id'] ?>" class="btn btn-primary">View Details</a>
                                            <a href="wishlist.php?action=remove&id=<?php echo $product['id'] ?>" class="btn btn-danger">Remove</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif;
                    endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php template('footer.php'); ?> 