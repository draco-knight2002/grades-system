<?php
/* 
 * STUDENT VIEW GRADES PAGE
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

// Fetch enrollments with latest approved grade and subject teacher
$grades_q = "
SELECT 
    s.subject_code,
    s.subject_name,
    u.full_name AS faculty_name,
    g.numeric_grade,
    g.remarks,
    g.status
FROM enrollments e
JOIN subjects s ON e.subject_id = s.subject_id
LEFT JOIN users u ON s.faculty_id = u.user_id
LEFT JOIN (
    SELECT g1.* FROM grades g1
    JOIN (
        SELECT enrollment_id, MAX(grade_id) AS grade_id
        FROM grades
        WHERE status = 'Approved'
        GROUP BY enrollment_id
    ) gm ON g1.enrollment_id = gm.enrollment_id AND g1.grade_id = gm.grade_id
) g ON e.enrollment_id = g.enrollment_id
WHERE e.student_id = ?
ORDER BY s.subject_code
";
$grades_stmt = $conn->prepare($grades_q);
$grades_stmt->bind_param("i", $student_id);
$grades_stmt->execute();
$grades_result = $grades_stmt->get_result();

logAction($conn, $student_id, "Viewed student grades");

$enrollments = [];
if ($grades_result) {
    while ($row = $grades_result->fetch_assoc()) {
        $enrollments[] = $row;
    }
}
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
.table-wrap { overflow-x: auto; margin-bottom: 1rem; }
table { border-collapse: collapse; width: 100%; background: #fff; border: 1px solid #e1e4e8; border-radius: 4px; overflow: hidden; }
th, td { border: 1px solid #e1e4e8; padding: 12px; text-align: center; vertical-align: middle; }
th { background: #f6f8fa; font-weight: 600; color: #0f246c; }
tr:nth-child(even) { background: #f9f9f9; }
tr:hover { background: #f0f7ff; }
.status-approved { display: inline-block; padding: 4px 8px; border-radius: 4px; background: #d4edda; color: #155724; font-size: 0.875rem; font-weight: 500; }
.empty-message { text-align: center; padding: 2rem; color: #64748b; }
</style>

<div class="student-page">
    <div class="page-header">
        <h1>My Grades</h1>
        <p>Overview of your grades by subject.</p>
    </div>

    <div class="content-section">
        <div class="content-title" aria-hidden="true"></div>

        <?php if (count($enrollments) > 0): ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Subject Code</th>
                            <th>Subject</th>
                            <th>Subject Teacher</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrollments as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['subject_code'] ?? '', ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($row['subject_name'] ?? '', ENT_QUOTES) ?></td>
                            <td><?= htmlspecialchars($row['faculty_name'] ?? '', ENT_QUOTES) ?></td>
                            <td>
                                <?php if (isset($row['status']) && $row['status'] === 'Approved' && $row['numeric_grade'] !== null): ?>
                                    <?= number_format((float)$row['numeric_grade'], 2) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                if (isset($row['status']) && $row['status'] === 'Approved' && $row['numeric_grade'] !== null) {
                                    $isFailed = false;
                                    if (!empty($row['remarks']) && stripos($row['remarks'], 'Failed') !== false) {
                                        $isFailed = true;
                                    }
                                    if (floatval($row['numeric_grade']) == 5.00) {
                                        $isFailed = true;
                                    }
                                    echo $isFailed ? 'Failed' : 'Passed';
                                }
                                ?>
                            </td>
                            <td>
                                <?php if (isset($row['status']) && $row['status'] === 'Approved'): ?>
                                    <span class="status-approved">Approved</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-message">
                <p>You are not enrolled in any subjects yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
