<?php
// tasks/add_task.php
// Form to create a new task.

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../config.php';

$errors = [];

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
            INSERT INTO tasks (user_id, title, description, due_date, due_time, priority, status, reminder_sent, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, "pending", 0, NOW(), NOW())
        ');
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $description,
            $due_date,
            $due_time,
            $priority
        ]);

        header('Location: /WorkReminder/dashboard.php');
        exit;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-3">Add Task</h3>

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
                               value="<?php echo isset($title) ? htmlspecialchars($title) : ''; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due Date</label>
                            <input type="date" name="due_date" class="form-control" required
                                   value="<?php echo isset($due_date) ? htmlspecialchars($due_date) : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Due Time</label>
                            <input type="time" name="due_time" class="form-control" required
                                   value="<?php echo isset($due_time) ? htmlspecialchars($due_time) : ''; ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            <option value="low" <?php echo (isset($priority) && $priority === 'low') ? 'selected' : ''; ?>>Low</option>
                            <option value="medium" <?php echo (!isset($priority) || $priority === 'medium') ? 'selected' : ''; ?>>Medium</option>
                            <option value="high" <?php echo (isset($priority) && $priority === 'high') ? 'selected' : ''; ?>>High</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Task</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

