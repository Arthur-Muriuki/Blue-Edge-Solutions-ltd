<?php
// START THE SESSION: This is required to hand out the "VIP Pass"
session_start();
require_once 'includes/db_connect.php';

$error = '';

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Find the user in the database
    $sql = "SELECT * FROM admins WHERE username = :username LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();

    // 2. Check if user exists AND the password matches the scrambled hash
    if ($admin && password_verify($password, $admin['password'])) {
        // SUCCESS! Hand out the VIP Session Pass
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        
        // Redirect them to the vault
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}

$page_title = "Admin Login | Blue Edge Solutions";
include_once 'includes/header.php';
?>

<main style="background-color: #f8fafc; min-height: 70vh; display: flex; align-items: center; justify-content: center;">
    <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 400px;">
        <h2 style="color: #002d62; text-align: center; margin-bottom: 20px;">Admin Access</h2>
        
        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 10px; border-radius: 4px; margin-bottom: 20px; text-align: center;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" style="display: flex; flex-direction: column; gap: 15px;">
            <div>
                <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">Username</label>
                <input type="text" name="username" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
            </div>
            <div>
                <label style="font-weight: bold; color: #475569; display: block; margin-bottom: 5px;">Password</label>
                <input type="password" name="password" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;">
            </div>
            <button type="submit" style="background: #ff7300; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 4px; cursor: pointer; margin-top: 10px; font-size: 1rem;">
                Secure Login
            </button>
        </form>
    </div>
</main>

<?php include_once 'includes/footer.php'; ?>