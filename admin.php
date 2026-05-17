<?php
session_start();

// The Bouncer
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit(); 
}

require_once 'includes/db_connect.php';

$message = '';

// 1. Handle New Article Publication
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['publish_article'])) {
    $title = trim($_POST['title']);
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $_POST['slug'])));
    $category = trim($_POST['category']);
    $excerpt = trim($_POST['excerpt']);
    $content = trim($_POST['content']);
    $meta_desc = trim($_POST['meta_description']);
    $publish_date = date('Y-m-d'); 

    $sql = "INSERT INTO blog_posts (title, slug, excerpt, content, category, publish_date, meta_description) 
            VALUES (:title, :slug, :excerpt, :content, :category, :publish_date, :meta_description)";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute(['title' => $title, 'slug' => $slug, 'excerpt' => $excerpt, 'content' => $content, 'category' => $category, 'publish_date' => $publish_date, 'meta_description' => $meta_desc]);
        $message = "<div style='background: #dcfce3; color: #166534; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;'>Success! Your article has been published.</div>";
    } catch (PDOException $e) {
        $message = "<div style='background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>Error: " . $e->getMessage() . "</div>";
    }
}

// 2. Handle Article Deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_article'])) {
    $delete_id = $_POST['delete_id'];
    
    $sql = "DELETE FROM blog_posts WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    
    try {
        $stmt->execute(['id' => $delete_id]);
        $message = "<div style='background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;'>Article successfully deleted.</div>";
    } catch (PDOException $e) {
        $message = "<div style='background: #fee2e2; color: #b91c1c; padding: 15px; border-radius: 4px; margin-bottom: 20px;'>Error deleting article: " . $e->getMessage() . "</div>";
    }
}

// Fetch all articles to display in the Manage section
$stmt = $pdo->query("SELECT id, title, publish_date FROM blog_posts ORDER BY publish_date DESC");
$all_articles = $stmt->fetchAll();

$page_title = "Admin Dashboard | Blue Edge Solutions";
include_once 'includes/header.php';
?>

<main style="background-color: #f8fafc; min-height: 80vh; padding: 40px 20px;">
    <div style="max-width: 1000px; margin: 0 auto;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="color: #002d62; margin: 0;">Admin Dashboard</h1>
            <span style="background: #e2e8f0; color: #475569; padding: 5px 15px; border-radius: 20px; font-weight: bold;">
                Logged in as: <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
            </span>
        </div>

        <?php echo $message; ?>

        <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 40px;">
            <h2 style="color: #ff7300; margin-top: 0; margin-bottom: 20px;">Publish New Article</h2>
            <form method="POST" action="admin.php" style="display: flex; flex-direction: column; gap: 20px;">
                <div>
                    <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">Article Title</label>
                    <input type="text" name="title" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">
                </div>
                <div style="display: flex; gap: 20px;">
                    <div style="flex: 1;">
                        <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">URL Slug (No spaces)</label>
                        <input type="text" name="slug" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                    <div style="flex: 1;">
                        <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">Category</label>
                        <input type="text" name="category" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px;">
                    </div>
                </div>
                <div>
                    <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">Short Excerpt</label>
                    <textarea name="excerpt" required rows="2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;"></textarea>
                </div>
                <div>
                    <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">SEO Meta Description</label>
                    <textarea name="meta_description" required rows="2" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-family: inherit;"></textarea>
                </div>
                <div>
                    <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">Full HTML Content</label>
                    <textarea name="content" required rows="6" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; font-family: monospace;"></textarea>
                </div>
                <button type="submit" name="publish_article" style="background: #ff7300; color: white; border: none; padding: 15px; font-weight: bold; border-radius: 4px; cursor: pointer; font-size: 1.1rem;">
                    Publish Article
                </button>
            </form>
        </div>

        <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <h2 style="color: #002d62; margin-top: 0; margin-bottom: 20px;">Manage Articles</h2>
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 12px; color: #475569;">Date</th>
                        <th style="padding: 12px; color: #475569;">Title</th>
                        <th style="padding: 12px; color: #475569;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_articles as $post): ?>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px; color: #64748b;"><?php echo date('M d, Y', strtotime($post['publish_date'])); ?></td>
                        <td style="padding: 12px; font-weight: bold; color: #0f172a;"><?php echo htmlspecialchars($post['title']); ?></td>
                        
                        <td style="padding: 12px; display: flex; gap: 15px; align-items: center;">
                            <a href="edit.php?id=<?php echo $post['id']; ?>" style="color: #002d62; font-weight: bold; text-decoration: none;">✏️ Edit</a>
                            
                            <form method="POST" action="admin.php" style="margin: 0;" onsubmit="return confirm('⚠️ Are you sure you want to completely delete this article? This cannot be undone.');">
                                <input type="hidden" name="delete_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" name="delete_article" style="background: none; border: none; color: #dc2626; font-weight: bold; cursor: pointer; padding: 0; font-size: 1rem;">
                                    🗑️ Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<?php include_once 'includes/footer.php'; ?>