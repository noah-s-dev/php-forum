<?php
require_once '../../config/config.php';

$page_title = 'Register';
$errors = [];
$success = false;

// Redirect if already logged in
if (is_logged_in()) {
    header('Location: ../index.php');
    exit;
}

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $display_name = trim($_POST['display_name'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validate input
        if (empty($username)) {
            $errors[] = 'Username is required.';
        } elseif (!validate_username($username)) {
            $errors[] = 'Username must be 3-20 characters long and contain only letters, numbers, and underscores.';
        }
        
        if (empty($email)) {
            $errors[] = 'Email is required.';
        } elseif (!validate_email($email)) {
            $errors[] = 'Please enter a valid email address.';
        }
        
        if (empty($display_name)) {
            $errors[] = 'Display name is required.';
        } elseif (strlen($display_name) < 2 || strlen($display_name) > 50) {
            $errors[] = 'Display name must be 2-50 characters long.';
        }
        
        if (empty($password)) {
            $errors[] = 'Password is required.';
        } elseif (!validate_password($password)) {
            $errors[] = 'Password must be at least 6 characters long.';
        }
        
        if ($password !== $confirm_password) {
            $errors[] = 'Passwords do not match.';
        }
        
        // Check if username or email already exists
        if (empty($errors)) {
            $db = getDB();
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $errors[] = 'Username or email already exists.';
            }
        }
        
        // Create user if no errors
        if (empty($errors)) {
            try {
                $password_hash = hash_password($password);
                $stmt = $db->prepare("INSERT INTO users (username, email, display_name, password_hash) VALUES (?, ?, ?, ?)");
                $stmt->execute([$username, $email, $display_name, $password_hash]);
                
                $success = true;
                $_SESSION['flash_message'] = 'Registration successful! You can now log in.';
                $_SESSION['flash_type'] = 'success';
                header('Location: login.php');
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Registration failed. Please try again.';
            }
        }
    }
}

include '../../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-person-plus"></i> Register</h4>
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
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo sanitize_input($_POST['username'] ?? ''); ?>" 
                               required maxlength="20" pattern="[a-zA-Z0-9_]{3,20}">
                        <div class="form-text">3-20 characters, letters, numbers, and underscores only.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo sanitize_input($_POST['email'] ?? ''); ?>" 
                               required maxlength="100">
                    </div>
                    
                    <div class="mb-3">
                        <label for="display_name" class="form-label">Display Name</label>
                        <input type="text" class="form-control" id="display_name" name="display_name" 
                               value="<?php echo sanitize_input($_POST['display_name'] ?? ''); ?>" 
                               required maxlength="50">
                        <div class="form-text">This is how your name will appear to other users.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" 
                               required minlength="6">
                        <div class="form-text">At least 6 characters.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               required minlength="6">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus"></i> Register
                        </button>
                    </div>
                </form>
                
                <hr>
                <div class="text-center">
                    <p class="mb-0">Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

