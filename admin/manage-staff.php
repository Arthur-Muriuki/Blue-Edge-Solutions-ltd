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

// Password Strength Validation Helper Function
function validatePasswordStrength($password) {
    if (strlen($password) < 8) {
        return "Password must be at least 8 characters long.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "Password must contain at least one uppercase letter (A-Z).";
    }
    if (!preg_match('/[a-z]/', $password)) {
        return "Password must contain at least one lowercase letter (a-z).";
    }
    if (!preg_match('/[0-9]/', $password)) {
        return "Password must contain at least one number (0-9).";
    }
    if (!preg_match('/[\W_]/', $password)) {
        return "Password must contain at least one special character (!@#$%^&*).";
    }
    return true;
}

// 2. HANDLE FORM ACTIONS (ADD, DELETE, RESET PASSWORD)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // A. ADD NEW STAFF MEMBER
    if ($action === 'add_staff') {
        $name     = trim($_POST['full_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'STAFF_ADMIN';

        if (!empty($name) && !empty($password)) {
            // Validate Password Strength
            $pwdCheck = validatePasswordStrength($password);
            if ($pwdCheck !== true) {
                $error = $pwdCheck;
            } else {
                // Check if account name already exists with distinct parameters
                $checkStmt = $pdo->prepare("SELECT id FROM admins WHERE username = :u_name OR full_name = :f_name");
                $checkStmt->execute([
                    ':u_name' => $name,
                    ':f_name' => $name
                ]);
                
                if ($checkStmt->fetch()) {
                    $error = "An admin with the name '{$name}' already exists.";
                } else {
                    // Set both username and full_name to the provided name with distinct placeholders
                    $stmt = $pdo->prepare("
                        INSERT INTO admins (username, password, full_name, role)
                        VALUES (:username, :password, :full_name, :role)
                    ");
                    $stmt->execute([
                        ':username'  => $name,
                        ':password'  => password_hash($password, PASSWORD_DEFAULT),
                        ':full_name' => $name,
                        ':role'      => $role
                    ]);
                    $message = "New sub-admin account '{$name}' created successfully!";
                }
            }
        } else {
            $error = "Please provide both Name and Password.";
        }
    }

    // B. RESET STAFF PASSWORD
    elseif ($action === 'reset_password') {
        $staff_id     = (int)($_POST['staff_id'] ?? 0);
        $new_password = $_POST['new_password'] ?? '';

        if ($staff_id > 0 && !empty($new_password)) {
            $pwdCheck = validatePasswordStrength($new_password);
            if ($pwdCheck !== true) {
                $error = "Reset failed: " . $pwdCheck;
            } else {
                $stmt = $pdo->prepare("UPDATE admins SET password = :password WHERE id = :id");
                $stmt->execute([
                    ':password' => password_hash($new_password, PASSWORD_DEFAULT),
                    ':id'       => $staff_id
                ]);
                $message = "Staff password updated successfully!";
            }
        } else {
            $error = "Password cannot be blank.";
        }
    }

    // C. DELETE STAFF ACCOUNT
    elseif ($action === 'delete_staff') {
        $staff_id = (int)($_POST['staff_id'] ?? 0);

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
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px;">
            <div>
                <h1 style="color: #002d62; margin: 0; font-size: 1.8rem;">Staff Management</h1>
                <p style="color: #64748b; margin: 5px 0 0 0;">Create sub-admins using just their name and password.</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="index.php" style="background: #e2e8f0; color: #334155; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9rem;">← Dashboard</a>
                <a href="profile.php" style="background: #002d62; color: white; padding: 10px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9rem;">My Profile</a>
            </div>
        </div>

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
                    ➕ Add Sub-Admin
                </h3>
                
                <form method="POST" action="manage-staff.php" style="display: flex; flex-direction: column; gap: 16px;">
                    <input type="hidden" name="action" value="add_staff">
                    
                    <div>
                        <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Admin Name</label>
                        <input type="text" name="full_name" required placeholder="e.g. John Doe" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    </div>

                    <div>
                        <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Password</label>
                        <div style="position: relative;">
                            <input type="password" id="staffPassword" name="password" required placeholder="Strong password (8+ chars, A-Z, 0-9, symbol)" style="width: 100%; padding: 10px 38px 10px 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 0.85rem;">
                            <button type="button" onclick="togglePasswordVisibility('staffPassword', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; font-size: 1rem; padding: 0;">
                                👁️
                            </button>
                        </div>
                        <small style="color: #64748b; font-size: 0.75rem; display: block; margin-top: 4px;">Must have 8+ chars, upper & lower case, number, & symbol.</small>
                    </div>

                    <div>
                        <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Role Permission</label>
                        <select name="role" style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; background: white;">
                            <option value="STAFF_ADMIN">STAFF_ADMIN (Orders, Hardware & Blog Editing)</option>
                            <option value="SUPER_ADMIN">SUPER_ADMIN (Full Control + Staff Management)</option>
                        </select>
                    </div>

                    <button type="submit" style="background: #ff7300; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                        Create Sub-Admin
                    </button>
                </form>
            </div>

            <!-- RIGHT COLUMN: EXISTING STAFF TABLE -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                <h3 style="color: #002d62; margin-top: 0; margin-bottom: 20px; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">
                    👥 Active Admin Team
                </h3>

                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 10px; color: #475569;">Admin Name</th>
                                <th style="padding: 10px; color: #475569;">Role</th>
                                <th style="padding: 10px; color: #475569; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staffList as $staff): ?>
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 12px 10px;">
                                    <strong><?php echo htmlspecialchars($staff['full_name'] ?? $staff['username']); ?></strong>
                                </td>
                                <td style="padding: 12px 10px;">
                                    <?php if ($staff['role'] === 'SUPER_ADMIN'): ?>
                                        <span style="background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">SUPER ADMIN</span>
                                    <?php else: ?>
                                        <span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: bold;">SUB ADMIN</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding: 12px 10px; text-align: right;">
                                    <div style="display: flex; gap: 6px; justify-content: flex-end;">
                                        <form method="POST" action="manage-staff.php" onsubmit="const p = prompt('Enter new strong password for <?php echo htmlspecialchars($staff['full_name']); ?>:'); if(p){ this.new_password.value = p; return true; } return false;">
                                            <input type="hidden" name="action" value="reset_password">
                                            <input type="hidden" name="staff_id" value="<?php echo $staff['id']; ?>">
                                            <input type="hidden" name="new_password" value="">
                                            <button type="submit" style="background: #3b82f6; color: white; border: none; padding: 6px 10px; border-radius: 4px; font-size: 0.75rem; cursor: pointer; font-weight: bold;">
                                                Reset Pass
                                            </button>
                                        </form>

                                        <?php if ((int)$staff['id'] !== (int)$_SESSION['admin_id']): ?>
                                            <form method="POST" action="manage-staff.php" onsubmit="return confirm('Are you sure you want to remove <?php echo htmlspecialchars($staff['full_name']); ?>?');">
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

<script>
function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        btn.textContent = "🙈";
    } else {
        input.type = "password";
        btn.textContent = "👁️";
    }
}
</script>

<?php include_once '../includes/footer.php'; ?>