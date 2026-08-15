<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db_connect.php';

// 1. SESSION GUARD
if (empty($_SESSION['admin_logged_in'])) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Unauthorized session.']);
        exit();
    }
    header("Location: login.php");
    exit();
}

$admin_id = (int)$_SESSION['admin_id'];

// -------------------------------------------------------------
// 2. AJAX HANDLERS (UPLOAD & DELETE AVATAR)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    
    // A. INSTANT AVATAR UPLOAD
    if ($_POST['action'] === 'upload_avatar_ajax') {
        header('Content-Type: application/json');

        if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath   = $_FILES['avatar_file']['tmp_name'];
            $fileName      = $_FILES['avatar_file']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            if (empty($fileExtension)) {
                $fileExtension = 'png';
            }

            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($fileExtension, $allowed)) {
                $uploadDir = '../assets/images/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                // Fetch existing profile pic for deletion
                $stmt = $pdo->prepare("SELECT profile_pic FROM admins WHERE id = :id");
                $stmt->execute([':id' => $admin_id]);
                $current_admin = $stmt->fetch();
                $old_pic = $current_admin['profile_pic'] ?? '';

                // Generate unique new filename
                $new_filename = 'avatar_admin_' . $admin_id . '_' . time() . '.' . $fileExtension;
                $destPath     = $uploadDir . $new_filename;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    
                    // Delete old avatar file if it exists and isn't a default template
                    if (!empty($old_pic) && $old_pic !== 'default_avatar.png') {
                        $old_file_path = $uploadDir . $old_pic;
                        if (file_exists($old_file_path) && is_file($old_file_path)) {
                            @unlink($old_file_path);
                        }
                    }

                    // Update DB & Session
                    $updateStmt = $pdo->prepare("UPDATE admins SET profile_pic = :pic WHERE id = :id");
                    $updateStmt->execute([
                        ':pic' => $new_filename,
                        ':id'  => $admin_id
                    ]);

                    $_SESSION['admin_avatar'] = $new_filename;

                    echo json_encode([
                        'success'   => true,
                        'image_url' => $uploadDir . $new_filename . '?v=' . time(),
                        'message'   => 'Profile picture updated successfully!'
                    ]);
                    exit();
                } else {
                    echo json_encode(['success' => false, 'error' => 'Failed to save file on server. Check permissions.']);
                    exit();
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid image file type.']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'No image file uploaded or upload error occurred.']);
            exit();
        }
    }

    // B. DELETE AVATAR
    elseif ($_POST['action'] === 'delete_avatar_ajax') {
        header('Content-Type: application/json');

        // Fetch current profile pic to remove from folder
        $stmt = $pdo->prepare("SELECT profile_pic, full_name, username FROM admins WHERE id = :id");
        $stmt->execute([':id' => $admin_id]);
        $current_admin = $stmt->fetch();
        $old_pic = $current_admin['profile_pic'] ?? '';

        $uploadDir = '../assets/images/';
        if (!empty($old_pic) && $old_pic !== 'default_avatar.png') {
            $old_file_path = $uploadDir . $old_pic;
            if (file_exists($old_file_path) && is_file($old_file_path)) {
                @unlink($old_file_path);
            }
        }

        // Reset DB & Session
        $updateStmt = $pdo->prepare("UPDATE admins SET profile_pic = NULL WHERE id = :id");
        $updateStmt->execute([':id' => $admin_id]);

        $_SESSION['admin_avatar'] = null;

        $displayName = $current_admin['full_name'] ?? $current_admin['username'];
        $default_avatar_url = 'https://ui-avatars.com/api/?name=' . urlencode($displayName) . '&background=002d62&color=fff';

        echo json_encode([
            'success'   => true,
            'image_url' => $default_avatar_url,
            'message'   => 'Profile picture deleted and reset to default.'
        ]);
        exit();
    }
}

// -------------------------------------------------------------
// 3. MAIN PROFILE & PASSWORD FORM HANDLER
// -------------------------------------------------------------
$message = '';
$error   = '';

