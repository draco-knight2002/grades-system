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

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token'])) { http_response_code(400); die('Missing CSRF token'); }
    csrf_validate_or_die($_POST['csrf_token']);

    // Approve correction
    if (isset($_POST['approve'])) {
        $request_id = intval($_POST['request_id'] ?? 0);
        $grade_id = intval($_POST['grade_id'] ?? 0);
        $faculty_id = intval($_POST['faculty_id'] ?? 0);
        $decision_notes = trim($_POST['decision_notes'] ?? '');

        $conn->begin_transaction();
        try {
            // Verify request is still pending
            $chk = $conn->prepare("SELECT status FROM grade_corrections WHERE request_id = ? FOR UPDATE");
            $chk->bind_param('i', $request_id);
            $chk->execute();
            $cres = $chk->get_result();
            if (!($cres && $crow = $cres->fetch_assoc()) || $crow['status'] !== 'Pending') {
                $conn->rollback();
                http_response_code(409);
                echo "This request has already been processed.";
                exit;
            }

            // Unlock grade and mark as Returned
            $u1 = $conn->prepare("UPDATE grades SET is_locked = 0, status = 'Returned' WHERE grade_id = ?");
            $u1->bind_param('i', $grade_id);
            $u1->execute();

            // Mark correction Approved
            $u2 = $conn->prepare("UPDATE grade_corrections SET status = 'Approved', registrar_id = ?, decision_notes = ?, decision_date = NOW() WHERE request_id = ?");
            $u2->bind_param('isi', $_SESSION['user_id'], $decision_notes, $request_id);
            $u2->execute();

            logAction($conn, $_SESSION['user_id'], "Approved correction request ID $request_id for grade ID $grade_id");
            addNotification($conn, $faculty_id, "Your correction request #$request_id was approved; grade unlocked for resubmission.");

            $conn->commit();

            $action_msg = 'Correction request approved — grade unlocked and returned to faculty.';
        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            $action_msg = 'Server error processing request.';
            $msg_type = 'error';
        }
    }

    // Reject correction
    if (isset($_POST['reject'])) {
        $request_id = intval($_POST['request_id'] ?? 0);
        $faculty_id = intval($_POST['faculty_id'] ?? 0);
        $decision_notes = trim($_POST['decision_notes'] ?? '');

        $conn->begin_transaction();
        try {
            // Verify request is still pending
            $chk = $conn->prepare("SELECT status FROM grade_corrections WHERE request_id = ? FOR UPDATE");
            $chk->bind_param('i', $request_id);
            $chk->execute();
            $cres = $chk->get_result();
            if (!($cres && $crow = $cres->fetch_assoc()) || $crow['status'] !== 'Pending') {
                $conn->rollback();
                http_response_code(409);
                echo "This request has already been processed.";
                exit;
            }

            // Mark correction Rejected
            $u = $conn->prepare("UPDATE grade_corrections SET status = 'Rejected', registrar_id = ?, decision_notes = ?, decision_date = NOW() WHERE request_id = ?");
            $u->bind_param('isi', $_SESSION['user_id'], $decision_notes, $request_id);
            $u->execute();

            logAction($conn, $_SESSION['user_id'], "Rejected correction request ID $request_id");
            addNotification($conn, $faculty_id, "Your correction request #$request_id was rejected. Notes: $decision_notes");

            $conn->commit();

            $action_msg = 'Correction request rejected.';
            $msg_type = 'warning';
        } catch (Exception $e) {
            $conn->rollback();
            http_response_code(500);
            $action_msg = 'Server error processing request.';
            $msg_type = 'error';
        }
    }
}

// Fetch pending correction requests
$stmt = $conn->prepare(
    "SELECT
        gc.request_id,
        gc.grade_id,
        gc.faculty_id,
        gc.reason,
        u.full_name AS student,
        s.subject_code,
        gp.period_name,
        g.percentage,
        r.full_name AS requester
     FROM grade_corrections gc
     JOIN grades g ON gc.grade_id = g.grade_id
     JOIN enrollments e ON g.enrollment_id = e.enrollment_id
     JOIN users u ON e.student_id = u.user_id
     JOIN subjects s ON e.subject_id = s.subject_id
     JOIN grading_periods gp ON g.period_id = gp.period_id
     JOIN users r ON gc.faculty_id = r.user_id
     WHERE gc.status = 'Pending'
     ORDER BY gc.request_id ASC"
);
$stmt->execute();
$res = $stmt->get_result();

logAction($conn, $_SESSION['user_id'], "Viewed pending correction requests");
?>

<style>
    .registrar-corrections { 
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
    .message.error {
        background: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
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

<div class="registrar-corrections">
    <div class="page-header">
        <h1>Pending Correction Requests</h1>
        <p>Review faculty requests to correct locked/approved grades.</p>
    </div>

    <?php if (!empty($action_msg)): ?>
        <div class="message <?= $msg_type !== 'success' ? $msg_type : '' ?>">
            <?= htmlspecialchars($action_msg, ENT_QUOTES) ?>
        </div>
    <?php endif; ?>

    <div class="content-section">
        <div class="content-title" aria-hidden="true"></div>
        
        <?php if ($res && $res->num_rows > 0): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Student</th>
                            <th>Subject</th>
                            <th>Period</th>
                            <th>Original %</th>
                            <th>Requested By</th>
                            <th>Reason</th>
                            <th>Decision Notes</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $res->fetch_assoc()): ?>
                        <tr>
                            <form method="post">
                                <?php echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">'; ?>
                                <input type="hidden" name="request_id" value="<?= htmlspecialchars($row['request_id'], ENT_QUOTES) ?>">
                                <input type="hidden" name="grade_id" value="<?= htmlspecialchars($row['grade_id'], ENT_QUOTES) ?>">
                                <input type="hidden" name="faculty_id" value="<?= htmlspecialchars($row['faculty_id'], ENT_QUOTES) ?>">

                                <td><?= htmlspecialchars($row['request_id'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['student'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['subject_code'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['period_name'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['percentage'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['requester'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($row['reason'], ENT_QUOTES) ?></td>
                                <td>
                                    <input type="text" name="decision_notes" placeholder="Notes (required)" required>
                                </td>
                                <td>
                                    <button type="submit" name="approve" class="btn-approve">Approve</button>
                                    <button type="submit" name="reject" class="btn-reject">Reject</button>
                                </td>
                            </form>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-message">
                <p>No pending correction requests at this time.</p>
            </div>
        <?php endif; ?>
    </div>
</div>