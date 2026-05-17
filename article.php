<?php
// 1. Initialize database connection
require_once 'includes/db_connect.php';

// 2. Get the 'slug' from the URL (e.g., article.php?slug=cybersecurity-threats)
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (!empty($slug)) {
    // Fetch the specific article matching this slug using a safe prepared statement
    $sql = "SELECT * FROM blog_posts WHERE slug = :slug LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['slug' => $slug]);
    $article = $stmt->fetch();
}

// 3. If the article doesn't exist in the database, redirect back to the blog feed
if (!$article) {
    header("Location: blog.php");
    exit();
}

// 4. Set the dynamic SEO page title based on the database content
$page_title = $article['title'] . " | Blue Edge Solutions";
include_once 'includes/header.php';
?>

<main style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; padding: 60px 20px;">
    <article style="max-width: 800px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
        
        <div style="display: flex; gap: 15px; align-items: center; margin-bottom: 20px;">
            <span style="background: #ff7300; color: white; padding: 4px 10px; font-size: 0.8rem; font-weight: bold; border-radius: 4px; text-transform: uppercase;">
                <?php echo htmlspecialchars($article['category']); ?>
            </span>
            <time style="color: #64748b; font-size: 0.9rem;">
                <?php echo date("F j, Y", strtotime($article['publish_date'])); ?>
            </time>
        </div>

        <h1 style="color: #002d62; font-size: 2.5rem; line-height: 1.2; margin: 0 0 30px 0; letter-spacing: -0.5px;">
            <?php echo htmlspecialchars($article['title']); ?>
        </h1>

        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin-bottom: 30px;">

        <div class="blog-content" style="color: #334155; font-size: 1.15rem; line-height: 1.8;">
            <?php echo $article['content']; ?>
        </div>

        <div style="margin-top: 5px; padding-top: 30px; border-top: 1px solid #e2e8f0;">
            <a href="blog.php" style="color: #002d62; font-weight: bold; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="transform: scaleX(-1);">
                    <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                </svg>
                Back to Blog Feed
            </a>
        </div>

    </article>
</main>

<?php 
include_once 'includes/footer.php'; 
?>