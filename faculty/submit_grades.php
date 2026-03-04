<?php
/*
 * FACULTY SUBMIT GRADES PAGE
 * This file is included by index.php (router)
 * Do not include HTML head/body tags - only content
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/grading_logic.php';
require_once __DIR__ . '/../includes/audit.php';
require_once __DIR__ . '/../includes/csrf.php';

// Faculty access control
requireRole([1]);

$faculty_id = $_SESSION["user_id"];

// Handle grade submission
$submit_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["encode"])) {
    if (empty($_POST['csrf_token'])) { http_response_code(400); die('Missing CSRF token'); }
    csrf_validate_or_die($_POST['csrf_token']);

    $enrollment_id = intval($_POST["enrollment_id"]);
    $period_id = intval($_POST["period_id"]);
    $percentage = floatval($_POST["percentage"]);

    // Check existing most recent grade for this enrollment+period
    $chk = $conn->prepare(
        "SELECT is_locked FROM grades WHERE enrollment_id = ? AND period_id = ? ORDER BY grade_id DESC LIMIT 1"
    );
    $chk->bind_param("ii", $enrollment_id, $period_id);
    $chk->execute();
    $chkres = $chk->get_result();
    if ($chkres && $row = $chkres->fetch_assoc()) {
        if (intval($row['is_locked']) === 1) {
            logAction($conn, $_SESSION["user_id"], "Attempted encode while grade locked for enrollment $enrollment_id period $period_id");
            $submit_msg = "Cannot submit: grade is locked pending correction/approval.";
        } else {
            [$numeric, $remarks] = convertGrade($percentage);
            $stmt = $conn->prepare(
                "INSERT INTO grades 
                (enrollment_id, period_id, percentage, numeric_grade, remarks, status, is_locked)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $status = 'Pending';
            $is_locked = 0;
            $stmt->bind_param("iiddssi", $enrollment_id, $period_id, $percentage, $numeric, $remarks, $status, $is_locked);
            $stmt->execute();
            logAction($conn, $_SESSION["user_id"], "Encoded grade for enrollment $enrollment_id period $period_id");
            $submit_msg = "Grade submitted successfully.";
        }
    } else {
        // No previous grade — allow insert
        [$numeric, $remarks] = convertGrade($percentage);
        $stmt = $conn->prepare(
            "INSERT INTO grades 
            (enrollment_id, period_id, percentage, numeric_grade, remarks, status, is_locked)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $status = 'Pending';
        $is_locked = 0;
        $stmt->bind_param("iiddssi", $enrollment_id, $period_id, $percentage, $numeric, $remarks, $status, $is_locked);
        $stmt->execute();
        logAction($conn, $_SESSION["user_id"], "Encoded grade for enrollment $enrollment_id period $period_id");
        $submit_msg = "Grade submitted successfully.";
    }
}

// Fetch subjects handled by this faculty
$subjects = $conn->prepare(
    "SELECT subject_id, subject_code, subject_name 
     FROM subjects WHERE faculty_id = ?"
);
$subjects->bind_param("i", $faculty_id);
$subjects->execute();
$subject_result = $subjects->get_result();

// Fetch grading periods
$periods_query = $conn->query("SELECT period_id, period_name FROM grading_periods ORDER BY period_id");
$periods_array = [];
while ($p = $periods_query->fetch_assoc()) {
    $periods_array[] = $p;
}
?>

<div class="faculty-dashboard student-page">
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

        input[type="number"],
        input[type="text"] {
            padding: 6px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        input[type="number"] { width: 80px; }
        input[type="text"] { width: 150px; }
        button {
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            background: #3B82F6;
            color: white;
            font-weight: 500;
        }
        button:hover { background: #1E40AF; }
        form { margin: 0; display: inline; }
    </style>

    <!-- Page header similar to student pages -->
    <div class="page-header">
        <h1>Submit Grades</h1>
        <p>Encode grades for your enrolled students.</p>
    </div>

    <div class="content-section">
        <div class="content-title" aria-hidden="true"></div>
        
        <?php if ($subject_result->num_rows > 0): 
            $subject_result->data_seek(0);
            while ($subject = $subject_result->fetch_assoc()):
        ?>
            <h3 class="subsection-title"><?= htmlspecialchars($subject["subject_code"], ENT_QUOTES) ?> - <?= htmlspecialchars($subject["subject_name"], ENT_QUOTES) ?></h3>
            
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Period</th>
                            <th>Percentage</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $enrollments = $conn->prepare(
                            "SELECT e.enrollment_id, u.full_name 
                             FROM enrollments e
                             JOIN users u ON e.student_id = u.user_id
                             WHERE e.subject_id = ?
                             ORDER BY u.full_name"
                        );
                        $enrollments->bind_param("i", $subject["subject_id"]);
                        $enrollments->execute();
                        $enrollment_result = $enrollments->get_result();

                        while ($row = $enrollment_result->fetch_assoc()):
                            foreach ($periods_array as $period):
                        ?>
                        <tr>
                            <form method="post">
                                <?php echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">'; ?>
                                <td><?= htmlspecialchars($row["full_name"], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($period["period_name"], ENT_QUOTES) ?></td>
                                <td>
                                    <input type="number" name="percentage" min="0" max="100" step="0.01" placeholder="0-100" required>
                                </td>
                                <td>
                                    <input type="hidden" name="enrollment_id" value="<?= htmlspecialchars($row["enrollment_id"], ENT_QUOTES) ?>">
                                    <input type="hidden" name="period_id" value="<?= htmlspecialchars($period["period_id"], ENT_QUOTES) ?>">
                                    <button type="submit" name="encode">Submit</button>
                                </td>
                            </form>
                        </tr>
                        <?php endforeach; endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endwhile; 
        else: ?>
            <p style="color: #64748B;">No subjects assigned.</p>
        <?php endif; ?>
    </div>

</div>
