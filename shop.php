<?php
// 1. Core configurations and database integration
require_once 'includes/db_connect.php';

// Fetch all products from database
try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
    $error_msg = "Unable to load shop items at this time.";
}

// 2. Inject global site header navigation
include_once 'includes/header.php';
?>

<main style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; padding-bottom: 80px;">

    <!-- HERO SECTION -->
    <section style="background: linear-gradient(135deg, #002d62 0%, #001f42 100%); color: white; padding: 60px 20px 80px 20px; text-align: center;">
        <div style="max-width: 900px; margin: 0 auto;">
            <h1 style="font-size: 2.8rem; font-weight: 700; color:#e2e8f0; margin: 10px 0 15px 0;">Hardware & Service Shop</h1>
            <p style="font-size: 1.1rem; color: #cbd5e1; max-width: 600px; margin: 0 auto;">
                Upgrade your infrastructure with enterprise-grade networking equipment, premium consumables, or book our specialized IT service packages directly.
            </p>
        </div>
    </section>

    <!-- SEARCH & FILTER BAR -->
    <div style="max-width: 1200px; margin: -30px auto 30px; padding: 0 20px; position: relative; z-index: 10;">
        <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            
            <!-- Text Search Input -->
            <div style="flex: 2; min-width: 250px;">
                <input type="text" id="shopSearch" placeholder="Search product title..." style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; box-sizing: border-box; outline: none;">
            </div>

            <!-- Category Filter Dropdown -->
            <div style="flex: 1; min-width: 180px;">
                <select id="categoryFilter" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; background: white; cursor: pointer; outline: none;">
                    <option value="all">All Categories</option>
                    <option value="Networking">Networking</option>
                    <option value="Hardware">Hardware</option>
                    <option value="Consumables">Consumables</option>
                    <option value="Repairs">Repairs</option>
                    <option value="Management">Management</option>
                </select>
            </div>

        </div>
    </div>

    <!-- PRODUCT GRID -->
    <section style="padding: 20px 20px 60px; max-width: 1200px; margin: 0 auto;">
        
        <!-- No Results Found Box -->
        <div id="noResults" style="display: none; text-align: center; padding: 50px 20px; background: white; border-radius: 8px; border: 1px dashed #cbd5e1;">
            <h3 style="color: #002d62; margin-bottom: 10px;">No Matching Items Found</h3>
            <p style="color: #64748b; margin: 0;">Try adjusting your search terms or selecting a different category.</p>
        </div>

        <div id="productGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">

            <?php if (!empty($products)): ?>
                <?php foreach ($products as $item): ?>
                    <div class="product-card" data-category="<?php echo htmlspecialchars($item['category']); ?>" style="background: #ffffff; border: 1px solid <?php echo !empty($item['badge']) ? '#002d62' : '#e2e8f0'; ?>; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02); display: flex; flex-direction: column; position: relative;">
                        
                        <!-- Dynamic Badge (SERVICE / PACKAGE / SALE) -->
                        <?php if (!empty($item['badge'])): ?>
                            <div style="position: absolute; top: 15px; right: -30px; background: #ff7300; color: white; padding: 5px 35px; transform: rotate(45deg); font-size: 0.8rem; font-weight: bold; z-index: 1;">
                                <?php echo htmlspecialchars($item['badge']); ?>
                            </div>
                        <?php endif; ?>

                        <div style="background: #f8fafc; height: 220px; display: flex; align-items: center; justify-content: center; padding: 15px;">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                        </div>

                        <div style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">
                            <span style="color: <?php echo ($item['category'] === 'Consumables') ? '#ff7300' : '#64748b'; ?>; font-size: 0.85rem; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">
                                <?php echo htmlspecialchars($item['category']); ?>
                            </span>

                            <h3 class="product-title" style="color: #002d62; font-size: 1.25rem; margin: 0 0 10px 0;">
                                <?php echo htmlspecialchars($item['title']); ?>
                            </h3>

                            <p class="product-desc" style="color: #475569; font-size: 0.95rem; line-height: 1.5; margin-bottom: 20px; flex-grow: 1;">
                                <?php echo htmlspecialchars($item['description']); ?>
                            </p>

                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="display: flex; flex-direction: column;">
                                    <?php if (!empty($item['price_label'])): ?>
                                        <span style="font-size: 0.8rem; color: #64748b;"><?php echo htmlspecialchars($item['price_label']); ?></span>
                                    <?php endif; ?>
                                    <span style="font-size: 1.4rem; font-weight: bold; color: #002d62;">
                                        Ksh <?php echo number_format($item['price'], 0); ?>
                                    </span>
                                </div>

                                <button style="background: <?php echo !empty($item['badge']) ? '#002d62' : '#ff7300'; ?>; color: white; border: none; padding: 10px 20px; border-radius: 4px; font-weight: bold; cursor: pointer;">
                                    <?php 
                                        if ($item['badge'] === 'SERVICE') {
                                            echo 'Book Now';
                                        } elseif ($item['badge'] === 'PACKAGE') {
                                            echo 'Subscribe';
                                        } else {
                                            echo 'Add to Cart';
                                        }
                                    ?>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; color: #64748b;">No products available right now.</p>
            <?php endif; ?>

        </div>
    </section>

</main>

<!-- CLIENT-SIDE TITLE-ONLY SEARCH SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('shopSearch');
    const categorySelect = document.getElementById('categoryFilter');
    const productCards = document.querySelectorAll('.product-card');
    const noResultsMsg = document.getElementById('noResults');

    function filterProducts() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedCategory = categorySelect.value;
        let visibleCount = 0;

        productCards.forEach(card => {
            const title = card.querySelector('.product-title').textContent.toLowerCase();
            const category = card.getAttribute('data-category');

            // Strictly match title only
            const matchesQuery = title.includes(query);
            const matchesCategory = (selectedCategory === 'all') || (category === selectedCategory);

            if (matchesQuery && matchesCategory) {
                card.style.display = 'flex';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        noResultsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
    }

    searchInput.addEventListener('input', filterProducts);
    categorySelect.addEventListener('change', filterProducts);
});
</script>

<?php
// 3. Inject global footer components
include_once 'includes/footer.php';
?>