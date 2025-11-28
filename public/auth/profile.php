<?php
require_once '../../config/config.php';

$page_title = 'Profile';
require_login();

$current_user = get_current_user_data();
$errors = [];
$success = false;

// Process profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $display_name = trim($_POST['display_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validate display name
        if (empty($display_name)) {
            $errors[] = 'Display name is required.';
        } elseif (strlen($display_name) < 2 || strlen($display_name) > 50) {
            $errors[] = 'Display name must be 2-50 characters long.';
        }
        
        // Validate email
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!validate_email($email)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        // Check if email is already taken by another user
        if (empty($errors) && $email !== $current_user['email']) {
            $db = getDB();
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $current_user['id']]);
            if ($stmt->fetch()) {
                $errors[] = 'Email address is already in use.';
            }
        }
        
        // Validate password change if requested
        $update_password = false;
        if (!empty($new_password)) {
            if (empty($current_password)) {
                $errors[] = 'Current password is required to change password.';
            } else {
                // Verify current password
                $stmt = $db->prepare("SELECT password_hash FROM users WHERE id = ?");
                $stmt->execute([$current_user['id']]);
                $user_data = $stmt->fetch();
                
                if (!verify_password($current_password, $user_data['password_hash'])) {
                    $errors[] = 'Current password is incorrect.';
                } elseif (!validate_password($new_password)) {
                    $errors[] = 'New password must be at least 6 characters long.';
                } elseif ($new_password !== $confirm_password) {
                    $errors[] = 'New passwords do not match.';
                } else {
                    $update_password = true;
                }
            }
        }
        
        // Update profile if no errors
        if (empty($errors)) {
            try {
                if ($update_password) {
                    $password_hash = hash_password($new_password);
                    $stmt = $db->prepare("UPDATE users SET display_name = ?, email = ?, password_hash = ? WHERE id = ?");
                    $stmt->execute([$display_name, $email, $password_hash, $current_user['id']]);
                } else {
                    $stmt = $db->prepare("UPDATE users SET display_name = ?, email = ? WHERE id = ?");
                    $stmt->execute([$display_name, $email, $current_user['id']]);
                }
                
                // Update session data
                $_SESSION['display_name'] = $display_name;
                
                $success = true;
                $_SESSION['flash_message'] = 'Profile updated successfully!';
                $_SESSION['flash_type'] = 'success';
                header('Location: profile.php');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Profile update failed. Please try again.';
            }
        }
    }
}

// Get user statistics
$db = getDB();
$stmt = $db->prepare("SELECT COUNT(*) as topic_count FROM topics WHERE user_id = ?");
$stmt->execute([$current_user['id']]);
$topic_count = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) as reply_count FROM replies WHERE user_id = ? AND is_deleted = 0");
$stmt->execute([$current_user['id']]);
$reply_count = $stmt->fetchColumn();

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-person"></i> Edit Profile</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo sanitize_input($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" 
                               value="<?php echo sanitize_input($current_user['username']); ?>" 
                               disabled>
                        <div class="form-text">Username cannot be changed.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_name" class="form-label">Display Name</label>
                        <input type="text" class="form-control" id="display_name" name="display_name" 
                               value="<?php echo sanitize_input($_POST['display_name'] ?? $current_user['display_name']); ?>" 
                               required maxlength="50">
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo sanitize_input($_POST['email'] ?? $current_user['email']); ?>" 
                               required maxlength="100">
                    </div>
                    
                    <hr>
                    <h6>Change Password (optional)</h6>
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password">
                        <div class="form-text">Required only if changing password.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" minlength="6">
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" minlength="6">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Statistics</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary"><?php echo $topic_count; ?></h4>
                        <small class="text-muted">Topics Created</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success"><?php echo $reply_count; ?></h4>
                        <small class="text-muted">Replies Posted</small>
                    </div>
                </div>
                <hr>
                <p class="small text-muted mb-1">
                    <strong>Member since:</strong> <?php echo format_date($current_user['join_date']); ?>
                </p>
                <p class="small text-muted mb-0">
                    <strong>Role:</strong> <?php echo ucfirst($current_user['user_role']); ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

