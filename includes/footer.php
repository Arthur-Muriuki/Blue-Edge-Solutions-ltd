<?php 
// Fallback if $base_url isn't defined on a root page
$base = $base_url ?? ''; 
?>
<footer class="main-footer">
    <div class="container footer-container">
        <div class="footer-brand">
            <h3>Blue Edge Solutions</h3>
            <p>Enterprise-grade IT Infrastructure, Cloud Management, and Cyber Security.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?php echo $base; ?>services.php">Our Services</a></li>
                <li><a href="<?php echo $base; ?>shop.php">Hardware Shop</a></li>
                <li><a href="<?php echo $base; ?>contact.php">Support</a></li>
                <li><a href="<?php echo $base; ?>usercraft.php">UserCraft</a></li>
                <li><a href="<?php echo $base; ?>help.php">Help Center</a></li>
                <li><a href="<?php echo $base; ?>admin/login.php">Staff Access</a></li>
            </ul>
        </div>
        <div class="footer-contact">
            <h4>Contact Us</h4>
            <p><i class="ph ph-envelope-simple"></i> info@blueedge-sl.com</p>
            <p><i class="ph ph-phone"></i> +254 722 942 293 / +254 733 775 544</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Blue Edge Solutions Limited. All rights reserved.</p>
    </div>
</footer>

<a href="https://wa.me/254722942293" class="whatsapp-float" target="_blank" rel="noopener noreferrer" aria-label="Chat with us on WhatsApp">
    <i class="ph-fill ph-whatsapp-logo"></i>
</a>

<!-- Dynamic JavaScript Path -->
<script src="<?php echo $base; ?>assets/js/main.js"></script>
</body>
</html>