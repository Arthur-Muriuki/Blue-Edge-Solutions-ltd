<?php 
// 1. Ensure PHP Session is running
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. FEATURE 3: PERSISTENT GUEST COOKIE LOGIC
if (!isset($_COOKIE['guest_device_id'])) {
    $guest_id = bin2hex(random_bytes(16));
    setcookie('guest_device_id', $guest_id, time() + (86400 * 30), "/");
}

if (empty($_SESSION['cart']) && isset($_COOKIE['saved_guest_cart'])) {
    $decoded_cart = json_decode($_COOKIE['saved_guest_cart'], true);
    if (is_array($decoded_cart)) {
        $_SESSION['cart'] = $decoded_cart;
    }
}

$base = $base_url ?? ''; 

// Auto-detect if current page is inside the /admin/ folder
$is_admin_area = (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false);

// PREVENT BROWSER CACHING FOR ADMIN PAGES (Fixes back-button access after logout)
if ($is_admin_area) {
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0"); // Proxies.
}

$currentPage = basename($_SERVER['PHP_SELF']);
$isUsercraft = ($currentPage === 'usercraft.php' || (isset($page_title) && strpos(strtolower($page_title), 'usercraft') !== false));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title><?php echo $page_title ?? 'Blue Edge Solutions Limited | IT Infrastructure & Support'; ?></title>
    <meta name="description" content="Professional IT support, server infrastructure, cloud management, and cybersecurity solutions.">
    
    <!-- Dynamic CSS Path -->
    <link rel="stylesheet" href="<?php echo $base; ?>assets/css/style.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<?php if ($is_admin_area): ?>
    <!-- ==================================================== -->
    <!-- SINGLE UNIFIED ADMIN HEADER (Only shown in /admin/)  -->
    <!-- ==================================================== -->
    <header style="background: #002d62; color: white; padding: 12px 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            
            <!-- Left Side: Admin Branding & Welcome Info -->
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="index.php" style="color: white; font-weight: 800; font-size: 1.25rem; text-decoration: none; display: flex; align-items: center; gap: 6px;">
                    Blue Edge<span style="color: #ff7300;">.</span> <span style="background: rgba(255,255,255,0.15); font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; text-transform: uppercase; letter-spacing: 0.5px;">Admin</span>
                </a>

                <?php if (!empty($_SESSION['admin_logged_in'])): ?>
                    <div style="display: flex; align-items: center; gap: 8px; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 15px; font-size: 0.85rem; color: #cbd5e1;">
                        <span>Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_fullname'] ?? $_SESSION['admin_username']); ?></strong></span>
                        <span style="background: #ff7300; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; font-weight: 800;">
                            <?php echo htmlspecialchars($_SESSION['admin_role'] ?? 'STAFF_ADMIN'); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Side: Navigation Links (Only if logged in) -->
            <?php if (!empty($_SESSION['admin_logged_in'])): ?>
            <nav style="display: flex; align-items: center; gap: 18px; font-size: 0.9rem; font-weight: 600;">
            <a href="index.php#orders-section" style="color: white; text-decoration: none; transition: opacity 0.2s;">Orders</a>
    <a href="manage_products.php" style="color: white; text-decoration: none; transition: opacity 0.2s;">Products</a>
    <a href="manage_articles.php" style="color: white; text-decoration: none; transition: opacity 0.2s;">Blog Posts</a>
                <?php if (($_SESSION['admin_role'] ?? '') === 'SUPER_ADMIN'): ?>
                    <a href="manage-staff.php" style="color: #38bdf8; text-decoration: none; font-weight: 700;">👥 Staff</a>
                <?php endif; ?>

                <a href="profile.php" style="color: white; text-decoration: none; transition: opacity 0.2s;">My Profile</a>
                <a href="../index.php" target="_blank" style="color: #94a3b8; text-decoration: none; font-size: 0.85rem; display: flex; align-items: center; gap: 4px; border: 1px solid #334155; padding: 4px 10px; border-radius: 6px;">
                     View Site
                </a>
                <a href="logout.php" style="color: #f87171; text-decoration: none; font-weight: 700; background: rgba(239, 68, 68, 0.1); padding: 5px 12px; border-radius: 6px;">
                    Logout
                </a>
            </nav>
            <?php endif; ?>

        </div>
    </header>

<?php else: ?>
    <!-- ==================================================== -->
    <!-- STANDARD PUBLIC CLIENT HEADER (Exact Original Code)  -->
    <!-- ==================================================== -->
    <header class="main-header">
        <div class="container nav-container">
            <!-- DYNAMIC BRAND LOGO (TEXT-BASED) -->
            <a href="<?php echo $base; ?>index.php" class="brand-logo">
                <?php if ($isUsercraft): ?>
                    UserCraft<span>.</span>
                <?php else: ?>
                    Blue Edge<span>.</span>
                <?php endif; ?>
            </a>

            <nav class="desktop-nav">
                <ul class="nav-links">
                    <li><a href="<?php echo $base; ?>index.php">Home</a></li>
                    <li><a href="<?php echo $base; ?>about.php">About Us</a></li>
                    <li><a href="<?php echo $base; ?>services.php">Services</a></li>
                    <li><a href="<?php echo $base; ?>shop.php">Shop</a></li>
                    <li><a href="<?php echo $base; ?>contact.php">Contact</a></li>
                    <li><a href="<?php echo $base; ?>blog.php">Blog</a></li>
                    <li><a href="<?php echo $base; ?>usercraft.php">UserCraft</a></li>
                    <li><a href="<?php echo $base; ?>help.php">Help Center</a></li>
                </ul>
            </nav>

            <div class="nav-actions" style="display: flex; align-items: center; gap: 15px;">
                <!-- GLOBAL HEADER CART BUTTON -->
                <button onclick="toggleCart()" style="background: none; border: none; color: #002d62; font-weight: bold; font-size: 1rem; cursor: pointer; display: flex; align-items: center; gap: 6px; transition: color 0.2s; padding: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                    (<span id="cartCount"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>)
                </button>

                <a href="<?php echo $base; ?>contact.php" class="btn btn-primary">Get in Touch</a>
                
                <button class="mobile-menu-btn" aria-label="Open Menu">
                    <i class="ph ph-list"></i>
                </button>
            </div>
        </div> 
        
        <nav class="mobile-menu">
            <ul class="mobile-nav-links">
                <li><a href="<?php echo $base; ?>index.php">Home</a></li>
                <li><a href="<?php echo $base; ?>about.php">About Us</a></li>
                <li><a href="<?php echo $base; ?>services.php">Services</a></li>
                <li><a href="<?php echo $base; ?>shop.php">Shop</a></li>
                <li><a href="<?php echo $base; ?>blog.php">Blog</a></li>
                <li><a href="<?php echo $base; ?>usercraft.php">UserCraft</a></li>
                <li><a href="#" onclick="toggleCart(); return false;" class="mobile-contact-link" style="background: #e2e8f0; color: #002d62;">View Cart (<span id="mobileCartCount"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>)</a></li>
                <li><a href="<?php echo $base; ?>contact.php" class="mobile-contact-link">Get in Touch</a></li>
            </ul>
        </nav>
    </header>
<?php endif; ?>