<?php
ob_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/audit.php';
require_once __DIR__ . '/../includes/csrf.php';

if (!headers_sent()) {
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}

requireRole([2]); // Registrar only

// Filters
$subject_id = intval($_GET['subject_id'] ?? 0);
$period_id = intval($_GET['period_id'] ?? 0);
$term = trim($_GET['term'] ?? '');

// Build base query: approved enrollments + approved grades
$base = "
SELECT u.user_id AS student_id, u.full_name, s.subject_code, gp.period_name, g.percentage, g.numeric_grade
FROM grades g
JOIN enrollments e ON g.enrollment_id = e.enrollment_id
JOIN users u ON e.student_id = u.user_id
JOIN subjects s ON e.subject_id = s.subject_id
JOIN grading_periods gp ON g.period_id = gp.period_id
WHERE g.status = 'Approved'
";

$params = [];
$types = '';
if ($subject_id > 0) { $base .= " AND s.subject_id = ?"; $types .= 'i'; $params[] = $subject_id; }
if ($period_id > 0) { $base .= " AND gp.period_id = ?"; $types .= 'i'; $params[] = $period_id; }
if ($term !== '') { $base .= " AND e.term = ?"; $types .= 's'; $params[] = $term; }

$base .= " ORDER BY s.subject_code, u.full_name, gp.period_id";

$stmt = $conn->prepare($base);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$res = $stmt->get_result();

logAction($conn, $_SESSION['user_id'], "Viewed Master Enrollment & Grade List");

?>

<style>
    .registrar-master-list { 
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
    .filter-form { margin-bottom: 1.5rem; }
    .filter-form input { padding: 6px; border: 1px solid #ddd; border-radius: 4px; margin: 0 0.5rem 0.5rem 0; }
    .filter-form button { padding: 6px 12px; background: #3B82F6; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .filter-form button:hover { background: #1E40AF; }
    .filter-form a { display: inline-block; padding: 6px 12px; background: #10b981; color: white; text-decoration: none; border-radius: 4px; margin-left: 0.5rem; }
    .filter-form a:hover { background: #059669; }
    .table-wrap { overflow-x: auto; }
    table { border-collapse: collapse; width: 100%; background: #fff; border: 1px solid #e1e4e8; border-radius: 4px; overflow: hidden; }
    th, td { border: 1px solid #e1e4e8; padding: 12px; text-align: left; vertical-align: middle; }
    th { background: #f6f8fa; font-weight: 600; color: #0f246c; }
    tr:nth-child(even) { background: #f9f9f9; }
    tr:hover { background: #f0f7ff; }
</style>

<div class="registrar-master-list">
    <div class="page-header">
        <h1>Master Enrollment & Grade List</h1>
        <p>Official records of all approved grades.</p>
    </div>

    <div class="content-section">
        <div class="content-title" aria-hidden="true"></div>
        
        <form method="get" class="filter-form">
            <label>Subject ID: <input name="subject_id" type="number" value="<?= htmlspecialchars($subject_id) ?>"></label>
            <label>Period ID: <input name="period_id" type="number" value="<?= htmlspecialchars($period_id) ?>"></label>
            <label>Term: <input name="term" value="<?= htmlspecialchars($term) ?>"></label>
            <button type="submit">Filter</button>
            <a href="../export_pdf.php?type=registrar_master">Export PDF</a>
        </form>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Subject</th>
                        <th>Period</th>
                        <th>Percentage</th>
                        <th>Numeric Grade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($r = $res->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['full_name'], ENT_QUOTES) ?> (<?= htmlspecialchars($r['student_id'], ENT_QUOTES) ?>)</td>
                        <td><?= htmlspecialchars($r['subject_code'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($r['period_name'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($r['percentage'], ENT_QUOTES) ?></td>
                        <td><?= htmlspecialchars($r['numeric_grade'], ENT_QUOTES) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
