<?php
// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Secret key definition
$SECRET_KEY = 'bes_staff';

// If already logged in, take them straight to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

// Block access if secret key is missing or wrong
if (!isset($_GET['key']) || $_GET['key'] !== $SECRET_KEY) {
    // Redirect unauthorized visitors to public home
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db_connect.php';

// ----------------------------------------------------
// 1. STEALTH "MAGIC BOOKMARK" CHECK
// ----------------------------------------------------
// Grant staff authorization if URL contains ?key=bes_staff
if (isset($_GET['key']) && $_GET['key'] === 'bes_staff') {
    $_SESSION['staff_access_granted'] = true;
}

// If user is already logged in, send them straight to admin index
if (!empty($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

// Block unauthorized visitors who try accessing without key or saved access
if (empty($_SESSION['staff_access_granted'])) {
    header("Location: ../index.php");
    exit();
}

$error = '';

// ----------------------------------------------------
// 2. PROCESS LOGIN SUBMISSION
// ----------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($username) && !empty($password)) {
        // Query user from database
        $sql = "SELECT * FROM admins WHERE username = :username LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        // Verify hashed password
        if ($admin && password_verify($password, $admin['password'])) {
            // Assign session credentials
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_username']  = $admin['username'];
            $_SESSION['admin_fullname']  = $admin['full_name'];
            $_SESSION['admin_role']      = $admin['role']; // SUPER_ADMIN or STAFF_ADMIN

            // Update last_login timestamp
            $updateStmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = :id");
            $updateStmt->execute([':id' => $admin['id']]);

            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

$base_url = '../';
$page_title = "Admin Portal | Blue Edge Solutions";
include_once '../includes/header.php';
?>

<main style="background-color: #f8fafc; min-height: 70vh; display: flex; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); width: 100%; max-width: 400px; border-top: 5px solid #002d62;">
        
        <div style="text-align: center; margin-bottom: 25px;">
            <h2 style="color: #002d62; margin: 0 0 5px 0;">Admin Access</h2>
            <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Sign in to manage orders, services, and content.</p>
        </div>
        
        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 0.9rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" style="display: flex; flex-direction: column; gap: 18px;">
            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.9rem;">Username</label>
                <input type="text" name="username" required placeholder="Enter username" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 0.95rem;">
            </div>
            
            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.9rem;">Password</label>
                <input type="password" name="password" required placeholder="Enter password" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 0.95rem;">
            </div>
            
            <button type="submit" style="background: #002d62; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-top: 5px; font-size: 1rem; transition: background 0.2s;">
                Secure Sign In
            </button>
        </form>

    </div>
</main>

<?php include_once '../includes/footer.php'; ?>