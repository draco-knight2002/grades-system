<?php
/*
 * REGISTRAR PENDING GRADES PAGE
 * This file is included by index.php (router)
 * Do not include HTML head/body tags - only content
 */

ob_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/audit.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/notifications.php';

if (!headers_sent()) {
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}

requireRole([2]); // Registrar only

$action_msg = '';
$msg_type = 'success';

// Handle grade approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["approve"])) {
    if (empty($_POST['csrf_token'])) { http_response_code(400); die('Missing CSRF token'); }
    csrf_validate_or_die($_POST['csrf_token']);

    $grade_id = intval($_POST["grade_id"]);

    $stmt = $conn->prepare(
        "UPDATE grades 
         SET status='Approved', is_locked=1 
         WHERE grade_id=?"
    );
    $stmt->bind_param("i", $grade_id);
    $stmt->execute();

    logAction($conn, $_SESSION["user_id"], "Approved grade ID $grade_id");

    // Notify faculty and student
    $q = $conn->prepare("SELECT s.faculty_id, e.student_id FROM grades g JOIN enrollments e ON g.enrollment_id = e.enrollment_id JOIN subjects s ON e.subject_id = s.subject_id WHERE g.grade_id = ?");
    $q->bind_param('i', $grade_id);
    $q->execute();
    $r = $q->get_result();
    if ($r && $row = $r->fetch_assoc()) {
        addNotification($conn, $row['faculty_id'], "Grade ID $grade_id was approved and locked.");
        addNotification($conn, $row['student_id'], "A grade for you was approved by the Registrar.");
    }

    $action_msg = 'Grade approved and locked.';
}

// Handle grade return
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["return"])) {
    if (empty($_POST['csrf_token'])) { http_response_code(400); die('Missing CSRF token'); }
    csrf_validate_or_die($_POST['csrf_token']);

    $grade_id = intval($_POST["grade_id"]);

    $stmt = $conn->prepare(
        "UPDATE grades 
         SET status='Returned' 
         WHERE grade_id=?"
    );
    $stmt->bind_param("i", $grade_id);
    $stmt->execute();

    logAction($conn, $_SESSION["user_id"], "Returned grade ID $grade_id");

    // Notify faculty
    $q = $conn->prepare("SELECT s.faculty_id FROM grades g JOIN enrollments e ON g.enrollment_id = e.enrollment_id JOIN subjects s ON e.subject_id = s.subject_id WHERE g.grade_id = ?");
    $q->bind_param('i', $grade_id);
    $q->execute();
    $r = $q->get_result();
    if ($r && $row = $r->fetch_assoc()) {
        addNotification($conn, $row['faculty_id'], "Grade ID $grade_id was returned by the Registrar.");
    }

    $action_msg = 'Grade returned to faculty.';
    $msg_type = 'warning';
}

// Fetch pending grades
$grades_q = "
SELECT 
    g.grade_id,
    u.full_name AS student,
    s.subject_code,
    gp.period_name,
    g.percentage,
    g.numeric_grade,
    g.status
FROM grades g
JOIN enrollments e ON g.enrollment_id = e.enrollment_id
JOIN users u ON e.student_id = u.user_id
JOIN subjects s ON e.subject_id = s.subject_id
JOIN grading_periods gp ON g.period_id = gp.period_id
WHERE g.status = 'Pending'
ORDER BY g.grade_id DESC
";
$grades_result = $conn->query($grades_q);
?>

<style>
    .registrar-pending-grades { 
        padding: 2rem; 
    }
    .page-header { margin-bottom: 2rem; }
    .page-header h1 { color: #0f246c; font-size: 1.5rem; font-weight: 600; margin-bottom: 0.25rem; }
    .page-header p { color: #64748B; font-size: 0.9375rem; }
    .content-section { margin-bottom: 3rem; }
    .content-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 1rem;
        color: #0f246c;
        border-bottom: 2px solid #3B82F6;
        padding-bottom: 0.5rem;
    }
    .message { 
        padding: 1rem; 
        margin: 1rem 0; 
        border-radius: 4px; 
        background: #d4edda; 
        color: #155724; 
        border: 1px solid #c3e6cb;
    }
    .message.warning {
        background: #fff3cd;
        color: #856404;
        border-color: #ffeeba;
    }
    .table-wrap { overflow-x: auto; margin-bottom: 1rem; }
    table { border-collapse: collapse; width: 100%; background: #fff; border: 1px solid #e1e4e8; border-radius: 4px; overflow: hidden; }
    th, td { border: 1px solid #e1e4e8; padding: 12px; text-align: center; vertical-align: middle; }
    th { background: #f6f8fa; font-weight: 600; color: #0f246c; }
    tr:nth-child(even) { background: #f9f9f9; }
    tr:hover { background: #f0f7ff; }
    button {
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        border: none;
        margin: 2px;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .btn-approve { background: #10b981; color: white; }
    .btn-approve:hover { background: #059669; }
    .btn-reject { background: #ef4444; color: white; }
    .btn-reject:hover { background: #dc2626; }
    form { margin: 0; display: inline; }
    .empty-message { text-align: center; padding: 2rem; color: #64748b; }
</style>

<div class="registrar-pending-grades">
    <div class="page-header">
        <h1>Pending Grade Submissions</h1>
        <p>Review and approve grades submitted by faculty.</p>
    </div>

    <?php if (!empty($action_msg)): ?>
        <div class="message <?= $msg_type !== 'success' ? $msg_type : '' ?>">
            <?= htmlspecialchars($action_msg, ENT_QUOTES) ?>
        </div>
    <?php endif; ?>

    <div class="content-section">
        <div class="content-title" aria-hidden="true"></div>
        
        <?php if ($grades_result && $grades_result->num_rows > 0): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Subject Code</th>
                            <th>Period</th>
                            <th>Percentage</th>
                            <th>Final Grade</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $grades_result->fetch_assoc()): ?>
                        <tr>
                            <form method="post">
                                <?php echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">'; ?>
                                <td><?= htmlspecialchars($row["student"], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row["subject_code"], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row["period_name"], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row["percentage"], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row["numeric_grade"], ENT_QUOTES) ?></td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; background: #fff3cd; color: #856404;">
                                        <?= htmlspecialchars($row["status"], ENT_QUOTES) ?>
                                    </span>
                                </td>
                                <td>
                                    <input type="hidden" name="grade_id" value="<?= htmlspecialchars($row["grade_id"], ENT_QUOTES) ?>">
                                    <button type="submit" name="approve" class="btn-approve">Approve</button>
                                    <button type="submit" name="return" class="btn-reject">Return</button>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-message">
                <p>No pending grades.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
