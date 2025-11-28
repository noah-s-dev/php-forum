<?php
require_once '../../config/config.php';

$topic_id = (int)($_GET['id'] ?? 0);
$page = max(1, (int)($_GET['page'] ?? 1));

if (!$topic_id) {
    header('Location: topics.php');
    exit;
}

$errors = [];
$reply_success = false;

// Get topic details
$db = getDB();
$stmt = $db->prepare("
    SELECT t.*, u.username, u.display_name, u.user_role, u.join_date
    FROM topics t 
    JOIN users u ON t.user_id = u.id 
    WHERE t.id = ?
");
$stmt->execute([$topic_id]);
$topic = $stmt->fetch();

if (!$topic) {
    header('Location: topics.php');
    exit;
}

$page_title = $topic['title'];

// Update view count
$stmt = $db->prepare("UPDATE topics SET view_count = view_count + 1 WHERE id = ?");
$stmt->execute([$topic_id]);

// Process reply submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && is_logged_in()) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $content = trim($_POST['content'] ?? '');
        $parent_reply_id = !empty($_POST['parent_reply_id']) ? (int)$_POST['parent_reply_id'] : null;
        
        // Validate input
        if (empty($content)) {
            $errors[] = 'Reply content is required.';
        } elseif (strlen($content) < 5) {
            $errors[] = 'Reply content must be at least 5 characters long.';
        }
        
        // Check if topic is locked
        if ($topic['is_locked'] && !is_admin_or_moderator()) {
            $errors[] = 'This topic is locked and cannot receive new replies.';
        }
        
        // Create reply if no errors
        if (empty($errors)) {
            try {
                $clean_content = clean_content($content);
                
                $stmt = $db->prepare("INSERT INTO replies (topic_id, user_id, content, parent_reply_id) VALUES (?, ?, ?, ?)");
                $stmt->execute([$topic_id, $_SESSION['user_id'], $clean_content, $parent_reply_id]);
                
                // Update topic reply count and last reply info
                $stmt = $db->prepare("UPDATE topics SET reply_count = reply_count + 1, last_reply_at = NOW(), last_reply_user_id = ? WHERE id = ?");
                $stmt->execute([$_SESSION['user_id'], $topic_id]);
                
                $reply_success = true;
                $_SESSION['flash_message'] = 'Reply posted successfully!';
                $_SESSION['flash_type'] = 'success';
                header("Location: view_topic.php?id=$topic_id&page=$page#replies");
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Failed to post reply. Please try again.';
            }
        }
    }
}

