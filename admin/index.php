<?php
// admin/index.php
// Simple admin panel: view/delete users and tasks.

require_once __DIR__ . '/../includes/admin_check.php';
require_once __DIR__ . '/../config.php';

// Fetch users
$usersStmt = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC');
$users = $usersStmt->fetchAll();

// Fetch tasks with user info
$tasksStmt = $pdo->query('
    SELECT t.id, t.title, t.due_date, t.due_time, t.status, t.priority, u.name AS owner
    FROM tasks t
    JOIN users u ON u.id = t.user_id
    ORDER BY t.due_date DESC, t.due_time DESC
');
$tasks = $tasksStmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="mb-4">
    <h2 class="mb-1">Admin Panel</h2>
    <p class="text-muted mb-0 small">Manage users and tasks. Deletions are irreversible.</p>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-dark text-white">Users</div>
            <div class="card-body table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?php echo (int)$u['id']; ?></td>
                            <td><?php echo htmlspecialchars($u['name']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><span class="badge bg-<?php echo $u['role'] === 'admin' ? 'danger' : 'secondary'; ?>">
                                    <?php echo htmlspecialchars($u['role']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($u['role'] !== 'admin'): ?>
                                    <a class="btn btn-sm btn-outline-danger"
                                       href="/WorkReminder/admin/delete_user.php?id=<?php echo (int)$u['id']; ?>"
                                       onclick="return confirm('Delete this user and all their tasks?');">
                                        Delete
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Protected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">Tasks</div>
            <div class="card-body table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Owner</th>
                        <th>Due</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tasks as $t): ?>
                        <tr>
                            <td><?php echo (int)$t['id']; ?></td>
                            <td><?php echo htmlspecialchars($t['title']); ?></td>
                            <td><?php echo htmlspecialchars($t['owner']); ?></td>
                            <td><?php echo htmlspecialchars($t['due_date']); ?> <?php echo htmlspecialchars(substr($t['due_time'],0,5)); ?></td>
                            <td><?php echo htmlspecialchars($t['status']); ?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-danger"
                                   href="/WorkReminder/admin/delete_task.php?id=<?php echo (int)$t['id']; ?>"
                                   onclick="return confirm('Delete this task?');">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

