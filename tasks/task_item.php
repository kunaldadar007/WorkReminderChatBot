<?php
// tasks/task_item.php
// Partial used to render a task card.
// Expects $task array from parent context.

$isCompleted = ($task['status'] === 'completed');
$priority = htmlspecialchars($task['priority']);
$badgeClass = match (strtolower($task['priority'])) {
    'high' => 'bg-danger',
    'medium' => 'bg-warning text-dark',
    default => 'bg-info text-dark',
};
?>

<div class="d-flex align-items-start justify-content-between border-bottom pb-2 mb-2">
    <div class="<?php echo $isCompleted ? 'task-completed' : ''; ?>">
        <div class="fw-bold">
            <?php echo htmlspecialchars($task['title']); ?>
            <span class="badge <?php echo $badgeClass; ?> task-badge-priority ms-2">
                <?php echo $priority; ?>
            </span>
        </div>
        <div class="text-muted small">
            <?php echo htmlspecialchars($task['description']); ?>
        </div>
        <div class="small">
            Due: <?php echo htmlspecialchars($task['due_date']); ?> at <?php echo htmlspecialchars(substr($task['due_time'], 0, 5)); ?>
        </div>
    </div>
    <div class="ms-3 text-end">
        <?php if (!$isCompleted): ?>
            <a href="/WorkReminder/tasks/complete_task.php?id=<?php echo (int)$task['id']; ?>" class="btn btn-sm btn-success mb-1">Mark Done</a>
            <a href="/WorkReminder/tasks/edit_task.php?id=<?php echo (int)$task['id']; ?>" class="btn btn-sm btn-primary mb-1">Edit</a>
        <?php endif; ?>
        <a href="/WorkReminder/tasks/delete_task.php?id=<?php echo (int)$task['id']; ?>"
           class="btn btn-sm btn-outline-danger mb-1"
           onclick="return confirm('Delete this task?');">
            Delete
        </a>
    </div>
</div>

