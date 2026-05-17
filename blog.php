<?php
// 1. Core configurations and database integration
require_once 'includes/db_connect.php';

// Fetch articles from the database using PDO syntax
$sql = "SELECT id, title, slug, excerpt, category, publish_date FROM blog_posts ORDER BY publish_date DESC";
$stmt = $pdo->query($sql);
$posts = $stmt->fetchAll();

// 2. Inject global site header navigation
$page_title = "IT Insights & Tech Blog | Blue Edge Solutions";
include_once 'includes/header.php';
?>

<main style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f8fafc; padding-bottom: 80px;">

    <header style="background: linear-gradient(135deg, #002d62 0%, #001f42 100%); color: white; padding: 80px 20px; text-align: center;">
        <div style="max-width: 900px; margin: 0 auto;">
            <span style="color: #ff7300; font-weight: bold; text-transform: uppercase; font-size: 0.9rem; letter-spacing: 1px;">Insights & Updates</span>
            <h1 style="font-size: 3rem; font-weight: 700; color:#e2e8f0; margin: 10px 0 20px 0; letter-spacing: -0.5px;">The Blue Edge Tech Blog</h1>
            <p style="font-size: 1.25rem; color: #cbd5e1; line-height: 1.6; max-width: 700px; margin: 0 auto;">
                Expert perspectives on enterprise networking, cloud infrastructure, and the latest cybersecurity defense strategies.
            </p>
        </div>
    </header>

    <section style="padding: 60px 20px; max-width: 1200px; margin: 0 auto;">
        
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 40px;">

            <?php 
            // Check if we have any posts in our PDO array
            if (count($posts) > 0): 
                // Loop through each row cleanly using a foreach loop
                foreach($posts as $row): 
                    // Format the date nicely (e.g., May 16, 2026)
                    $formatted_date = date("F j, Y", strtotime($row['publish_date']));
                    
                    // Assign a specific color based on category
                    $badge_color = "#002d62"; 
                    if($row['category'] == 'Cybersecurity') $badge_color = "#ff7300";
            ?>
            
            <article style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.03); transition: transform 0.2s ease; display: flex; flex-direction: column;">
                <div style="background: #e2e8f0; height: 220px; display: flex; align-items: center; justify-content: center; position: relative;">
                    <span style="position: absolute; top: 15px; left: 15px; background: <?php echo $badge_color; ?>; color: white; padding: 5px 12px; font-size: 0.8rem; font-weight: bold; border-radius: 4px; text-transform: uppercase;">
                        <?php echo htmlspecialchars($row['category']); ?>
                    </span>
                    
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="#94a3b8" viewBox="0 0 16 16">
                        <path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
                        <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
                    </svg>
                </div>
                <div style="padding: 30px; display: flex; flex-direction: column; flex-grow: 1;">
                    <time datetime="<?php echo $row['publish_date']; ?>" style="color: #64748b; font-size: 0.85rem; font-weight: 600; margin-bottom: 10px; display: block;">
                        <?php echo $formatted_date; ?>
                    </time>
                    
                    <h2 style="color: #002d62; font-size: 1.4rem; margin: 0 0 15px 0; line-height: 1.3;">
                        <a href="article.php?slug=<?php echo $row['slug']; ?>" style="text-decoration: none; color: inherit;">
                            <?php echo htmlspecialchars($row['title']); ?>
                        </a>
                    </h2>
                    
                    <p style="color: #475569; font-size: 1rem; line-height: 1.6; margin-bottom: 25px; flex-grow: 1;">
                        <?php echo htmlspecialchars($row['excerpt']); ?>
                    </p>
                    
                    <a href="article.php?slug=<?php echo $row['slug']; ?>" style="color: #002d62; font-weight: bold; text-decoration: none; display: inline-flex; align-items: center; gap: 5px;">
                        Read Article 
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
                        </svg>
                    </a>
                </div>
            </article>

            <?php 
                endforeach; 
            else: 
            ?>
                <p style="grid-column: 1 / -1; text-align: center; color: #64748b; font-size: 1.2rem; padding: 40px 0;">No articles published yet. Check back soon!</p>
            <?php endif; ?>

        </div>
    </section>

</main>

<?php
// 3. Inject global footer components
include_once 'includes/footer.php';
?>