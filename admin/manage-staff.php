<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db_connect.php';

// 1. SESSION & ROLE GUARD
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// RESTRICT ACCESS TO SUPER ADMIN ONLY
if (($_SESSION['admin_role'] ?? '') !== 'SUPER_ADMIN') {
    die("<div style='font-family:sans-serif; padding:40px; text-align:center;'>
            <h2 style='color:#dc2626;'>🚫 Access Denied</h2>
            <p>You do not have permission to access Staff Management. This area is reserved for Super Administrators.</p>
            <p><a href='index.php' style='color:#002d62; font-weight:bold;'>Return to Dashboard</a></p>
         </div>");
}

$message = '';
$error = '';

// 2. HANDLE FORM ACTIONS (ADD, DELETE, RESET PASSWORD)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // A. ADD NEW STAFF MEMBER
    if ($action === 'add_staff') {
        $username  = trim($_POST['username'] ?? '');
        $fullname  = trim($_POST['full_name'] ?? '');
        $password  = $_POST['password'] ?? '';
        $role      = $_POST['role'] ?? 'STAFF_ADMIN';

        if (!empty($username) && !empty($fullname) && !empty($password)) {
            // Check if username already exists
            $checkStmt = $pdo->prepare("SELECT id FROM admins WHERE username = :username");
            $checkStmt->execute([':username' => $username]);
            
            if ($checkStmt->fetch()) {
                $error = "Username '{$username}' is already taken.";
            } else {
                $stmt = $pdo->prepare("
                    INSERT INTO admins (username, password, full_name, role)
                    VALUES (:username, :password, :full_name, :role)
                ");
                $stmt->execute([
                    ':username'  => $username,
                    ':password'  => password_hash($password, PASSWORD_DEFAULT),
                    ':full_name' => $fullname,
                    ':role'      => $role
                ]);
                $message = "New staff account '{$username}' created successfully!";
            }
        } else {
            $error = "Please complete all fields to create a staff account.";
        }
    }

    // B. RESET STAFF PASSWORD
    elseif ($action === 'reset_password') {
        $staff_id     = (int)($_POST['staff_id'] ?? 0);
        $new_password = $_POST['new_password'] ?? '';

        if ($staff_id > 0 && !empty($new_password)) {
            $stmt = $pdo->prepare("UPDATE admins SET password = :password WHERE id = :id");
            $stmt->execute([
                ':password' => password_hash($new_password, PASSWORD_DEFAULT),
                ':id'       => $staff_id
            ]);
            $message = "Staff password updated successfully!";
        } else {
            $error = "Password cannot be blank.";
        }
    }

    // C. DELETE STAFF ACCOUNT
    elseif ($action === 'delete_staff') {
        $staff_id = (int)($_POST['staff_id'] ?? 0);

        // Prevent self-deletion
        if ($staff_id === (int)$_SESSION['admin_id']) {
            $error = "You cannot delete your own Super Admin account!";
        } elseif ($staff_id > 0) {
            $stmt = $pdo->prepare("DELETE FROM admins WHERE id = :id");
            $stmt->execute([':id' => $staff_id]);
            $message = "Staff account removed successfully.";
        }
    }
}

// 3. FETCH ALL ADMIN ACCOUNTS
$staffList = $pdo->query("SELECT id, username, full_name, role, created_at, last_login FROM admins ORDER BY id ASC")->fetchAll();

$base_url = '../';
$page_title = "Manage Staff | Blue Edge Admin";
include_once '../includes/header.php';
?>

<main style="background-color: #f8fafc; min-height: 80vh; padding: 40px 20px;">
    <div style="max-width: 1100px; margin: 0 auto;">
        
        <!-- Header Nav Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="color: #002d62; margin: 0; font-size: 1.8rem;">Staff Management</h1>
                <p style="color: #64748b; margin: 5px 0 0 0;">Create sub-admin accounts, manage permissions, and reset passwords.</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="index.php" style="background: #e2e8f0; color: #334155; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9rem;">← Dashboard</a>
                <a href="profile.php" style="background: #002d62; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9rem;">My Profile</a>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if ($message): ?>
            <div style="background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 14px; border-radius: 8px; margin-bottom: 25px; font-weight: bold;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 14px; border-radius: 8px; margin-bottom: 25px; font-weight: bold;">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 30px; align-items: start;">
            
            <!-- LEFT COLUMN: ADD NEW STAFF FORM -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <h3 style="color: #002d62; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                    ➕ Add New Sub-Admin
                </h3>
                
                <form method="POST" action="manage-staff.php" style="display: flex; flex-direction: column; gap: 16px;">
                    <input type="hidden" name="action" value="add_staff">
                    
                    <div>
                        <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Full Name</label>
                        <input type="text" name="full_name" required placeholder="e.g. John Doe" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Username</label>
                        <input type="text" name="username" required placeholder="e.g. johndoe" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Initial Password</label>
                        <input type="password" name="password" required placeholder="Create temporary password" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Role Permission</label>
                        <select name="role" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; background: white;">
                            <option value="STAFF_ADMIN">STAFF_ADMIN (Orders, Hardware & Blog Editing)</option>
                            <option value="SUPER_ADMIN">SUPER_ADMIN (Full Control + Staff Management)</option>
                        </select>
                    </div>

                    <button type="submit" style="background: #ff7300; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                        Create Account
                    </button>
                </form>
            </div>

            <!-- RIGHT COLUMN: EXISTING STAFF TABLE -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; grid-column: span 1;">
                <h3 style="color: #002d62; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                    👥 Active Admin Team
                </h3>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 10px; color: #475569;">User</th>
                                <th style="padding: 10px; color: #475569;">Role</th>
                                <th style="padding: 10px; color: #475569;">Last Login</th>
                                <th style="padding: 10px; color: #475569; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staffList as $staff): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 10px;">
                                    <strong><?php echo htmlspecialchars($staff['full_name']); ?></strong><br>
                                    <span style="color: #64748b; font-size: 0.8rem;">@<?php echo htmlspecialchars($staff['username']); ?></span>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <?php if ($staff['role'] === 'SUPER_ADMIN'): ?>
                                        <span style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">SUPER ADMIN</span>
                                    <?php else: ?>
                                        <span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">SUB ADMIN</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px 10px; color: #64748b; font-size: 0.8rem;">
                                    <?php echo $staff['last_login'] ? date('M j, g:i a', strtotime($staff['last_login'])) : 'Never'; ?>
                                </td>
                                <td style="padding: 12px 10px; text-align: right;">
                                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                        <!-- Quick Reset Password Form -->
                                        <form method="POST" action="manage-staff.php" onsubmit="const p = prompt('Enter new password for <?php echo htmlspecialchars($staff['username']); ?>:'); if(p){ this.new_password.value = p; return true; } return false;">
                                            <input type="hidden" name="action" value="reset_password">
                                            <input type="hidden" name="staff_id" value="<?php echo $staff['id']; ?>">
                                            <input type="hidden" name="new_password" value="">
                                            <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 6px 10px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: bold;">
                                                Reset Pass
                                            </button>
                                        </form>

                                        <!-- Delete Form (Only if not self) -->
                                        <?php if ((int)$staff['id'] !== (int)$_SESSION['admin_id']): ?>
                                            <form method="POST" action="manage-staff.php" onsubmit="return confirm('Are you sure you want to remove <?php echo htmlspecialchars($staff['username']); ?>?');">
                                                <input type="hidden" name="action" value="delete_staff">
                                                <input type="hidden" name="staff_id" value="<?php echo $staff['id']; ?>">
                                                <button type="submit" style="background: #ef4444; color: white; border: none; padding: 6px 10px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: bold;">
                                                    Remove
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

<?php include_once '../includes/footer.php'; ?>