function validatePasswordStrength($password) {
    if (strlen($password) < 8) return "Password must be at least 8 characters long.";
    if (!preg_match('/[A-Z]/', $password)) return "Password must contain at least one uppercase letter (A-Z).";
    if (!preg_match('/[a-z]/', $password)) return "Password must contain at least one lowercase letter (a-z).";
    if (!preg_match('/[0-9]/', $password)) return "Password must contain at least one number (0-9).";
    if (!preg_match('/[\W_]/', $password)) return "Password must contain at least one special character (!@#$%^&*).";
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['action'])) {
    $full_name        = trim($_POST['full_name'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($full_name)) {
        $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = :id");
        $stmt->execute([':id' => $admin_id]);
        $user = $stmt->fetch();

        if (!empty($current_password) || !empty($new_password)) {
            if (!password_verify($current_password, $user['password'])) {
                $error = "Your current password is incorrect.";
            } elseif ($new_password !== $confirm_password) {
                $error = "New password and confirmation do not match.";
            } else {
                $pwdCheck = validatePasswordStrength($new_password);
                if ($pwdCheck !== true) {
                    $error = $pwdCheck;
                } else {
                    $updateStmt = $pdo->prepare("UPDATE admins SET full_name = :full_name, password = :pwd WHERE id = :id");
                    $updateStmt->execute([
                        ':full_name' => $full_name,
                        ':pwd'       => password_hash($new_password, PASSWORD_DEFAULT),
                        ':id'        => $admin_id
                    ]);
                    $_SESSION['admin_fullname'] = $full_name;
                    $message = "Profile name and password updated successfully!";
                }
            }
        } else {
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

// Fetch admin profile details
$stmt = $pdo->prepare("SELECT * FROM admins WHERE id = :id");
$stmt->execute([':id' => $admin_id]);
$current_user = $stmt->fetch();

$has_custom_avatar = !empty($current_user['profile_pic']);
$avatar_file = $has_custom_avatar ? $current_user['profile_pic'] : 'default_avatar.png';
$avatar_path = '../assets/images/' . $avatar_file;

if (!file_exists($avatar_path) || !$has_custom_avatar) {
    $avatar_path = 'https://ui-avatars.com/api/?name=' . urlencode($current_user['full_name'] ?? $current_user['username']) . '&background=002d62&color=fff';
} else {
    $avatar_path .= '?v=' . time();
}

$base_url   = '../';
$page_title = "My Profile | Blue Edge Admin";
include_once '../includes/header.php';
?>

<!-- Include Cropper.js CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">

<main style="background-color: #f8fafc; min-height: 80vh; padding: 40px 20px;">
    <div style="max-width: 550px; margin: 0 auto; background: white; padding: 35px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
            <div>
                <h2 style="color: #002d62; margin: 0;">My Account Profile</h2>
                <p style="color: #64748b; font-size: 0.85rem; margin: 4px 0 0 0;">Manage your avatar, profile details, and security.</p>
            </div>
            <a href="index.php" style="color: #64748b; text-decoration: none; font-size: 0.85rem; font-weight: bold;">← Back</a>
        </div>

        <!-- Dynamic Avatar Alert Box -->
        <div id="avatarStatusAlert" style="display: none; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 0.9rem; font-weight: bold;"></div>

        <?php if ($message): ?>
            <div style="background: #dcfce7; color: #15803d; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 0.9rem; font-weight: bold;">
                ✅ <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div style="background: #fee2e2; color: #b91c1c; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center; font-size: 0.9rem; font-weight: bold;">
                ⚠️ <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <!-- SECTION 1: AVATAR, CROPPING & DELETE SECTION -->
        <div style="display: flex; flex-direction: column; align-items: center; gap: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 25px; margin-bottom: 20px;">
            
            <img id="currentAvatarImg" src="<?php echo htmlspecialchars($avatar_path); ?>" alt="Profile Picture" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid #002d62; box-shadow: 0 4px 12px rgba(0,0,0,0.12);">

            <div style="display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; align-items: center;">
                <label for="avatarSelectInput" style="background: #e2e8f0; color: #1e293b; padding: 8px 16px; border-radius: 6px; font-weight: bold; font-size: 0.85rem; cursor: pointer; transition: background 0.2s;">
                    📷 Choose New Image
                </label>
                <input type="file" id="avatarSelectInput" accept="image/*" style="display: none;">

                <button type="button" id="btnDeleteAvatar" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; padding: 8px 16px; border-radius: 6px; font-size: 0.85rem; font-weight: bold; cursor: pointer; display: <?php echo $has_custom_avatar ? 'inline-block' : 'none'; ?>;">
                    🗑️ Delete Picture
                </button>
            </div>
            <small style="color: #94a3b8; font-size: 0.75rem; text-align: center;">Old profile images are automatically cleaned from server storage.</small>

            <!-- CROPPER CONTAINER MODAL / UI -->
            <div id="cropperWrapper" style="display: none; width: 100%; background: #f1f5f9; border-radius: 8px; padding: 15px; box-sizing: border-box; text-align: center; margin-top: 10px; border: 1px dashed #cbd5e1;">
                <p style="margin: 0 0 10px 0; font-weight: bold; color: #002d62; font-size: 0.9rem;">Crop Your Image (Or Upload As Is)</p>
                
                <div style="max-height: 300px; overflow: hidden; display: flex; justify-content: center; align-items: center; margin-bottom: 15px;">
                    <img id="cropperImage" src="" alt="Crop Preview" style="max-width: 100%; display: block;">
                </div>

                <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                    <button type="button" id="btnCropAndSave" style="background: #16a34a; color: white; border: none; padding: 8px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: bold; cursor: pointer;">
                        ✂️ Crop & Instant Save
                    </button>
                    <button type="button" id="btnDirectSave" style="background: #002d62; color: white; border: none; padding: 8px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: bold; cursor: pointer;">
                        📤 Upload Without Cropping
                    </button>
                    <button type="button" id="btnCancelCrop" style="background: #94a3b8; color: white; border: none; padding: 8px 14px; border-radius: 6px; font-size: 0.85rem; font-weight: bold; cursor: pointer;">
                        Cancel
                    </button>
                </div>
            </div>

        </div>

        <!-- SECTION 2: MAIN ACCOUNT FORM -->
        <form method="POST" action="profile.php" style="display: flex; flex-direction: column; gap: 18px;">
            
            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Admin Name</label>
                <input type="text" name="full_name" value="<?php echo htmlspecialchars($current_user['full_name'] ?? $current_user['username']); ?>" required style="width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
            </div>

            <hr style="border: none; border-top: 1px solid #f1f5f9; margin: 5px 0;">

            <h4 style="color: #002d62; margin: 0;">Change Password</h4>

            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Current Password</label>
                <div style="position: relative;">
                    <input type="password" id="curPwd" name="current_password" placeholder="Leave blank if not changing" style="width: 100%; padding: 10px 38px 10px 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    <button type="button" onclick="togglePasswordVisibility('curPwd', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; font-size: 1rem; padding: 0;">👁️</button>
                </div>
            </div>

            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">New Password</label>
                <div style="position: relative;">
                    <input type="password" id="newPwd" name="new_password" placeholder="8+ chars, uppercase, number, symbol" style="width: 100%; padding: 10px 38px 10px 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    <button type="button" onclick="togglePasswordVisibility('newPwd', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; font-size: 1rem; padding: 0;">👁️</button>
                </div>
            </div>

            <div>
                <label style="font-weight: bold; color: #334155; display: block; margin-bottom: 6px; font-size: 0.85rem;">Confirm New Password</label>
                <div style="position: relative;">
                    <input type="password" id="cfmPwd" name="confirm_password" placeholder="Repeat new password" style="width: 100%; padding: 10px 38px 10px 10px; border: 1px solid #cbd5e1; border-radius: 6px; box-sizing: border-box;">
                    <button type="button" onclick="togglePasswordVisibility('cfmPwd', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; font-size: 1rem; padding: 0;">👁️</button>
                </div>
            </div>

            <button type="submit" style="background: #002d62; color: white; border: none; padding: 12px; font-weight: bold; border-radius: 6px; cursor: pointer; margin-top: 10px;">
                Save Profile Changes
            </button>
        </form>

    </div>
</main>

<!-- Include Cropper.js JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>

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

// CROPPER, UPLOAD & DELETE LOGIC
let cropper = null;
let rawSelectedFile = null;

const avatarSelectInput = document.getElementById('avatarSelectInput');
const btnDeleteAvatar   = document.getElementById('btnDeleteAvatar');
const cropperWrapper    = document.getElementById('cropperWrapper');
const cropperImage      = document.getElementById('cropperImage');
const currentAvatarImg  = document.getElementById('currentAvatarImg');
const avatarStatusAlert = document.getElementById('avatarStatusAlert');

function showAlert(msg, isSuccess = true) {
    avatarStatusAlert.style.display = 'block';
    avatarStatusAlert.style.background = isSuccess ? '#dcfce7' : '#fee2e2';
    avatarStatusAlert.style.color = isSuccess ? '#15803d' : '#b91c1c';
    avatarStatusAlert.innerHTML = (isSuccess ? '✅ ' : '⚠️ ') + msg;
}

// 1. File Chosen
avatarSelectInput.addEventListener('change', function(e) {
    const files = e.target.files;
    if (files && files.length > 0) {
        rawSelectedFile = files[0];
        const reader = new FileReader();

        reader.onload = function(evt) {
            cropperImage.src = evt.target.result;
            cropperWrapper.style.display = 'block';

            if (cropper) {
                cropper.destroy();
            }

            cropper = new Cropper(cropperImage, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 0.9,
                responsive: true
            });
        };

        reader.readAsDataURL(rawSelectedFile);
    }
});

// Helper: Upload File AJAX
function uploadAvatarFile(fileBlob, fileName = 'avatar.png') {
    const formData = new FormData();
    formData.append('action', 'upload_avatar_ajax');
    formData.append('avatar_file', fileBlob, fileName);

    showAlert('Uploading new profile picture...', true);

    fetch('profile.php', {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            currentAvatarImg.src = data.image_url;
            showAlert(data.message, true);
            btnDeleteAvatar.style.display = 'inline-block';
            resetCropper();
        } else {
            showAlert(data.error || 'Failed to upload profile picture.', false);
        }
    })
    .catch(err => {
        console.error(err);
        showAlert('An error occurred during upload.', false);
    });
}

// 2. Crop & Instant Save Clicked
document.getElementById('btnCropAndSave').addEventListener('click', function() {
    if (!cropper) return;
    const canvas = cropper.getCroppedCanvas({
        width: 400,
        height: 400,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high'
    });

    canvas.toBlob(function(blob) {
        uploadAvatarFile(blob, 'cropped_avatar.png');
    }, 'image/png');
});

// 3. Direct Upload Without Cropping Clicked
document.getElementById('btnDirectSave').addEventListener('click', function() {
    if (!rawSelectedFile) return;
    uploadAvatarFile(rawSelectedFile, rawSelectedFile.name);
});

// 4. Delete Avatar Clicked
if (btnDeleteAvatar) {
    btnDeleteAvatar.addEventListener('click', function() {
        if (!confirm('Are you sure you want to remove your profile picture?')) return;

        const formData = new FormData();
        formData.append('action', 'delete_avatar_ajax');

        showAlert('Deleting profile picture...', true);

        fetch('profile.php', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                currentAvatarImg.src = data.image_url;
                showAlert(data.message, true);
                btnDeleteAvatar.style.display = 'none';
            } else {
                showAlert(data.error || 'Failed to delete picture.', false);
            }
        })
        .catch(err => {
            console.error(err);
            showAlert('An error occurred while deleting picture.', false);
        });
    });
}

// 5. Cancel Cropping
document.getElementById('btnCancelCrop').addEventListener('click', function() {
    resetCropper();
});

function resetCropper() {
    if (cropper) {
        cropper.destroy();
        cropper = null;
    }
    cropperWrapper.style.display = 'none';
    cropperImage.src = '';
    avatarSelectInput.value = '';
    rawSelectedFile = null;
}
</script>

<?php include_once '../includes/footer.php'; ?>