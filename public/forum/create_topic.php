<?php
require_once '../../config/config.php';

$page_title = 'Create New Topic';
require_login();

$errors = [];
$success = false;

// Process topic creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid security token. Please try again.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        
        // Validate input
        if (empty($title)) {
            $errors[] = 'Topic title is required.';
        } elseif (strlen($title) < 5 || strlen($title) > 255) {
            $errors[] = 'Topic title must be between 5 and 255 characters.';
        }
        
        if (empty($content)) {
            $errors[] = 'Topic content is required.';
        } elseif (strlen($content) < 10) {
            $errors[] = 'Topic content must be at least 10 characters long.';
        }
        
        // Create topic if no errors
        if (empty($errors)) {
            try {
                $db = getDB();
                $clean_content = clean_content($content);
                
                $stmt = $db->prepare("INSERT INTO topics (title, content, user_id) VALUES (?, ?, ?)");
                $stmt->execute([$title, $clean_content, $_SESSION['user_id']]);
                
                $topic_id = $db->lastInsertId();
                
                $_SESSION['flash_message'] = 'Topic created successfully!';
                $_SESSION['flash_type'] = 'success';
                header("Location: view_topic.php?id=$topic_id");
                exit;
            } catch (PDOException $e) {
                $errors[] = 'Failed to create topic. Please try again.';
            }
        }
    }
}

include '../../includes/header.php';
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Topic</h4>
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
                        <label for="title" class="form-label">Topic Title</label>
                        <input type="text" class="form-control" id="title" name="title" 
                               value="<?php echo sanitize_input($_POST['title'] ?? ''); ?>" 
                               required maxlength="255" minlength="5"
                               placeholder="Enter a descriptive title for your topic">
                        <div class="form-text">5-255 characters. Be clear and descriptive.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="8" 
                                  required minlength="10"
                                  placeholder="Write your topic content here. You can use basic HTML formatting."><?php echo sanitize_input($_POST['content'] ?? ''); ?></textarea>
                        <div class="form-text">
                            Minimum 10 characters. Basic HTML tags are allowed: &lt;p&gt;, &lt;br&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;u&gt;, &lt;ol&gt;, &lt;ul&gt;, &lt;li&gt;, &lt;blockquote&gt;
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create Topic
                        </button>
                        <a href="topics.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-info-circle"></i> Posting Guidelines</h6>
            </div>
            <div class="card-body">
                <ul class="small mb-0">
                    <li>Choose a clear, descriptive title</li>
                    <li>Provide enough detail in your content</li>
                    <li>Be respectful and constructive</li>
                    <li>Search existing topics before posting</li>
                    <li>Use proper formatting for readability</li>
                </ul>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="bi bi-code"></i> Formatting Help</h6>
            </div>
            <div class="card-body">
                <div class="small">
                    <p><strong>Bold text:</strong> &lt;strong&gt;text&lt;/strong&gt;</p>
                    <p><strong>Italic text:</strong> &lt;em&gt;text&lt;/em&gt;</p>
                    <p><strong>Line break:</strong> &lt;br&gt;</p>
                    <p><strong>Paragraph:</strong> &lt;p&gt;text&lt;/p&gt;</p>
                    <p><strong>Quote:</strong> &lt;blockquote&gt;text&lt;/blockquote&gt;</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-resize textarea
document.getElementById('content').addEventListener('input', function() {
    this.style.height = 'auto';
    this.style.height = this.scrollHeight + 'px';
});
</script>

<?php include '../../includes/footer.php'; ?>

