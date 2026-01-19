<?php
// dashboard.php
// Shows today's, upcoming, and completed tasks for the logged-in user.

require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config.php';

$userId = $_SESSION['user_id'];

// Fetch tasks grouped by status/date.
$today = date('Y-m-d');

// Today's tasks (pending)
$stmtToday = $pdo->prepare('
    SELECT * FROM tasks
    WHERE user_id = ? AND status = "pending" AND due_date = ?
    ORDER BY due_time ASC
');
$stmtToday->execute([$userId, $today]);
$tasksToday = $stmtToday->fetchAll();

// This month's tasks (pending, current month)
$stmtMonth = $pdo->prepare('
    SELECT * FROM tasks
    WHERE user_id = ?
      AND status = "pending"
      AND MONTH(due_date) = MONTH(?)
      AND YEAR(due_date) = YEAR(?)
    ORDER BY due_date ASC, due_time ASC
');
$stmtMonth->execute([$userId, $today, $today]);
$tasksMonth = $stmtMonth->fetchAll();

// Upcoming tasks (pending, future dates)
$stmtUpcoming = $pdo->prepare('
    SELECT * FROM tasks
    WHERE user_id = ? AND status = "pending" AND due_date > ?
    ORDER BY due_date ASC, due_time ASC
');
$stmtUpcoming->execute([$userId, $today]);
$tasksUpcoming = $stmtUpcoming->fetchAll();

// Completed tasks
$stmtCompleted = $pdo->prepare('
    SELECT * FROM tasks
    WHERE user_id = ? AND status = "completed"
    ORDER BY updated_at DESC
');
$stmtCompleted->execute([$userId]);
$tasksCompleted = $stmtCompleted->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="mb-0">Dashboard</h2>
    <a href="/WorkReminder/tasks/add_task.php" class="btn btn-success">+ Add Task</a>
</div>

<!-- This Month -->
<div class="card mb-3 shadow-sm">
    <div class="card-header bg-info text-white">
        This Month
    </div>
    <div class="card-body">
        <?php if (empty($tasksMonth)): ?>
            <p class="text-muted mb-0">No tasks scheduled for this month.</p>
        <?php else: ?>
            <?php foreach ($tasksMonth as $task): ?>
                <?php include __DIR__ . '/tasks/task_item.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Notification status + fallback -->
<div class="alert alert-info" id="notification-status" style="display:none;"></div>
<div class="alert alert-warning notification-fallback" id="notification-fallback" style="display:none;">
    Notifications are blocked. You can still get reminders:
    <a href="#" id="email-fallback" class="ms-2">Email me</a> or
    <a href="#" id="whatsapp-fallback" class="ms-2">Send via WhatsApp</a>.
</div>

<!-- Today -->
<div class="card mb-3 shadow-sm">
    <div class="card-header bg-primary text-white">
        Today (<?php echo htmlspecialchars($today); ?>)
    </div>
    <div class="card-body">
        <?php if (empty($tasksToday)): ?>
            <p class="text-muted mb-0">No tasks for today.</p>
        <?php else: ?>
            <?php foreach ($tasksToday as $task): ?>
                <?php include __DIR__ . '/tasks/task_item.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Upcoming -->
<div class="card mb-3 shadow-sm">
    <div class="card-header bg-secondary text-white">
        Upcoming
    </div>
    <div class="card-body">
        <?php if (empty($tasksUpcoming)): ?>
            <p class="text-muted mb-0">No upcoming tasks.</p>
        <?php else: ?>
            <?php foreach ($tasksUpcoming as $task): ?>
                <?php include __DIR__ . '/tasks/task_item.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Completed -->
<div class="card mb-3 shadow-sm">
    <div class="card-header bg-success text-white">
        Completed
    </div>
    <div class="card-body">
        <?php if (empty($tasksCompleted)): ?>
            <p class="text-muted mb-0">No completed tasks yet.</p>
        <?php else: ?>
            <?php foreach ($tasksCompleted as $task): ?>
                <?php include __DIR__ . '/tasks/task_item.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Notifications JS -->
<script src="/WorkReminder/assets/js/notifications.js"></script>
<script>
    // Initialize notification handling for this page.
    window.addEventListener('DOMContentLoaded', () => {
        if (window.WorkReminderNotifications) {
            window.WorkReminderNotifications.init({
                apiEndpoint: '/WorkReminder/tasks/api_get_due_tasks.php'
            });
        }
    });
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>

