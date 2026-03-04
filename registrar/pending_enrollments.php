<?php
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

requireRole([2]); // Registrar

$action_msg = '';
$msg_type = 'success';

// Handle approval
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_enrollment'])) {
    if (empty($_POST['csrf_token'])) { http_response_code(400); die('Missing CSRF token'); }
    csrf_validate_or_die($_POST['csrf_token']);

    $request_id = intval($_POST['request_id'] ?? 0);
    $decision_notes = trim($_POST['decision_notes'] ?? '');

    // Validate and approve enrollment
    $chk = $conn->prepare("SELECT student_id, subject_id, status FROM enrollment_requests WHERE request_id = ?");
    $chk->bind_param('i', $request_id);
    $chk->execute();
    $cres = $chk->get_result();
    if ($cres && $crow = $cres->fetch_assoc()) {
        if ($crow['status'] === 'Pending') {
            $student_id = intval($crow['student_id']);
            $subject_id = intval($crow['subject_id']);

            // Get semester_id
            $sem = $conn->prepare("SELECT semester_id FROM semesters ORDER BY semester_id DESC LIMIT 1");
            $sem->execute();
            $sr = $sem->get_result();
            if ($sr && $srow = $sr->fetch_assoc()) {
                $semester_id = intval($srow['semester_id']);

                // Create enrollment
                $ins = $conn->prepare("INSERT INTO enrollments (student_id, subject_id, semester_id) VALUES (?, ?, ?)");
                $ins->bind_param('iii', $student_id, $subject_id, $semester_id);
                $ins->execute();

                // Update request
                $upd = $conn->prepare("UPDATE enrollment_requests SET status = 'Approved', registrar_id = ?, decision_notes = ?, decision_date = NOW() WHERE request_id = ?");
                $upd->bind_param('isi', $_SESSION['user_id'], $decision_notes, $request_id);
                $upd->execute();

                logAction($conn, $_SESSION['user_id'], "Approved enrollment request $request_id");
                addNotification($conn, $student_id, "Your enrollment request #$request_id was approved.");

                $action_msg = 'Enrollment approved.';
            }
        }
    }
}

// Handle rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reject_enrollment'])) {
    if (empty($_POST['csrf_token'])) { http_response_code(400); die('Missing CSRF token'); }
    csrf_validate_or_die($_POST['csrf_token']);

    $request_id = intval($_POST['request_id'] ?? 0);
    $decision_notes = trim($_POST['decision_notes'] ?? '');

    $upd = $conn->prepare("UPDATE enrollment_requests SET status = 'Rejected', registrar_id = ?, decision_notes = ?, decision_date = NOW() WHERE request_id = ?");
    $upd->bind_param('isi', $_SESSION['user_id'], $decision_notes, $request_id);
    $upd->execute();

    logAction($conn, $_SESSION['user_id'], "Rejected enrollment request $request_id");

    $action_msg = 'Enrollment rejected.';
    $msg_type = 'warning';
}

// Fetch pending enrollments
$er_q = "
SELECT er.request_id, er.student_id, er.subject_id, u.full_name AS student_name, s.subject_code, s.subject_name, er.request_date, er.status
FROM enrollment_requests er
JOIN users u ON er.student_id = u.user_id
JOIN subjects s ON er.subject_id = s.subject_id
WHERE er.status = 'Pending'
ORDER BY er.request_date DESC
";
$er_result = $conn->query($er_q);

logAction($conn, $_SESSION['user_id'], "Viewed pending enrollment requests");
?>

<style>
    .registrar-pending-enrollments { 
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
    th, td { border: 1px solid #e1e4e8; padding: 12px; text-align: left; vertical-align: middle; }
    th { background: #f6f8fa; font-weight: 600; color: #0f246c; }
    tr:nth-child(even) { background: #f9f9f9; }
    tr:hover { background: #f0f7ff; }
    input[type="text"] { padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9rem; width: 160px; }
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

<div class="registrar-pending-enrollments">
    <div class="page-header">
        <h1>Pending Enrollment Requests</h1>
        <p>Review and approve student enrollment requests.</p>
    </div>

    <?php if (!empty($action_msg)): ?>
        <div class="message <?= $msg_type !== 'success' ? $msg_type : '' ?>">
            <?= htmlspecialchars($action_msg, ENT_QUOTES) ?>
        </div>
    <?php endif; ?>

    <div class="content-section">
        <div class="content-title" aria-hidden="true"></div>
        
        <?php if ($er_result && $er_result->num_rows > 0): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Student Name</th>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Request Date</th>
                            <th>Status</th>
                            <th>Decision Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $er_result->fetch_assoc()): ?>
                        <tr>
                            <form method="post">
                                <?php echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">'; ?>
                                <td><?= htmlspecialchars($row['request_id'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['student_name'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['subject_code'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['subject_name'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['request_date'], ENT_QUOTES) ?></td>
                                <td>
                                    <span style="padding: 4px 8px; border-radius: 4px; background: #fff3cd; color: #856404;">
                                        <?= htmlspecialchars($row['status'], ENT_QUOTES) ?>
                                    </span>
                                </td>
                                <td>
                                    <input type="text" name="decision_notes" placeholder="Notes (optional)">
                                </td>
                                <td>
                                    <input type="hidden" name="request_id" value="<?= htmlspecialchars($row['request_id'], ENT_QUOTES) ?>">
                                    <button type="submit" name="approve_enrollment" class="btn-approve">Approve</button>
                                    <button type="submit" name="reject_enrollment" class="btn-reject">Reject</button>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-message">
                <p>No pending enrollment requests.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
