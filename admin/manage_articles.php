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

// 3. Handle Article Deletion
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = :id");
    if ($stmt->execute(['id' => $id])) {
        $message = "Article deleted successfully.";
    } else {
        $error = "Failed to delete article.";
    }
}

// 4. Fetch all blog posts
$stmt = $pdo->query("SELECT * FROM blog_posts ORDER BY id DESC");
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configuration
$base_url = '../';
$page_title = "Manage Articles | Admin Dashboard";
include_once '../includes/header.php';
?>

<main style="background-color: #f8fafc; min-height: 80vh; padding: 40px 20px;">
    <div style="max-width: 1000px; margin: 0 auto;">
        
        <!-- Header Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="color: #002d62; margin: 0; font-size: 2rem;">Blog Article Manager</h1>
                <p style="color: #64748b; margin: 5px 0 0;">Manage published technical insights and corporate news.</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="add_article.php" style="background: #ff7300; color: white; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">
                    + New Article
                </a>
                <a href="index.php" style="background: #e2e8f0; color: #002d62; text-decoration: none; padding: 10px 18px; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">
                    &larr; Back to Dashboard
                </a>
            </div>
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

        <!-- ARTICLES LIST TABLE -->
        <div style="background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); border: 1px solid #e2e8f0; overflow-x: auto;">
            <h3 style="color: #002d62; margin-top: 0; margin-bottom: 20px; font-size: 1.25rem;">Published Articles (<?php echo count($articles); ?>)</h3>

            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                <thead>
                    <tr style="border-bottom: 2px solid #e2e8f0; color: #64748b;">
                        <th style="padding: 10px;">Title</th>
                        <th style="padding: 10px;">Category</th>
                        <th style="padding: 10px;">Slug</th>
                        <th style="padding: 10px; text-align: right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($articles)): ?>
                        <?php foreach ($articles as $art): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 10px; font-weight: bold; color: #002d62;">
                                    <?php echo htmlspecialchars($art['title']); ?>
                                </td>
                                <td style="padding: 12px 10px; color: #475569;">
                                    <span style="background: #e0f2fe; color: #0369a1; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;">
                                        <?php echo htmlspecialchars($art['category']); ?>
                                    </span>
                                </td>
                                <td style="padding: 12px 10px; color: #64748b; font-family: monospace; font-size: 0.85rem;">
                                    <?php echo htmlspecialchars($art['slug']); ?>
                                </td>
                                <td style="padding: 12px 10px; text-align: right; display: flex; gap: 12px; justify-content: flex-end; align-items: center;">
                                    <a href="../article.php?slug=<?php echo $art['slug']; ?>" target="_blank" style="color: #64748b; text-decoration: none; font-size: 0.85rem;">
                                        View ↗
                                    </a>
                                    <a href="edit_article.php?id=<?php echo $art['id']; ?>" style="color: #002d62; text-decoration: none; font-weight: bold; font-size: 0.85rem;">
                                        Edit
                                    </a>
                                    <a href="manage_articles.php?action=delete&id=<?php echo $art['id']; ?>" 
                                       onclick="return confirm('Are you sure you want to delete this article?');" 
                                       style="color: #dc2626; text-decoration: none; font-weight: bold; font-size: 0.85rem;">
                                        Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="padding: 20px; text-align: center; color: #64748b;">No blog articles found in database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</main>

<?php include_once '../includes/footer.php'; ?>