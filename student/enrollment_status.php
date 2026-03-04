<?php
/* 
 * STUDENT ENROLLMENT STATUS PAGE
 * This file is included by index.php (router)
 * Do not include HTML head/body tags - only content
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/audit.php';

// Student access control
requireRole([3]);

$student_id = $_SESSION["user_id"];

// Fetch student's enrollment requests
$myrq = $conn->prepare(
    "SELECT er.request_id, s.subject_code, s.subject_name, er.request_date, er.status
     FROM enrollment_requests er
     JOIN subjects s ON er.subject_id = s.subject_id
     WHERE er.student_id = ?
     ORDER BY er.request_date DESC"
);
$myrq->bind_param('i', $student_id);
$myrq->execute();
$myrq_res = $myrq->get_result();

logAction($conn, $student_id, "Viewed enrollment status");
?>

<style>
/* Shared student page styles (same as enrollment_request) */
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
.table-wrap { overflow-x: auto; margin-bottom: 1rem; }
table { border-collapse: collapse; width: 100%; background: #fff; border: 1px solid #e1e4e8; border-radius: 4px; overflow: hidden; }
th, td { border: 1px solid #e1e4e8; padding: 12px; text-align: center; vertical-align: middle; }
th { background: #f6f8fa; font-weight: 600; color: #0f246c; }
tr:nth-child(even) { background: #f9f9f9; }
tr:hover { background: #f0f7ff; }
.status-badge { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 0.875rem; font-weight: 500; }
.status-pending { background: #fff3cd; color: #856404; }
.status-approved { background: #d4edda; color: #155724; }
.status-rejected { background: #f8d7da; color: #721c24; }
.empty-message { text-align: center; padding: 2rem; color: #64748b; }
</style>

<div class="student-page">
    <div class="page-header">
        <h1>Enrollment Status</h1>
        <p>View the status of your submitted enrollment requests.</p>
    </div>
    <div class="content-section">
        <div class="content-title" aria-hidden="true"></div>

        <?php if ($myrq_res && $myrq_res->num_rows > 0): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject Name</th>
                            <th>Request Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($r = $myrq_res->fetch_assoc()): 
                            $status_class = 'status-pending';
                            if ($r['status'] === 'Approved') $status_class = 'status-approved';
                            elseif ($r['status'] === 'Rejected') $status_class = 'status-rejected';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($r['subject_code'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($r['subject_name'], ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($r['request_date'], ENT_QUOTES) ?></td>
                            <td>
                                <span class="status-badge <?= $status_class ?>">
                                    <?= htmlspecialchars($r['status'], ENT_QUOTES) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-message">
                <p style="color:#64748B;">You have not submitted any enrollment requests yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
