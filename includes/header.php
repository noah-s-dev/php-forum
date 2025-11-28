<?php
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo SITE_DESCRIPTION; ?>">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo url('public/css/style.css'); ?>?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="top-bar-left">
                        <span class="me-3">
                            <i class="bi bi-envelope text-primary"></i>
                            <small class="text-muted">support@forum.local</small>
                        </span>
                        <span>
                            <i class="bi bi-telephone text-primary"></i>
                            <small class="text-muted">+1 (555) 123-4567</small>
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="top-bar-right text-end">
                        <div class="social-links">
                            <a href="#" class="social-link">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="bi bi-linkedin"></i>
                            </a>
                            <a href="#" class="social-link">
                                <i class="bi bi-instagram"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark main-navbar">
        <div class="container">
            <!-- Brand -->
            <a class="navbar-brand" href="<?php echo url('public/index.php'); ?>">
                <div class="brand-container">
                    <div class="brand-icon">
                        <i class="bi bi-chat-dots"></i>
                    </div>
                    <div class="brand-text">
                        <span class="brand-name"><?php echo SITE_NAME; ?></span>
                        <span class="brand-tagline">Community Forum</span>
                    </div>
                </div>
            </a>
            
            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <!-- Navigation Content -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Main Menu -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('public/index.php'); ?>">
                            <i class="bi bi-house"></i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo url('public/forum/topics.php'); ?>">
                            <i class="bi bi-list-ul"></i>
                            <span>Topics</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-people"></i>
                            <span>Members</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">
                            <i class="bi bi-question-circle"></i>
                            <span>Help</span>
                        </a>
                    </li>
                    <?php if (is_logged_in()): ?>
                        <li class="nav-item">
                            <a class="nav-link btn-create-topic" href="<?php echo url('public/forum/create_topic.php'); ?>">
                                <i class="bi bi-plus-circle"></i>
                                <span>New Topic</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Search Bar -->
                <div class="navbar-search me-3">
                    <div class="search-container">
                        <input type="text" class="form-control search-input" placeholder="Search topics...">
                        <button class="btn search-btn">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                
                <!-- User Menu -->
                <ul class="navbar-nav">
                    <?php if (is_logged_in()): ?>
                        <?php $current_user = get_current_user_data(); ?>
                        <!-- Notifications -->
                        <li class="nav-item dropdown me-2">
                            <a class="nav-link notification-link" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-bell"></i>
                                <span class="notification-badge">3</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                                <li class="dropdown-header">
                                    <h6 class="mb-0">Notifications</h6>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item notification-item" href="#">
                                        <div class="notification-icon">
                                            <i class="bi bi-chat text-primary"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">New reply to your topic</div>
                                            <div class="notification-text">John replied to "Getting Started"</div>
                                            <div class="notification-time">2 minutes ago</div>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item notification-item" href="#">
                                        <div class="notification-icon">
                                            <i class="bi bi-heart text-danger"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">Your post was liked</div>
                                            <div class="notification-text">Sarah liked your post</div>
                                            <div class="notification-time">1 hour ago</div>
                                        </div>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-center" href="#">
                                        View all notifications
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- User Profile -->
                        <li class="nav-item dropdown">
                            <a class="nav-link user-profile-link" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="user-avatar">
                                    <div class="avatar-circle">
                                        <i class="bi bi-person-circle"></i>
                                    </div>
                                    <div class="user-info">
                                        <span class="user-name"><?php echo sanitize_input($current_user['display_name']); ?></span>
                                        <span class="user-role"><?php echo ucfirst($current_user['user_role']); ?></span>
                                    </div>
                                    <i class="bi bi-chevron-down dropdown-arrow"></i>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end user-dropdown">
                                <li class="dropdown-header">
                                    <div class="user-profile-header">
                                        <div class="avatar-circle-large">
                                            <i class="bi bi-person-circle"></i>
                                        </div>
                                        <div class="user-details">
                                            <div class="user-name-large"><?php echo sanitize_input($current_user['display_name']); ?></div>
                                            <div class="user-email"><?php echo sanitize_input($current_user['email']); ?></div>
                                            <div class="user-role-badge">
                                                <span class="badge badge-<?php echo $current_user['user_role'] === 'admin' ? 'danger' : ($current_user['user_role'] === 'moderator' ? 'warning' : 'primary'); ?>">
                                                    <?php echo ucfirst($current_user['user_role']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="<?php echo url('public/auth/profile.php'); ?>">
                                        <i class="bi bi-person"></i>
                                        <span>My Profile</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-gear"></i>
                                        <span>Settings</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#">
                                        <i class="bi bi-bookmark"></i>
                                        <span>My Topics</span>
                                    </a>
                                </li>
                                <?php if (is_admin_or_moderator()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item admin-link" href="<?php echo url('public/admin/dashboard.php'); ?>">
                                            <i class="bi bi-shield-check"></i>
                                            <span>Admin Panel</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="<?php echo url('public/auth/logout.php'); ?>">
                                        <i class="bi bi-box-arrow-right"></i>
                                        <span>Logout</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <!-- Guest User -->
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo url('public/auth/login.php'); ?>">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Login</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-register" href="<?php echo url('public/auth/register.php'); ?>">
                                <i class="bi bi-person-plus"></i>
                                <span>Join Now</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container mt-4">
        <?php display_flash_message(); ?>

