<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db_connect.php';

// 1. SESSION GUARD
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$admin_id = (int)$_SESSION['admin_id'];
$message = '';
$error = '';

// 2. HANDLE PROFILE & PASSWORD UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name        = trim($_POST['full_name'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($full_name)) {
        // Fetch current stored password for verification
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = :id");
        $stmt->execute([':id' => $admin_id]);
        $user = $stmt->fetch();

        // Check if updating password
        if (!empty($current_password) || !empty($new_password)) {
            if (!password_verify($current_password, $user['password'])) {
                $error = "Your current password is incorrect.";
            } elseif ($new_password !== $confirm_password) {
                $error = "New password and confirmation do not match.";
            } elseif (strlen($new_password) < 6) {
                $error = "New password must be at least 6 characters long.";
            } else {
                // Update password + full name
                $updateStmt = $pdo->prepare("UPDATE admins SET full_name = :full_name, password = :password WHERE id = :id");
                $updateStmt->execute([
                    ':full_name' => $full_name,
                    ':password'  => password_hash($new_password, PASSWORD_DEFAULT),
                    ':id'        => $admin_id
                ]);
                $_SESSION['admin_fullname'] = $full_name;
                $message = "Profile and password updated successfully!";
            }
        } else {
            // Update full name only
            $updateStmt = $pdo->prepare("UPDATE admins SET full_name = :full_name WHERE id = :id");
            $updateStmt->execute([
                ':full_name' => $full_name,
                ':id'        => $admin_id
            ]);
            $_SESSION['admin_fullname'] = $full_name;
            $message = "Profile name updated successfully!";
        }
    } else {
        $error = "Full Name cannot be empty.";
    }
}

// Fetch user info
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = :id");
$stmt->execute([':id' => $admin_id]);
$current_user = $stmt->fetch();

$base_url = '../';
$page_title = "My Profile | Blue Edge Admin";
include_once '../includes/header.php';
?>

<main style="background-color: #f8fafc; min-height: 80vh; padding: 40px 20px;">
    <div style="max-width: 500px; margin: 0 auto; background: white; padding: 35px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h2 style="color: #002d62; margin: 0;">My Account Profile</h2>
                <p style="color: #64748b; font-size: 0.85rem; margin: 4px 0 0 0;">Update your name and account password.</p>
            </div>
            <a href="index.php" style="color: #64748b; text-decoration: none; font-size: 0.85rem; font-weight: bold;">← Back</a>
        </div>

        <?php if ($message): ?>
            <div style="background: #dcfce7; color: #15803d; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 0.9rem;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 0.9rem;">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="profile.php" style="display: flex; flex-direction: column; gap: 18px;">
            
            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Username (Read-Only)</label>
                <input type="text" value="@<?php echo htmlspecialchars($current_user['username']); ?>" disabled style="width: 100%; padding: 10px; border: 1px solid #e2e8f0; border-radius: 6px; background: #f8fafc; color: #64748b;">
            </div>

            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Full Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($current_user['full_name']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <hr style="border: none; border-top: 1px solid #f1f5f9; margin: 10px 0;">

            <h4 style="color: #002d62; margin: 0;">Change Password</h4>

            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Current Password</label>
                <input type="password" name="current_password" placeholder="Leave blank if not changing" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">New Password</label>
                <input type="password" name="new_password" placeholder="Min 6 characters" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Confirm New Password</label>
                <input type="password" name="confirm_password" placeholder="Repeat new password" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <button type="submit" style="background: #002d62; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                Save Profile Changes
            </button>
        </form>

    </div>
</main>

<?php include_once '../includes/footer.php'; ?>