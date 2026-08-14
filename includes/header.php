<?php 
// Fallback if $base_url isn't defined on a root page
$base = $base_url ?? ''; 

// Detect if current page is usercraft.php
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
                    (<span id="cartCount">0</span>)
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
                <li><a href="#" onclick="toggleCart(); return false;" class="mobile-contact-link" style="background: #e2e8f0; color: #002d62;">View Cart (<span id="mobileCartCount">0</span>)</a></li>
                <li><a href="<?php echo $base; ?>contact.php" class="mobile-contact-link">Get in Touch</a></li>
            </ul>
        </nav>
    </header>