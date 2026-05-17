<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit(); 
}

require_once 'includes/db_connect.php';
$message = '';

// Check if the form was submitted to UPDATE the article
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_article'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug'])));
    $category = trim($_POST['category']);
    $excerpt = trim($_POST['excerpt']);
    $content = trim($_POST['content']);
    $meta_desc = trim($_POST['meta_description']);

    $sql = "UPDATE blog_posts SET title = :title, slug = :slug, category = :category, excerpt = :excerpt, content = :content, meta_description = :meta_desc WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute(['title' => $title, 'slug' => $slug, 'category' => $category, 'excerpt' => $excerpt, 'content' => $content, 'meta_desc' => $meta_desc, 'id' => $id]);
        $message = "<div style='background: #dcfce3; color: #166534; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;'>Article updated successfully! <a href='admin.php' style='color: #166534; text-decoration: underline;'>Back to Dashboard</a></div>";
    } catch (PDOException $e) {
        $message = "<div style='background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch the existing article data to fill the form
$article_id = $_GET['id'] ?? null;
if (!$article_id) { header("Location: admin.php"); exit(); }

$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = :id");
$stmt->execute(['id' => $article_id]);
$article = $stmt->fetch();

if (!$article) { echo "Article not found."; exit(); }

$page_title = "Edit Article | Blue Edge Solutions";
include_once 'includes/header.php';
?>

<main style="background-color: #f8fafc; min-height: 80vh; padding: 40px 20px;">
    <div style="max-width: 800px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="color: #002d62; margin: 0;">Edit Article</h1>
            <a href="admin.php" style="color: #475569; font-weight: bold; text-decoration: none;">&larr; Back to Dashboard</a>
        </div>

        <?php echo $message; ?>

        <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <form method="POST" action="edit.php?id=<?php echo $article['id']; ?>" style="display: flex; flex-direction: column; gap: 20px;">
                <input type="hidden" name="id" value="<?php echo $article['id']; ?>">
                
                <div>
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Article Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">URL Slug</label>
                        <input type="text" name="slug" value="<?php echo htmlspecialchars($article['slug']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-weight: bold; display: block; margin-bottom: 5px;">Category</label>
                        <input type="text" name="category" value="<?php echo htmlspecialchars($article['category']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                </div>
                <div>
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Short Excerpt</label>
                    <textarea name="excerpt" required rows="2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;"><?php echo htmlspecialchars($article['excerpt']); ?></textarea>
                </div>
                <div>
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">SEO Meta Description</label>
                    <textarea name="meta_description" required rows="2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;"><?php echo htmlspecialchars($article['meta_description']); ?></textarea>
                </div>
                <div>
                    <label style="font-weight: bold; display: block; margin-bottom: 5px;">Full HTML Content</label>
                    <textarea name="content" required rows="10" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-family: monospace;"><?php echo htmlspecialchars($article['content']); ?></textarea>
                </div>
                <button type="submit" name="update_article" style="background: #002d62; color: white; border: none; padding: 15px; font-weight: bold; border-radius: 4px; cursor: pointer; font-size: 1.1rem;">
                    Update Article
                </button>
            </form>
        </div>

    </div>
</main>

<?php include_once 'includes/footer.php'; ?>