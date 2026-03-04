<?php
ob_start();
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/audit.php';

if (!headers_sent()) {
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}

requireRole([1]); // Faculty only
$faculty_id = $_SESSION['user_id'];

// Log view
logAction($conn, $faculty_id, "Viewed Faculty Grade Sheets");

// Fetch subjects assigned to this faculty
$subq = $conn->prepare("SELECT subject_id, subject_code, subject_name FROM subjects WHERE faculty_id = ?");
$subq->bind_param("i", $faculty_id);
$subq->execute();
$sub_res = $subq->get_result();

// Fetch grading periods (prepared statement)
$pr = $conn->prepare("SELECT period_id, period_name FROM grading_periods ORDER BY period_id");
$pr->execute();
$periods = $pr->get_result();
$period_list = [];
while ($p = $periods->fetch_assoc()) $period_list[] = $p;
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

    /* reuse student table styles */
    .table-wrap { overflow-x: auto; margin-bottom: 1rem; }
    table { border-collapse: collapse; width: 100%; background: #fff; border: 1px solid #e1e4e8; border-radius: 4px; overflow: hidden; }
    th, td { border: 1px solid #e1e4e8; padding: 12px; text-align: center; vertical-align: middle; }
    th { background: #f6f8fa; font-weight: 600; color: #0f246c; }
    tr:nth-child(even) { background: #f9f9f9; }
    tr:hover { background: #f0f7ff; }

    /* headings adopt template/global styles */
    .subsection-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 1.5rem 0 1rem 0;
        color: #1E40AF;
    }

    .small { font-size: 0.9em; color: #555; }
</style>

<div class="faculty-dashboard student-page">
    <!-- Page header similar to student pages -->
    <div class="page-header">
        <h1>View Submitted Grades</h1>
        <p>Review class grade sheets for your enrolled students.</p>
    </div>

    <div class="content-section">
        <div class="content-title" aria-hidden="true"></div>

        <?php while ($sub = $sub_res->fetch_assoc()): ?>
        <h3 class="subsection-title"><?= htmlspecialchars($sub['subject_code'] . ' - ' . $sub['subject_name'], ENT_QUOTES) ?></h3>
        
        <div class="table-wrap">
        <table>
    <tr>
        <th>Student</th>
        <?php foreach ($period_list as $p): ?><th><?= htmlspecialchars($p['period_name'], ENT_QUOTES) ?></th><?php endforeach; ?>
        <th>Final Grade</th>
        <th>Status</th>
    </tr>

    <?php
    $enq = $conn->prepare("SELECT e.enrollment_id, u.user_id, u.full_name FROM enrollments e JOIN users u ON e.student_id = u.user_id WHERE e.subject_id = ?");
    $enq->bind_param("i", $sub['subject_id']);
    $enq->execute();
    $enr = $enq->get_result();

    while ($student = $enr->fetch_assoc()):
        echo '<tr>';
        echo '<td>' . htmlspecialchars($student['full_name'], ENT_QUOTES) . '</td>';

        // Per-period grades
        $final_numeric = null;
        $status_label = 'Draft';
        foreach ($period_list as $p) {
            $gq = $conn->prepare("SELECT percentage, numeric_grade, status, is_locked FROM grades WHERE enrollment_id = ? AND period_id = ? ORDER BY grade_id DESC LIMIT 1");
            $gq->bind_param("ii", $student['enrollment_id'], $p['period_id']);
            $gq->execute();
            $gres = $gq->get_result();
            if ($gres && $gro = $gres->fetch_assoc()) {
                $cell_status = 'Draft';
                if ($gro['status'] === 'Approved' && intval($gro['is_locked']) === 1) $cell_status = 'Approved (Locked)';
                elseif ($gro['status'] === 'Pending') $cell_status = 'Submitted';
                elseif ($gro['status'] === 'Returned') $cell_status = 'Returned';

                echo '<td>' . htmlspecialchars($gro['numeric_grade'], ENT_QUOTES) . '</td>';

                // Elevate overall status if any approved locked exists
                if ($cell_status === 'Approved (Locked)') {
                    $status_label = 'Approved (Locked)';
                } elseif ($cell_status === 'Submitted' && $status_label !== 'Approved (Locked)') {
                    $status_label = 'Submitted';
                } elseif ($cell_status === 'Returned' && !in_array($status_label, ['Approved (Locked)','Submitted'])) {
                    $status_label = 'Returned';
                }
                $final_numeric = $gro['numeric_grade'];
            } else {
                echo '<td>-</td>';
            }
        }

        echo '<td>' . ($final_numeric !== null ? htmlspecialchars($final_numeric, ENT_QUOTES) : '-') . '</td>';
        echo '<td>' . $status_label . '</td>';
        echo '</tr>';
    endwhile;
        ?>
        </table>
        </div>
        <?php endwhile; ?>
    </div>
</div>
