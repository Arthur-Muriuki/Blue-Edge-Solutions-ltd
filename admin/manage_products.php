<?php
// 1. Session check to protect the page
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 2. Database connection
require_once '../includes/db_connect.php';

$message = '';
$error = '';

// 3. Handle Product Deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
    if ($stmt->execute(['id' => $id])) {
        $message = "Product deleted successfully.";
    } else {
        $error = "Failed to delete product.";
    }
}

// 4. Handle Adding New Product with Automatic Image Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);
    $price = floatval($_POST['price']);
    $price_label = !empty($_POST['price_label']) ? trim($_POST['price_label']) : NULL;
    $description = trim($_POST['description']);
    $badge = !empty($_POST['badge']) ? trim($_POST['badge']) : NULL;
    
    // Default image path fallback
    $image_path = 'assets/images/placeholder.jpeg';

    // Handle File Upload
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['product_image']['tmp_name'];
        $fileName = $_FILES['product_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            // Generate unique filename to avoid overwriting existing files
            $newFileName = time() . '_' . preg_replace("/[^a-zA-Z0-9\._-]/", "", $fileName);
            $uploadDir = '../assets/images/';
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $image_path = 'assets/images/' . $newFileName;
            } else {
                $error = "Error moving uploaded image to assets folder.";
            }
        } else {
            $error = "Invalid image file type. Allowed formats: JPG, JPEG, PNG, WEBP, GIF.";
        }
    }

    if (empty($error) && !empty($title) && !empty($category) && $price > 0) {
        $sql = "INSERT INTO products (title, category, price, price_label, description, image, badge) 
                VALUES (:title, :category, :price, :price_label, :description, :image, :badge)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([
            'title' => $title,
            'category' => $category,
            'price' => $price,
            'price_label' => $price_label,
            'description' => $description,
            'image' => $image_path,
            'badge' => $badge
        ])) {
            $message = "New item added to inventory successfully!";
        } else {
            $error = "Failed to add product to database.";
        }
    } elseif (empty($error)) {
        $error = "Please fill in all required fields (Title, Category, Price).";
    }
}

// 5. Fetch all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Subfolder path configuration
$base_url = '../';
$page_title = "Manage Products | Admin Dashboard";
include_once '../includes/header.php';
?>

<main style="background-color: #f8fafc; min-height: 80vh; padding: 40px 20px;">
    <div style="max-width: 1200px; margin: 0 auto;">
        
        <!-- Header Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="color: #002d62; margin: 0; font-size: 2rem;">Product Inventory Manager</h1>
                <p style="color: #64748b; margin: 5px 0 0;">Add new hardware, consumables, or service packages directly to your shop.</p>
            </div>
            <a href="index.php" style="background: #e2e8f0; color: #002d62; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">
                &larr; Back to Dashboard
            </a>
        </div>

        <!-- Alert Notifications -->
        <?php if ($message): ?>
            <div style="background: #dcfce7; color: #15803d; padding: 12px 20px; border-radius: 6px; margin-bottom: 25px; font-weight: bold;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px 20px; border-radius: 6px; margin-bottom: 25px; font-weight: bold;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px; align-items: start;">
            
            <!-- ADD PRODUCT FORM (WITH MULTIPART ENCTYPE) -->
            <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #e2e8f0;">
                <h3 style="color: #002d62; margin-top: 0; margin-bottom: 20px; font-size: 1.25rem;">Add New Product</h3>
                
                <form method="POST" action="manage_products.php" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <label style="display: block; font-weight: bold; color: #475569; margin-bottom: 5px;">Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Cisco Catalyst Switch" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: bold; color: #475569; margin-bottom: 5px;">Category *</label>
                        <select name="category" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; background: white; box-sizing: border-box;">
                            <option value="Networking">Networking</option>
                            <option value="Hardware">Hardware</option>
                            <option value="Consumables">Consumables</option>
                            <option value="Repairs">Repairs</option>
                            <option value="Management">Management</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label style="display: block; font-weight: bold; color: #475569; margin-bottom: 5px;">Price (Ksh) *</label>
                            <input type="number" step="0.01" name="price" required placeholder="25000" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                        </div>
                        <div style="flex: 1;">
                            <label style="display: block; font-weight: bold; color: #475569; margin-bottom: 5px;">Price Label</label>
                            <input type="text" name="price_label" placeholder="e.g. Per Month" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-weight: bold; color: #475569; margin-bottom: 5px;">Badge Ribbon</label>
                        <input type="text" name="badge" placeholder="e.g. SERVICE, PACKAGE, SALE" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
                    </div>

                    <!-- ATTACH IMAGE FILE -->
                    <div>
                        <label style="display: block; font-weight: bold; color: #475569; margin-bottom: 5px;">Attach Image File</label>
                        <input type="file" name="product_image" accept="image/*" style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; background: #f8fafc; box-sizing: border-box;">
                    </div>

                    <div>
                        <label style="display: block; font-weight: bold; color: #475569; margin-bottom: 5px;">Description *</label>
                        <textarea name="description" rows="3" required placeholder="Short product summary..." style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></textarea>
                    </div>

                    <button type="submit" name="add_product" style="background: #ff7300; color: white; border: none; padding: 12px; border-radius: 4px; font-weight: bold; cursor: pointer; margin-top: 10px;">
                        Save Product
                    </button>
                </form>
            </div>

            <!-- EXISTING PRODUCTS TABLE -->
            <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; overflow-x: auto;">
                <h3 style="color: #002d62; margin-top: 0; margin-bottom: 20px; font-size: 1.25rem;">Current Inventory (<?php echo count($products); ?>)</h3>

                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0; color: #64748b;">
                            <th style="padding: 10px;">Image</th>
                            <th style="padding: 10px;">Title</th>
                            <th style="padding: 10px;">Category</th>
                            <th style="padding: 10px;">Price</th>
                            <th style="padding: 10px; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $p): ?>
                                <tr style="border-bottom: 1px solid #f1f5f9;">
                                    <td style="padding: 10px;">
                                        <img src="../<?php echo htmlspecialchars($p['image']); ?>" alt="thumb" style="width: 45px; height: 45px; object-fit: contain; border-radius: 4px; background: #f8fafc; border: 1px solid #e2e8f0;">
                                    </td>
                                    <td style="padding: 12px 10px; font-weight: bold; color: #002d62;">
                                        <?php echo htmlspecialchars($p['title']); ?>
                                    </td>
                                    <td style="padding: 12px 10px; color: #475569;">
                                        <?php echo htmlspecialchars($p['category']); ?>
                                    </td>
                                    <td style="padding: 12px 10px; color: #166534; font-weight: bold;">
                                        Ksh <?php echo number_format($p['price'], 0); ?>
                                    </td>
                                    <td style="padding: 12px 10px; text-align: right;">
                                        <a href="manage_products.php?action=delete&id=<?php echo $p['id']; ?>" 
                                           onclick="return confirm('Are you sure you want to delete this product?');" 
                                           style="color: #dc2626; text-decoration: none; font-weight: bold; font-size: 0.85rem;">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" style="padding: 20px; text-align: center; color: #64748b;">No products in database.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>