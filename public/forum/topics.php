<?php
require_once '../../config/config.php';

$page_title = 'Forum Topics';
$page = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'latest';

// Build search query
$where_clause = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $where_clause .= " AND (t.title LIKE ? OR t.content LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
}

// Build sort clause
$sort_clause = match($sort) {
    'oldest' => 'ORDER BY t.is_pinned DESC, t.created_at ASC',
    'replies' => 'ORDER BY t.is_pinned DESC, t.reply_count DESC, t.created_at DESC',
    'views' => 'ORDER BY t.is_pinned DESC, t.view_count DESC, t.created_at DESC',
    default => 'ORDER BY t.is_pinned DESC, t.last_reply_at DESC, t.created_at DESC'
};

// Get total count for pagination
$db = getDB();
$count_sql = "SELECT COUNT(*) FROM topics t $where_clause";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total_topics = $stmt->fetchColumn();

$pagination = get_pagination($total_topics, TOPICS_PER_PAGE, $page);

// Get topics
$offset = $pagination['offset'];
$sql = "
    SELECT t.*, 
           u.username, u.display_name as author_name,
           lr_u.display_name as last_reply_author
    FROM topics t 
    JOIN users u ON t.user_id = u.id 
    LEFT JOIN users lr_u ON t.last_reply_user_id = lr_u.id
    $where_clause 
    $sort_clause 
    LIMIT ? OFFSET ?
";

$params[] = TOPICS_PER_PAGE;
$params[] = $offset;

$stmt = $db->prepare($sql);
$stmt->execute($params);
$topics = $stmt->fetchAll();

include '../../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="bi bi-list-ul"></i> Forum Topics</h1>
    <?php if (is_logged_in()): ?>
        <a href="create_topic.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Topic
        </a>
    <?php endif; ?>
</div>

<!-- Search and Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <div class="col-md-6">
                <label for="search" class="form-label">Search Topics</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="<?php echo sanitize_input($search); ?>" 
                       placeholder="Search by title or content...">
            </div>
            <div class="col-md-4">
                <label for="sort" class="form-label">Sort By</label>
                <select class="form-select" id="sort" name="sort">
                    <option value="latest" <?php echo $sort === 'latest' ? 'selected' : ''; ?>>Latest Activity</option>
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="replies" <?php echo $sort === 'replies' ? 'selected' : ''; ?>>Most Replies</option>
                    <option value="views" <?php echo $sort === 'views' ? 'selected' : ''; ?>>Most Views</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
        
        <?php if (!empty($search)): ?>
            <div class="mt-2">
                <small class="text-muted">
                    Showing results for: <strong><?php echo sanitize_input($search); ?></strong>
                    <a href="topics.php" class="ms-2">Clear search</a>
                </small>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Topics List -->
<?php if (empty($topics)): ?>
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> 
        <?php if (!empty($search)): ?>
            No topics found matching your search criteria.
        <?php else: ?>
            No topics have been created yet. 
            <?php if (is_logged_in()): ?>
                <a href="create_topic.php">Create the first topic!</a>
            <?php else: ?>
                <a href="auth/login.php">Login</a> to create the first topic!
            <?php endif; ?>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Topic</th>
                        <th class="text-center d-none d-md-table-cell">Replies</th>
                        <th class="text-center d-none d-md-table-cell">Views</th>
                        <th class="text-center">Last Activity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topics as $topic): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <?php if ($topic['is_pinned']): ?>
                                                <i class="bi bi-pin text-warning" title="Pinned"></i>
                                            <?php endif; ?>
                                            <?php if ($topic['is_locked']): ?>
                                                <i class="bi bi-lock text-danger" title="Locked"></i>
                                            <?php endif; ?>
                                            <a href="view_topic.php?id=<?php echo $topic['id']; ?>" class="text-decoration-none">
                                                <?php echo sanitize_input($topic['title']); ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-person"></i> by <strong><?php echo sanitize_input($topic['author_name']); ?></strong>
                                            <i class="bi bi-clock ms-2"></i> <?php echo time_ago($topic['created_at']); ?>
                                        </small>
                                        <div class="d-md-none mt-1">
                                            <small class="text-muted">
                                                <i class="bi bi-chat"></i> <?php echo $topic['reply_count']; ?> replies
                                                <i class="bi bi-eye ms-2"></i> <?php echo $topic['view_count']; ?> views
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <span class="badge bg-primary"><?php echo $topic['reply_count']; ?></span>
                            </td>
                            <td class="text-center d-none d-md-table-cell">
                                <span class="badge bg-secondary"><?php echo $topic['view_count']; ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($topic['last_reply_at']): ?>
                                    <small class="text-muted">
                                        <?php echo time_ago($topic['last_reply_at']); ?><br>
                                        by <strong><?php echo sanitize_input($topic['last_reply_author']); ?></strong>
                                    </small>
                                <?php else: ?>
                                    <small class="text-muted">No replies</small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
        <nav aria-label="Topics pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <?php if ($pagination['has_prev']): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $pagination['current_page'] - 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>">Previous</a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $pagination['current_page'] - 2); $i <= min($pagination['total_pages'], $pagination['current_page'] + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($pagination['has_next']): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $pagination['current_page'] + 1; ?>&search=<?php echo urlencode($search); ?>&sort=<?php echo $sort; ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        
        <div class="text-center text-muted small">
            Showing <?php echo $pagination['offset'] + 1; ?>-<?php echo min($pagination['offset'] + TOPICS_PER_PAGE, $total_topics); ?> 
            of <?php echo $total_topics; ?> topics
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Forum Statistics -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h6 class="card-title"><i class="bi bi-bar-chart"></i> Forum Statistics</h6>
                <div class="row text-center">
                    <?php
                    // Get forum statistics
                    $stmt = $db->query("SELECT COUNT(*) FROM topics");
                    $total_topics_count = $stmt->fetchColumn();
                    
                    $stmt = $db->query("SELECT COUNT(*) FROM replies WHERE is_deleted = 0");
                    $total_replies_count = $stmt->fetchColumn();
                    
                    $stmt = $db->query("SELECT COUNT(*) FROM users WHERE is_active = 1");
                    $total_users_count = $stmt->fetchColumn();
                    
                    $stmt = $db->query("SELECT display_name FROM users WHERE is_active = 1 ORDER BY join_date DESC LIMIT 1");
                    $newest_user = $stmt->fetchColumn();
                    ?>
                    <div class="col-6 col-md-3">
                        <h5 class="text-primary"><?php echo $total_topics_count; ?></h5>
                        <small class="text-muted">Total Topics</small>
                    </div>
                    <div class="col-6 col-md-3">
                        <h5 class="text-success"><?php echo $total_replies_count; ?></h5>
                        <small class="text-muted">Total Replies</small>
                    </div>
                    <div class="col-6 col-md-3">
                        <h5 class="text-info"><?php echo $total_users_count; ?></h5>
                        <small class="text-muted">Total Users</small>
                    </div>
                    <div class="col-6 col-md-3">
                        <h6 class="text-warning"><?php echo sanitize_input($newest_user ?: 'None'); ?></h6>
                        <small class="text-muted">Newest User</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

