<?php
// 1. Session check to protect the page
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once '../includes/db_connect.php';

// Quick metrics overview
$product_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

$base_url = '../';
$page_title = "Admin Control Dashboard | Blue Edge Solutions";
include_once '../includes/header.php';
?>

<main style="background-color: #f8fafc; min-height: 80vh; padding: 50px 20px;">
    <div style="max-width: 1000px; margin: 0 auto;">
        
        <!-- Welcome Hero Box -->
        <div style="background: linear-gradient(135deg, #002d62 0%, #001f42 100%); color: white; padding: 35px 30px; border-radius: 12px; margin-bottom: 40px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
            <div>
                <h1 style="margin: 0 0 10px 0; font-size: 2rem;">Welcome back, <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>! </h1>
                <p style="margin: 0; color: #cbd5e1; font-size: 1.05rem;">Select a tool below to manage your website hardware shop or editorial content.</p>
            </div>
            <a href="logout.php" style="background: rgba(255,255,255,0.15); color: white; text-decoration: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; border: 1px solid rgba(255,255,255,0.3); transition: background 0.2s;">
                Log Out
            </a>
        </div>

        <!-- Dashboard Selection Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">

            <!-- SHOP INVENTORY CARD -->
            <div style="background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="background: #eff6ff; color: #2563eb; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 20px;">
                        <i class="ph ph-shopping-bag"></i>
                    </div>
                    <h3 style="color: #002d62; margin: 0 0 10px 0; font-size: 1.3rem;">Shop Inventory</h3>
                    <p style="color: #64748b; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
                        Add new hardware, consumables, or service packages to your online shop. (Currently <?php echo $product_count; ?> active items)
                    </p>
                </div>
                <a href="manage_products.php" style="background: #ff7300; color: white; text-align: center; text-decoration: none; padding: 12px; border-radius: 6px; font-weight: bold; display: block;">
                    Manage Shop &rarr;
                </a>
            </div>

            <!-- BLOG & ARTICLES CARD -->
            <div style="background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="background: #f0fdf4; color: #16a34a; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 20px;">
                        <i class="ph ph-article"></i>
                    </div>
                    <h3 style="color: #002d62; margin: 0 0 10px 0; font-size: 1.3rem;">Blog Articles</h3>
                    <p style="color: #64748b; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
                        Write, edit, and publish new technical blog posts and industry insights for Blue Edge clients.
                    </p>
                </div>
               <a href="manage_articles.php" style="background: #002d62; color: white; text-align: center; text-decoration: none; padding: 12px; border-radius: 6px; font-weight: bold; display: block;">
                Manage Articles &rarr;
                </a>
            </div>

            <!-- LIVE SITE PREVIEW CARD -->
            <div style="background: white; border-radius: 10px; border: 1px solid #e2e8f0; padding: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <div style="background: #fefce8; color: #ca8a04; width: 50px; height: 50px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 20px;">
                        <i class="ph ph-globe"></i>
                    </div>
                    <h3 style="color: #002d62; margin: 0 0 10px 0; font-size: 1.3rem;">View Public Website</h3>
                    <p style="color: #64748b; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px;">
                        Leave the admin portal and view the live website homepage as a client.
                    </p>
                </div>
                <a href="../index.php" target="_blank" style="background: #e2e8f0; color: #002d62; text-align: center; text-decoration: none; padding: 12px; border-radius: 6px; font-weight: bold; display: block;">
                    Open Website &rarr;
                </a>
            </div>

        </div>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>