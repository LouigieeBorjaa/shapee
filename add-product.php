<?php include 'helpers/functions.php'; ?>
<?php template('header.php'); ?>
<?php

use Aries\MiniFrameworkStore\Models\Category;
use Aries\MiniFrameworkStore\Models\Product;
use Carbon\Carbon;

$categories = new Category();
$product = new Product();

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $category = intval($_POST['category']);
    $image = $_FILES['image'];
    $errors = [];

    // Validate inputs
    if (empty($name)) {
        $errors[] = "Product name is required";
    }
    if (empty($description)) {
        $errors[] = "Description is required";
    }
    if ($price <= 0) {
        $errors[] = "Price must be greater than 0";
    }
    if ($category <= 0) {
        $errors[] = "Please select a category";
    }

    // Validate and process the image file
    if ($image['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowedTypes)) {
            $errors[] = "Only JPG, PNG and GIF images are allowed";
        }
        
        if ($image['size'] > 5000000) { // 5MB limit
            $errors[] = "Image size must be less than 5MB";
        }
    } else {
        $errors[] = "Please upload an image";
    }

    if (empty($errors)) {
        // Create uploads directory if it doesn't exist
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Generate unique filename
        $fileExtension = pathinfo($image["name"], PATHINFO_EXTENSION);
        $newFilename = uniqid() . '.' . $fileExtension;
        $targetFile = $targetDir . $newFilename;

        if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            // Insert the product into the database
            $product->insert([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'slug' => strtolower(str_replace(' ', '-', $name)),
                'image_path' => $targetFile,
                'category_id' => $category,
                'created_at' => Carbon::now('Asia/Manila'),
                'updated_at' => Carbon::now()
            ]);

            $message = "Product added successfully!";
        } else {
            $errors[] = "Failed to upload image";
        }
    }
}

?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 my-5">
            <h1 class="text-center">Add Product</h1>
            <p class="text-center">Fill in the details below to add a new product.</p>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0">
                        <?php foreach($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (isset($message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form action="add-product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group my-5">
                    <div class="mb-3">
                        <label for="product-name" class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="product-name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="product-description" class="form-label">Description</label>
                        <textarea class="form-control" id="product-description" name="description" rows="5" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="product-price" class="form-label">Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="product-price" name="price" step="0.01" min="0" value="<?php echo isset($_POST['price']) ? htmlspecialchars($_POST['price']) : ''; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="product-category" class="form-label">Category</label>
                        <select class="form-select" id="product-category" name="category" required>
                            <option value="">Select category</option>
                            <?php foreach($categories->getAll() as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo (isset($_POST['category']) && $_POST['category'] == $category['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="product-image" class="form-label">Product Image</label>
                        <input class="form-control" type="file" id="product-image" name="image" accept="image/*" required>
                        <div class="form-text">Max file size: 5MB. Allowed formats: JPG, PNG, GIF</div>
                    </div>
                    <div class="mb-3">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" type="submit" name="submit">Add Product</button>
                        </div>
                    </div>
                </div>
            </form>
         </div>
    </div>
</div>

<?php template('footer.php'); ?>