<?php
/* 
 * STUDENT ENROLLMENT REQUEST PAGE
 * This file is included by index.php (router)
 * Do not include HTML head/body tags - only content
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/audit.php';
require_once __DIR__ . '/../includes/csrf.php';

// Student access control
requireRole([3]);

$student_id = $_SESSION["user_id"];

// Handle enrollment request POST
$request_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'request_enrollment') {
    if (empty($_POST['csrf_token'])) { 
        $request_msg = 'Missing CSRF token';
    } else {
        csrf_validate_or_die($_POST['csrf_token']);

        // Ensure role is student (defense-in-depth)
        requireRole([3]);

        $subject_id = intval($_POST['subject_id'] ?? 0);
        if ($subject_id <= 0) {
            $request_msg = 'Invalid subject selected.';
        } else {
            // Atomic check + insert to prevent duplicates
            $conn->begin_transaction();
            try {
                // Check existing official enrollment
                $chkEn = $conn->prepare("SELECT enrollment_id FROM enrollments WHERE student_id = ? AND subject_id = ? LIMIT 1");
                $chkEn->bind_param('ii', $student_id, $subject_id);
                $chkEn->execute();
                $rEn = $chkEn->get_result();
                if ($rEn && $rEn->num_rows > 0) {
                    $conn->rollback();
                    $request_msg = 'You are already enrolled in this subject.';
                    logAction($conn, $student_id, "Attempted duplicate enrollment request for subject $subject_id");
                } else {
                    // Check existing pending request (row lock)
                    $chkReq = $conn->prepare("SELECT request_id FROM enrollment_requests WHERE student_id = ? AND subject_id = ? AND status = 'Pending' LIMIT 1 FOR UPDATE");
                    $chkReq->bind_param('ii', $student_id, $subject_id);
                    $chkReq->execute();
                    $rReq = $chkReq->get_result();
                    if ($rReq && $rReq->num_rows > 0) {
                        $conn->rollback();
                        $request_msg = 'You already have a pending request for this subject.';
                        logAction($conn, $student_id, "Duplicate enrollment request blocked for subject $subject_id");
                    } else {
                        $ins = $conn->prepare("INSERT INTO enrollment_requests (student_id, subject_id, status, request_date) VALUES (?, ?, 'Pending', NOW())");
                        $ins->bind_param('ii', $student_id, $subject_id);
                        $ins->execute();
                        $conn->commit();
                        $request_msg = 'Enrollment request submitted successfully.';
                        logAction($conn, $student_id, "Submitted enrollment request for subject $subject_id");
                    }
                }
            } catch (Exception $e) {
                $conn->rollback();
                $request_msg = 'Server error while submitting request.';
            }
        }
    }
}

// Fetch available subjects (not enrolled, no pending request)
$avail_q = "
SELECT s.subject_id, s.subject_code, s.subject_name, u.full_name AS faculty_name
FROM subjects s
LEFT JOIN enrollments e ON s.subject_id = e.subject_id AND e.student_id = ?
LEFT JOIN enrollment_requests er ON s.subject_id = er.subject_id AND er.student_id = ? AND er.status = 'Pending'
LEFT JOIN users u ON s.faculty_id = u.user_id
WHERE e.enrollment_id IS NULL AND er.request_id IS NULL
ORDER BY s.subject_code
";
$avail = $conn->prepare($avail_q);
$avail->bind_param('ii', $student_id, $student_id);
$avail->execute();
$avail_res = $avail->get_result();
?>

<style>
/* Shared student page styles */
.student-page { padding: 2rem; }
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
.message-box { padding: 1rem; margin: 1rem 0; border-radius: 4px; font-size: 0.9rem; }
.message-box.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.message-box.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.table-wrap { overflow-x: auto; margin-bottom: 1rem; }
table { border-collapse: collapse; width: 100%; background: #fff; border: 1px solid #e1e4e8; border-radius: 4px; overflow: hidden; }
th, td { border: 1px solid #e1e4e8; padding: 12px; text-align: center; vertical-align: middle; }
th { background: #f6f8fa; font-weight: 600; color: #0f246c; }
tr:nth-child(even) { background: #f9f9f9; }
tr:hover { background: #f0f7ff; }
.btn-request { padding: 6px 14px; background: #3B82F6; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: 500; font-size: 0.875rem; }
.btn-request:hover { background: #1E40AF; }
form { margin: 0; }
</style>

<div class="student-page">
    <div class="page-header">
        <h1>Request Subject Enrollment</h1>
        <p>Select a subject below to submit an enrollment request.</p>
    </div>

    <div class="content-section">
        <div class="content-title" aria-hidden="true"></div>

        <?php if (!empty($request_msg)): ?>
            <?php $is_error = (strpos($request_msg, 'Invalid') !== false || strpos($request_msg, 'already') !== false || strpos($request_msg, 'Server') !== false); ?>
            <div class="message-box <?= $is_error ? 'error' : 'success' ?>">
                <?= htmlspecialchars($request_msg, ENT_QUOTES) ?>
            </div>
        <?php endif; ?>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Assigned Faculty</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($avail_res && $avail_res->num_rows > 0): ?>
                        <?php while ($s = $avail_res->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($s['subject_code'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($s['subject_name'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($s['faculty_name'] ?? '-', ENT_QUOTES) ?></td>
                            <td>
                                <form method="post">
                                    <?php echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">'; ?>
                                    <input type="hidden" name="action" value="request_enrollment">
                                    <input type="hidden" name="subject_id" value="<?= htmlspecialchars($s['subject_id'], ENT_QUOTES) ?>">
                                    <button type="submit" class="btn-request">Request</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b;">No available subjects to enroll in.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
