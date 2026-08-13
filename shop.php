<?php
require_once 'includes/db_connect.php';

try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}

$page_title = "Shop & Services | Blue Edge Solutions";
include_once 'includes/header.php';
?>

<main style="font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; background-color: #f8fafc; padding-bottom: 80px; min-height: 80vh;">

    <!-- HERO BANNER -->
    <section style="background: linear-gradient(135deg, #002d62 0%, #001f42 100%); color: white; padding: 60px 20px; text-align: center;">
        <div style="max-width: 900px; margin: 0 auto;">
            <h1 style="font-size: 2.8rem; font-weight: 700; margin: 0 0 15px 0;">Products, Services & Subscriptions</h1>
            <p style="font-size: 1.1rem; color: #cbd5e1; max-width: 600px; margin: 0 auto;">Shop hardware, book technician services, or subscribe to managed cloud support plans.</p>
        </div>
    </section>

    <!-- SEARCH & FILTER BAR -->
    <div style="max-width: 1200px; margin: -30px auto 30px; padding: 0 20px; position: relative; z-index: 10;">
        <div style="background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 2; min-width: 250px;">
                <input type="text" id="shopSearch" placeholder="Search product or service title..." style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; box-sizing: border-box; outline: none;">
            </div>
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

    <!-- PRODUCTS GRID -->
    <section style="padding: 20px 20px 40px; max-width: 1200px; margin: 0 auto;">
        <div id="noResults" style="display: none; text-align: center; padding: 50px 20px; background: white; border-radius: 8px; border: 1px dashed #cbd5e1;">
            <h3 style="color: #002d62; margin-bottom: 10px;">No Matching Items Found</h3>
        </div>

        <div id="productGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
            <?php if (!empty($products)): ?>
                <?php foreach ($products as $item): ?>
                    <?php 
                        $item_type = $item['item_type'] ?? 'product'; 
                        $safe_title = htmlspecialchars(addslashes($item['title']));
                    ?>
                    <div class="product-card" data-category="<?php echo htmlspecialchars($item['category'] ?? ''); ?>" style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column;">
                        
                        <div style="background: #f8fafc; height: 220px; padding: 15px; display: flex; align-items: center; justify-content: center; position: relative;">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="max-height: 100%; object-fit: contain;">
                            
                            <!-- TYPE BADGE -->
                            <?php if ($item_type === 'subscription'): ?>
                                <span style="position: absolute; top: 12px; right: 12px; background: #0284c7; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">Monthly Plan</span>
                            <?php elseif ($item_type === 'booking'): ?>
                                <span style="position: absolute; top: 12px; right: 12px; background: #7c3aed; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">Service</span>
                            <?php endif; ?>
                        </div>

                        <div style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 class="product-title" style="color: #002d62; font-size: 1.2rem; margin: 0 0 10px 0;"><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p style="color: #475569; font-size: 0.9rem; flex-grow: 1; margin-bottom: 20px;"><?php echo htmlspecialchars($item['description']); ?></p>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                                <span style="font-size: 1.2rem; font-weight: bold; color: #002d62;">
                                    Ksh <?php echo number_format($item['price'], 0); ?>
                                    <?php echo ($item_type === 'subscription') ? '<small style="font-size:0.75rem; font-weight:normal; color:#64748b;">/mo</small>' : ''; ?>
                                </span>
                                
                                <!-- UNIFIED SMART CART BUTTONS -->
                                <?php if ($item_type === 'subscription'): ?>
                                    <button onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo $safe_title; ?>', <?php echo $item['price']; ?>, 'subscription', 1)" 
                                            style="background: #0284c7; color: white; border: none; padding: 10px 14px; border-radius: 4px; font-weight: bold; cursor: pointer; transition: background 0.2s;">
                                        Subscribe
                                    </button>
                                <?php elseif ($item_type === 'booking'): ?>
                                    <button onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo $safe_title; ?>', <?php echo $item['price']; ?>, 'booking', 1)" 
                                            style="background: #7c3aed; color: white; border: none; padding: 10px 14px; border-radius: 4px; font-weight: bold; cursor: pointer; transition: background 0.2s;">
                                        Book Service
                                    </button>
                                <?php else: ?>
                                    <button onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo $safe_title; ?>', <?php echo $item['price']; ?>, 'product', 1)" 
                                            style="background: #ff7300; color: white; border: none; padding: 10px 14px; border-radius: 4px; font-weight: bold; cursor: pointer; transition: background 0.2s;">
                                        Add to Cart
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

</main>

<!-- Unified Cart Engine -->
<script src="js/carts.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Live Search & Category Filter Logic
        const searchInput = document.getElementById('shopSearch');
        const categorySelect = document.getElementById('categoryFilter');
        const productCards = document.querySelectorAll('.product-card');
        const noResultsMsg = document.getElementById('noResults');

        if (searchInput && categorySelect) {
            function filterProducts() {
                const query = searchInput.value.toLowerCase().trim();
                const selectedCategory = categorySelect.value;
                let visibleCount = 0;

                productCards.forEach(card => {
                    const titleEl = card.querySelector('.product-title');
                    const title = titleEl ? titleEl.textContent.toLowerCase() : '';
                    const category = card.getAttribute('data-category') || '';

                    const matchesQuery = title.includes(query);
                    const matchesCategory = (selectedCategory === 'all') || (category === selectedCategory);

                    if (matchesQuery && matchesCategory) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (noResultsMsg) noResultsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
            }

            searchInput.addEventListener('input', filterProducts);
            categorySelect.addEventListener('change', filterProducts);
        }
    });
</script>

<?php include_once 'includes/footer.php'; ?>