// Get replies with pagination
$offset = ($page - 1) * REPLIES_PER_PAGE;
$stmt = $db->prepare("
    SELECT r.*, u.username, u.display_name, u.user_role, u.join_date,
           parent_u.display_name as parent_author
    FROM replies r 
    JOIN users u ON r.user_id = u.id 
    LEFT JOIN replies parent_r ON r.parent_reply_id = parent_r.id
    LEFT JOIN users parent_u ON parent_r.user_id = parent_u.id
    WHERE r.topic_id = ? AND r.is_deleted = 0
    ORDER BY r.created_at ASC 
    LIMIT ? OFFSET ?
");
$stmt->execute([$topic_id, REPLIES_PER_PAGE, $offset]);
$replies = $stmt->fetchAll();

// Get total reply count for pagination
$stmt = $db->prepare("SELECT COUNT(*) FROM replies WHERE topic_id = ? AND is_deleted = 0");
$stmt->execute([$topic_id]);
$total_replies = $stmt->fetchColumn();

$pagination = get_pagination($total_replies, REPLIES_PER_PAGE, $page);

include '../../includes/header.php';
?>

<!-- Topic Header -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-start">
        <div>
            <h1 class="h4 mb-1"><?php echo sanitize_input($topic['title']); ?></h1>
            <div class="text-muted small">
                <i class="bi bi-person"></i> By <strong><?php echo sanitize_input($topic['display_name']); ?></strong>
                <i class="bi bi-clock ms-2"></i> <?php echo time_ago($topic['created_at']); ?>
                <i class="bi bi-eye ms-2"></i> <?php echo $topic['view_count']; ?> views
                <i class="bi bi-chat ms-2"></i> <?php echo $topic['reply_count']; ?> replies
            </div>
        </div>
        <div class="text-end">
            <?php if ($topic['is_pinned']): ?>
                <span class="badge bg-warning"><i class="bi bi-pin"></i> Pinned</span>
            <?php endif; ?>
            <?php if ($topic['is_locked']): ?>
                <span class="badge bg-danger"><i class="bi bi-lock"></i> Locked</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="topic-content">
            <?php echo $topic['content']; ?>
        </div>
        <hr>
        <div class="d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                <i class="bi bi-person-badge"></i> 
                <?php echo ucfirst($topic['user_role']); ?> since <?php echo format_date($topic['join_date']); ?>
            </div>
            <div>
                <a href="topics.php" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Back to Topics
                </a>
                <?php if (is_logged_in() && ($_SESSION['user_id'] == $topic['user_id'] || is_admin_or_moderator())): ?>
                    <a href="edit_topic.php?id=<?php echo $topic_id; ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Replies Section -->
<div id="replies">
    <h5><i class="bi bi-chat-dots"></i> Replies (<?php echo $total_replies; ?>)</h5>
    
    <?php if (empty($replies)): ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No replies yet. Be the first to reply!
        </div>
    <?php else: ?>
        <?php foreach ($replies as $reply): ?>
            <div class="card mb-3" id="reply-<?php echo $reply['id']; ?>">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <strong><?php echo sanitize_input($reply['display_name']); ?></strong>
                        <span class="badge bg-secondary ms-1"><?php echo ucfirst($reply['user_role']); ?></span>
                        <?php if ($reply['parent_reply_id']): ?>
                            <small class="text-muted ms-2">
                                <i class="bi bi-reply"></i> Replying to <?php echo sanitize_input($reply['parent_author']); ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted">
                        <i class="bi bi-clock"></i> <?php echo time_ago($reply['created_at']); ?>
                    </small>
                </div>
                <div class="card-body">
                    <div class="reply-content">
                        <?php echo $reply['content']; ?>
                    </div>
                    <div class="mt-2">
                        <?php if (is_logged_in() && !$topic['is_locked']): ?>
                            <button class="btn btn-outline-primary btn-sm reply-btn" 
                                    data-reply-id="<?php echo $reply['id']; ?>"
                                    data-author="<?php echo sanitize_input($reply['display_name']); ?>">
                                <i class="bi bi-reply"></i> Reply
                            </button>
                        <?php endif; ?>
                        <?php if (is_logged_in() && ($_SESSION['user_id'] == $reply['user_id'] || is_admin_or_moderator())): ?>
                            <a href="edit_reply.php?id=<?php echo $reply['id']; ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <nav aria-label="Replies pagination">
                <ul class="pagination justify-content-center">
                    <?php if ($pagination['has_prev']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?id=<?php echo $topic_id; ?>&page=<?php echo $pagination['current_page'] - 1; ?>#replies">Previous</a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                            <a class="page-link" href="?id=<?php echo $topic_id; ?>&page=<?php echo $i; ?>#replies"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['has_next']): ?>
                        <li class="page-item">
                            <a class="page-link" href="?id=<?php echo $topic_id; ?>&page=<?php echo $pagination['current_page'] + 1; ?>#replies">Next</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<!-- Reply Form -->
<?php if (is_logged_in() && !$topic['is_locked']): ?>
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0"><i class="bi bi-plus-circle"></i> Post a Reply</h6>
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
            
            <form method="POST" action="" id="reply-form">
                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                <input type="hidden" name="parent_reply_id" id="parent_reply_id" value="">
                
                <div id="reply-context" class="alert alert-info" style="display: none;">
                    <i class="bi bi-reply"></i> Replying to <strong id="reply-author"></strong>
                    <button type="button" class="btn-close float-end" id="cancel-reply"></button>
                </div>
                
                <div class="mb-3">
                    <label for="content" class="form-label">Your Reply</label>
                    <textarea class="form-control" id="content" name="content" rows="4" 
                              required minlength="5"
                              placeholder="Write your reply here..."><?php echo sanitize_input($_POST['content'] ?? ''); ?></textarea>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Post Reply
                    </button>
                    <button type="button" class="btn btn-secondary" id="cancel-reply-btn" style="display: none;">
                        Cancel Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php elseif (!is_logged_in()): ?>
    <div class="alert alert-info mt-4">
        <i class="bi bi-info-circle"></i> 
                        <a href="../auth/login.php">Login</a> or <a href="../auth/register.php">register</a> to post a reply.
    </div>
<?php elseif ($topic['is_locked']): ?>
    <div class="alert alert-warning mt-4">
        <i class="bi bi-lock"></i> This topic is locked and cannot receive new replies.
    </div>
<?php endif; ?>

<script>
// Handle reply to specific comment
document.querySelectorAll('.reply-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const replyId = this.dataset.replyId;
        const author = this.dataset.author;
        
        document.getElementById('parent_reply_id').value = replyId;
        document.getElementById('reply-author').textContent = author;
        document.getElementById('reply-context').style.display = 'block';
        document.getElementById('cancel-reply-btn').style.display = 'inline-block';
        
        // Scroll to reply form
        document.getElementById('reply-form').scrollIntoView({ behavior: 'smooth' });
        document.getElementById('content').focus();
    });
});

// Cancel reply
document.getElementById('cancel-reply').addEventListener('click', cancelReply);
document.getElementById('cancel-reply-btn').addEventListener('click', cancelReply);

function cancelReply() {
    document.getElementById('parent_reply_id').value = '';
    document.getElementById('reply-context').style.display = 'none';
    document.getElementById('cancel-reply-btn').style.display = 'none';
}

// Auto-resize textarea
document.getElementById('content').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});
</script>

<?php include '../../includes/footer.php'; ?>

