<?php
require_once '../config/config.php';

$page_title = 'Home';

// Get recent topics
$db = getDB();
$stmt = $db->prepare("
    SELECT t.*, u.display_name as author_name
    FROM topics t 
    JOIN users u ON t.user_id = u.id 
    ORDER BY t.is_pinned DESC, t.created_at DESC 
    LIMIT 5
");
$stmt->execute();
$recent_topics = $stmt->fetchAll();

// Get forum statistics
$stmt = $db->query("SELECT COUNT(*) FROM topics");
$total_topics = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM replies WHERE is_deleted = 0");
$total_replies = $stmt->fetchColumn();

$stmt = $db->query("SELECT COUNT(*) FROM users WHERE is_active = 1");
$total_users = $stmt->fetchColumn();

// Get most active users
$stmt = $db->prepare("
    SELECT u.display_name, u.user_role, 
           COUNT(t.id) as topic_count,
           (SELECT COUNT(*) FROM replies r WHERE r.user_id = u.id AND r.is_deleted = 0) as reply_count
    FROM users u 
    LEFT JOIN topics t ON u.id = t.user_id
    WHERE u.is_active = 1
    GROUP BY u.id, u.display_name, u.user_role
    ORDER BY (COUNT(t.id) + (SELECT COUNT(*) FROM replies r WHERE r.user_id = u.id AND r.is_deleted = 0)) DESC
    LIMIT 5
");
$stmt->execute();
$active_users = $stmt->fetchAll();

include '../includes/header.php';
?>

<!-- Hero Section -->
<div class="jumbotron bg-primary text-white rounded p-5 mb-4">
    <div class="container-fluid">
        <h1 class="display-4">Welcome to <?php echo SITE_NAME; ?></h1>
        <p class="lead"><?php echo SITE_DESCRIPTION; ?></p>
        <?php if (!is_logged_in()): ?>
            <hr class="my-4">
            <p>Join our community to start discussions and connect with others.</p>
            <div class="d-flex gap-2">
                <a class="btn btn-light btn-lg" href="auth/register.php" role="button">
                    <i class="bi bi-person-plus"></i> Register Now
                </a>
                <a class="btn btn-outline-light btn-lg" href="auth/login.php" role="button">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
            </div>
        <?php else: ?>
            <hr class="my-4">
            <p>Ready to start a new discussion?</p>
            <a class="btn btn-light btn-lg" href="forum/create_topic.php" role="button">
                <i class="bi bi-plus-circle"></i> Create New Topic
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- Recent Topics -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Topics</h5>
                <a href="forum/topics.php" class="btn btn-outline-primary btn-sm">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recent_topics)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-chat-dots display-4"></i>
                        <p class="mt-2">No topics yet. Be the first to start a discussion!</p>
                        <?php if (is_logged_in()): ?>
                            <a href="forum/create_topic.php" class="btn btn-primary">Create First Topic</a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recent_topics as $topic): ?>
                            <div class="list-group-item border-0 px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <?php if ($topic['is_pinned']): ?>
                                                <i class="bi bi-pin text-warning" title="Pinned"></i>
                                            <?php endif; ?>
                                            <?php if ($topic['is_locked']): ?>
                                                <i class="bi bi-lock text-danger" title="Locked"></i>
                                            <?php endif; ?>
                                            <a href="forum/view_topic.php?id=<?php echo $topic['id']; ?>" class="text-decoration-none">
                                                <?php echo sanitize_input($topic['title']); ?>
                                            </a>
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            <?php echo truncate_text(strip_tags($topic['content']), 120); ?>
                                        </p>
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> by <strong><?php echo sanitize_input($topic['author_name']); ?></strong>
                                            <i class="bi bi-clock ms-2"></i> <?php echo time_ago($topic['created_at']); ?>
                                            <i class="bi bi-chat ms-2"></i> <?php echo $topic['reply_count']; ?> replies
                                            <i class="bi bi-eye ms-2"></i> <?php echo $topic['view_count']; ?> views
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Forum Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Forum Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-4">
                        <h4 class="text-primary"><?php echo $total_topics; ?></h4>
                        <small class="text-muted">Topics</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-success"><?php echo $total_replies; ?></h4>
                        <small class="text-muted">Replies</small>
                    </div>
                    <div class="col-4">
                        <h4 class="text-info"><?php echo $total_users; ?></h4>
                        <small class="text-muted">Users</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Most Active Users -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-people"></i> Most Active Users</h6>
            </div>
            <div class="card-body">
                <?php if (empty($active_users)): ?>
                    <p class="text-muted small">No active users yet.</p>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($active_users as $user): ?>
                            <div class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo sanitize_input($user['display_name']); ?></strong>
                                        <span class="badge bg-secondary ms-1"><?php echo ucfirst($user['user_role']); ?></span>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo $user['topic_count'] + $user['reply_count']; ?> posts
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <?php if (is_logged_in()): ?>
            <div class="card mt-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="forum/create_topic.php" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Create New Topic
                        </a>
                        <a href="forum/topics.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-list-ul"></i> Browse All Topics
                        </a>
                        <a href="auth/profile.php" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-person"></i> Edit Profile
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Features Section -->
<div class="row mt-5">
    <div class="col-12">
        <h3 class="text-center mb-4">Forum Features</h3>
    </div>
    <div class="col-md-4">
        <div class="text-center">
            <i class="bi bi-chat-dots display-4 text-primary"></i>
            <h5 class="mt-3">Threaded Discussions</h5>
            <p class="text-muted">Engage in organized conversations with threaded replies and easy-to-follow discussions.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="text-center">
            <i class="bi bi-search display-4 text-success"></i>
            <h5 class="mt-3">Powerful Search</h5>
            <p class="text-muted">Find topics and discussions quickly with our built-in search and filtering capabilities.</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="text-center">
            <i class="bi bi-shield-check display-4 text-info"></i>
            <h5 class="mt-3">Secure & Safe</h5>
            <p class="text-muted">Your data is protected with secure authentication and input validation.</p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

