<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$SECRET_KEY = 'bes_staff';

// 1. If already logged in, send directly to admin dashboard
if (!empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

// 2. Access Gate: Grant session authorization if secret key is present in URL
if (isset($_GET['key']) && $_GET['key'] === $SECRET_KEY) {
    $_SESSION['staff_access_granted'] = true;
}

// 3. Block anyone who doesn't have session access granted
if (empty($_SESSION['staff_access_granted'])) {
    header("Location: ../index.php");
    exit();
}

require_once '../includes/db_connect.php';

$error = '';

// 4. PROCESS LOGIN SUBMISSION
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password   = $_POST['password'] ?? '';

    if (!empty($identifier) && !empty($password)) {
        // Query user matching either username OR full_name with distinct placeholders
        $sql = "SELECT * FROM admins WHERE username = :u_name OR full_name = :f_name LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':u_name' => $identifier,
            ':f_name' => $identifier
        ]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify hashed password
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_username']  = $admin['username'];
            $_SESSION['admin_fullname']  = $admin['full_name'] ?? $admin['username'];
            $_SESSION['admin_role']      = $admin['role'];

            // Update last_login timestamp
            $updateStmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = :id");
            $updateStmt->execute([':id' => $admin['id']]);

            // Log activity audit record directly to the database
            try {
                $logStmt = $pdo->prepare("
                    INSERT INTO activity_logs 
                    (user_id, username, user_role, action, details, ip_address, is_read) 
                    VALUES 
                    (:user_id, :username, :user_role, :action, :details, :ip, 0)
                ");
                $logStmt->execute([
                    ':user_id'   => $admin['id'],
                    ':username'  => $admin['username'],
                    ':user_role' => $admin['role'],
                    ':action'    => 'Admin Login',
                    ':details'   => 'Signed into administrative portal',
                    ':ip'        => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
                ]);
            } catch (PDOException $e) {
                // Fail silently so a logging error doesn't stop the admin from logging in
            }

            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid name/username or password.";
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
            <p style="color: #64748b; font-size: 0.85rem; margin: 0;">Sign in with your Name and Password.</p>
        </div>
        
        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 0.9rem;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php?key=bes_staff" style="display: flex; flex-direction: column; gap: 18px;">
            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.9rem;">Admin Name / Username</label>
                <input type="text" name="identifier" required placeholder="Enter your name" style="width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 0.95rem;">
            </div>
            
            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.9rem;">Password</label>
                <div style="position: relative;">
                    <input type="password" id="loginPassword" name="password" required placeholder="Enter password" style="width: 100%; padding: 12px 40px 12px 12px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box; font-size: 0.95rem;">
                    <button type="button" onclick="togglePasswordVisibility('loginPassword', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; font-size: 1.1rem; padding: 0;">
                        👁️
                    </button>
                </div>
            </div>
            
            <button type="submit" style="background: #002d62; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-top: 5px; font-size: 1rem;">
                Secure Sign In
            </button>
        </form>

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