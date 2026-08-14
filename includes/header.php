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

    <style>
        .brand-logo {
            white-space: nowrap;
        }

        .desktop-nav {
            display: flex;
            justify-content: center;
            flex-grow: 1;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            font-size: 1.8rem;
            cursor: pointer;
            color: inherit;
        }

        /* Mobile Menu Scrollable Container */
        .mobile-menu {
            display: none;
            width: 100%;
            background-color: <?php echo $isUsercraft ? '#111827' : '#ffffff'; ?>;
            border-top: 1px solid <?php echo $isUsercraft ? '#334155' : '#e2e8f0'; ?>;
            padding: 15px 20px;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
            max-height: calc(100vh - 80px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }

        .mobile-menu.active {
            display: block !important;
        }

        .mobile-nav-links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .mobile-nav-links a {
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            color: <?php echo $isUsercraft ? '#f8fafc' : '#002d62'; ?>;
            display: block;
            padding: 8px 10px;
            border-radius: 6px;
        }

        .mobile-nav-links a:hover {
            color: #ff7300;
            background-color: <?php echo $isUsercraft ? '#1e293b' : '#f1f5f9'; ?>;
        }

        /* Responsive Breakpoints (< 992px) */
        @media (max-width: 992px) {
            .desktop-nav {
                display: none !important;
            }
            .mobile-menu-btn {
                display: flex !important;
                align-items: center;
                justify-content: center;
            }
            .usercraft-contact-info {
                display: none !important;
            }
        }

        <?php if ($isUsercraft): ?>
        /* Dark Theme overrides specifically for UserCraft navbar */
        .main-header.usercraft-header {
            background-color: #000000 !important;
            border-bottom: 2px solid #ff7300;
            padding: 10px 0;
        }
        .usercraft-header .nav-links a {
            color: #f8fafc !important;
        }
        .usercraft-header .nav-links a:hover {
            color: #ff7300 !important;
        }
        .usercraft-header .cart-btn,
        .usercraft-header .mobile-menu-btn {
            color: #ffffff !important;
        }
        <?php endif; ?>
    </style>
</head>
<body>

    <header class="main-header <?php echo $isUsercraft ? 'usercraft-header' : ''; ?>">
        <div class="container nav-container" style="display: flex; align-items: center; justify-content: space-between; gap: 15px;">
            
            <!-- 1. FAR LEFT: BRAND LOGO -->
            <?php if ($isUsercraft): ?>
                <a href="<?php echo $base; ?>index.php" style="display: flex; align-items: center; text-decoration: none; flex-shrink: 0;">
                    <img src="<?php echo $base; ?>assets/images/UserCraft.png" alt="UserCraft Consult" style="height: 100px; width: auto; object-fit: contain; display: block;">
                </a>
            <?php else: ?>
                <a href="<?php echo $base; ?>index.php" class="brand-logo">
                    Blue Edge<span>.</span>
                </a>
            <?php endif; ?>

            <!-- 2. MIDDLE: DESKTOP NAVIGATION -->
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

            <!-- 3. FAR RIGHT: ACTIONS & MOBILE MENU TOGGLE -->
            <div class="nav-actions" style="display: flex; align-items: center; gap: 18px; flex-shrink: 0;">
                
                <!-- CART BUTTON -->
                <button onclick="toggleCart()" class="cart-btn" style="background: none; border: none; color: <?php echo $isUsercraft ? '#ffffff' : '#002d62'; ?>; font-weight: bold; font-size: 1rem; cursor: pointer; display: flex; align-items: center; gap: 6px; padding: 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16"><path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .49.598l-1 5a.5.5 0 0 1-.465.401l-9.397.472L4.415 11H13a.5.5 0 0 1 0 1H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l.84 4.479 9.144-.459L13.89 4H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                    (<span id="cartCount">0</span>)
                </button>

                <!-- CONTACT DETAILS / CTA -->
                <?php if ($isUsercraft): ?>
                    <div class="usercraft-contact-info" style="display: flex; flex-direction: column; font-size: 0.78rem; color: #cbd5e1; line-height: 1.35; text-align: right;">
                        <strong style="font-size: 0.95rem; color: #ffffff;">UserCraft Consult</strong>
                        <span>P.O Box 4119-00100 Nairobi</span>
                        <span>Caxton House 1st Floor, Kenyatta Ave</span>
                        <span>Email: usercraft@gmail.com</span>
                        <span>Phone: 0722-146-546</span>
                    </div>
                <?php else: ?>
                    <a href="<?php echo $base; ?>contact.php" class="btn btn-primary">Get in Touch</a>
                <?php endif; ?>

                <!-- HAMBURGER BUTTON -->
                <button class="mobile-menu-btn" aria-label="Open Menu" onclick="toggleMobileMenu()">
                    <i class="ph ph-list"></i>
                </button>
            </div>
        </div> 
        
        <!-- MOBILE MENU OVERLAY -->
        <nav class="mobile-menu" id="mobileMenu">
            <ul class="mobile-nav-links">
                <li><a href="<?php echo $base; ?>index.php">Home</a></li>
                <li><a href="<?php echo $base; ?>about.php">About Us</a></li>
                <li><a href="<?php echo $base; ?>services.php">Services</a></li>
                <li><a href="<?php echo $base; ?>shop.php">Shop</a></li>
                <li><a href="<?php echo $base; ?>blog.php">Blog</a></li>
                <li><a href="<?php echo $base; ?>usercraft.php">UserCraft</a></li>
                <li><a href="<?php echo $base; ?>help.php">Help Center</a></li>
                <li style="margin-top: 6px;"><a href="#" onclick="toggleCart(); return false;" style="background: #e2e8f0; color: #002d62; text-align: center;">View Cart (<span id="mobileCartCount">0</span>)</a></li>
                <li><a href="<?php echo $base; ?>contact.php" style="background: #002d62; color: #ffffff; text-align: center;">Get in Touch</a></li>
            </ul>
        </nav>
    </header>

    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            if (menu) {
                menu.classList.toggle('active');
            }
        }
    </script>