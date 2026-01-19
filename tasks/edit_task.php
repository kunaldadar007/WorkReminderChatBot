<?php
// tasks/edit_task.php
// Edit an existing task belonging to the logged-in user.

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config.php';

$userId = $_SESSION['user_id'];
$errors = [];

$taskId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch task to edit.
$stmt = $pdo->prepare('SELECT * FROM tasks WHERE id = ? AND user_id = ? LIMIT 1');
$stmt->execute([$taskId, $userId]);
$task = $stmt->fetch();

if (!$task) {
    die('Task not found or not authorized.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? '';
    $due_time = $_POST['due_time'] ?? '';
    $priority = trim($_POST['priority'] ?? 'medium');

    if ($title === '') {
        $errors[] = 'Title is required.';
    }
    if ($due_date === '') {
        $errors[] = 'Due date is required.';
    }
    if ($due_time === '') {
        $errors[] = 'Due time is required.';
    }
    if (!in_array($priority, ['low', 'medium', 'high'], true)) {
        $priority = 'medium';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('
            UPDATE tasks
            SET title = ?, description = ?, due_date = ?, due_time = ?, priority = ?, reminder_sent = 0, updated_at = NOW()
            WHERE id = ? AND user_id = ?
        ');
        $stmt->execute([
            $title,
            $description,
            $due_date,
            $due_time,
            $priority,
            $taskId,
            $userId
        ]);

        header('Location: /WorkReminder/dashboard.php');
        exit;
    }
} else {
    // Pre-fill form variables with current task data.
    $title = $task['title'];
    $description = $task['description'];
    $due_date = $task['due_date'];
    $due_time = $task['due_time'];
    $priority = $task['priority'];
}

include __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-3">Edit Task</h3>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post" action="">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" name="title" class="form-control" required
                               value="<?php echo htmlspecialchars($title); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" required
                                   value="<?php echo htmlspecialchars($due_date); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due Time</label>
                            <input type="time" name="due_time" class="form-control" required
                                   value="<?php echo htmlspecialchars(substr($due_time, 0, 5)); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="low" <?php echo ($priority === 'low') ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo ($priority === 'medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo ($priority === 'high') ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Update Task</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

