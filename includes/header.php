<?php 
// Fallback if $base_url isn't defined on a root page
$base = $base_url ?? ''; 
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
            <a href="<?php echo $base; ?>index.php" class="brand-logo">
                Blue Edge<span>.</span>
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
                </ul>
            </nav>

            <div class="nav-actions">
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
                <li><a href="<?php echo $base; ?>contact.php" class="mobile-contact-link">Get in Touch</a></li>
            </ul>
        </nav>
    </header>