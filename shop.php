<?php
require_once 'includes/db_connect.php';

try {
    $pdo->exec("ALTER TABLE products ADD COLUMN stock INT NOT NULL DEFAULT 10");
} catch (PDOException $e) {}

try {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}

$page_title = "Shop & Services | Blue Edge Solutions";
include_once 'includes/header.php';

$base_path = $base ?? '';
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
                        $item_type  = $item['item_type'] ?? 'product'; 
                        $title      = $item['title'] ?? 'Untitled Item';
                        $price      = floatval($item['price'] ?? 0);
                        $stock      = isset($item['stock']) ? intval($item['stock']) : 0;
                        $is_out_of_stock = ($item_type === 'product' && $stock <= 0);
                        $image_src  = !empty($item['image']) ? $item['image'] : 'assets/images/placeholder.png';
                    ?>
                    <div class="product-card" 
                         data-category="<?php echo htmlspecialchars($item['category'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" 
                         style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        
                        <div style="background: #f8fafc; height: 220px; padding: 15px; display: flex; align-items: center; justify-content: center; position: relative;">
                            <img src="<?php echo htmlspecialchars($image_src, ENT_QUOTES, 'UTF-8'); ?>" 
                                 alt="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>" 
                                 onerror="this.src='https://via.placeholder.com/300x200?text=No+Image';"
                                 style="max-height: 100%; max-width: 100%; object-fit: contain; <?php echo $is_out_of_stock ? 'filter: grayscale(80%); opacity: 0.6;' : ''; ?>">
                            
                            <?php if ($item_type === 'subscription'): ?>
                                <span style="position: absolute; top: 12px; right: 12px; background: #0284c7; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">Monthly Plan</span>
                            <?php elseif ($item_type === 'booking'): ?>
                                <span style="position: absolute; top: 12px; right: 12px; background: #7c3aed; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">Service</span>
                            <?php elseif ($is_out_of_stock): ?>
                                <span style="position: absolute; top: 12px; right: 12px; background: #dc2626; color: white; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase;">Out of Stock</span>
                            <?php endif; ?>
                        </div>

                        <div style="padding: 25px; display: flex; flex-direction: column; flex-grow: 1;">
                            <h3 class="product-title" style="color: #002d62; font-size: 1.2rem; margin: 0 0 10px 0;">
                                <?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>
                            </h3>
                            <p style="color: #475569; font-size: 0.9rem; flex-grow: 1; margin-bottom: 20px; line-height: 1.4;">
                                <?php echo htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                                <div>
                                    <span style="font-size: 1.2rem; font-weight: bold; color: #002d62;">
                                        Ksh <?php echo number_format($price, 0); ?>
                                        <?php echo ($item_type === 'subscription') ? '<small style="font-size:0.75rem; font-weight:normal; color:#64748b;">/mo</small>' : ''; ?>
                                    </span>
                                    
                                    <?php if ($item_type === 'product'): ?>
                                        <div style="font-size: 0.8rem; margin-top: 3px; font-weight: 600; color: <?php echo $stock > 0 ? '#16a34a' : '#dc2626'; ?>;">
                                            <?php echo $stock > 0 ? "In Stock ({$stock} left)" : "Out of Stock"; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($item_type === 'subscription'): ?>
                                    <button type="button" class="btn-add-to-cart" 
                                            data-id="<?php echo $item['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-price="<?php echo $price; ?>"
                                            data-type="subscription"
                                            style="background: #0284c7; color: white; border: none; padding: 10px 14px; border-radius: 4px; font-weight: bold; cursor: pointer;">
                                        Subscribe
                                    </button>
                                <?php elseif ($item_type === 'booking'): ?>
                                    <button type="button" class="btn-add-to-cart"
                                            data-id="<?php echo $item['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-price="<?php echo $price; ?>"
                                            data-type="booking"
                                            style="background: #7c3aed; color: white; border: none; padding: 10px 14px; border-radius: 4px; font-weight: bold; cursor: pointer;">
                                        Book Service
                                    </button>
                                <?php elseif ($is_out_of_stock): ?>
                                    <button type="button" disabled 
                                            style="background: #94a3b8; color: white; border: none; padding: 10px 14px; border-radius: 4px; font-weight: bold; cursor: not-allowed; opacity: 0.7;">
                                        Out of Stock
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn-add-to-cart"
                                            data-id="<?php echo $item['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>"
                                            data-price="<?php echo $price; ?>"
                                            data-type="product"
                                            style="background: #ff7300; color: white; border: none; padding: 10px 14px; border-radius: 4px; font-weight: bold; cursor: pointer;">
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

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.btn-add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const title = this.getAttribute('data-title');
                const price = parseFloat(this.getAttribute('data-price')) || 0;
                const type = this.getAttribute('data-type') || 'product';

                if (typeof addToCart === 'function') {
                    addToCart(id, title, price, type, 1);
                } else {
                    console.error('addToCart function is not available.');
                }
            });
        });

        const searchInput = document.getElementById('shopSearch');
        const categorySelect = document.getElementById('categoryFilter');
        const productCards = document.querySelectorAll('.product-card');
        const noResultsMsg = document.getElementById('noResults');

        if (searchInput && categorySelect) {
            function filterProducts() {
                const query = searchInput.value.toLowerCase().trim();
                const selectedCategory = categorySelect.value.toLowerCase();
                let visibleCount = 0;

                productCards.forEach(card => {
                    const titleEl = card.querySelector('.product-title');
                    const title = titleEl ? titleEl.textContent.toLowerCase() : '';
                    const category = (card.getAttribute('data-category') || '').toLowerCase();

                    const matchesQuery = title.includes(query);
                    const matchesCategory = (selectedCategory === 'all') || (category === selectedCategory);

                    if (matchesQuery && matchesCategory) {
                        card.style.display = 'flex';
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });

                if (noResultsMsg) {
                    noResultsMsg.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            }

            searchInput.addEventListener('input', filterProducts);
            categorySelect.addEventListener('change', filterProducts);
        }
    });
</script>

<?php include_once 'includes/footer.php'; ?>