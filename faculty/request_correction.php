<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/audit.php';
require_once __DIR__ . '/../includes/csrf.php';

// cache-control headers with check
if (!headers_sent()) {
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: 0");
}


requireRole([1]); // Faculty

// Only accept POST - prevent direct URL access
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // GET: Show locked/approved grades that can be corrected
    $faculty_id = $_SESSION['user_id'];
    
    // Fetch all locked/approved grades for this faculty's subjects
    $grades_query = "
        SELECT 
            g.grade_id,
            u.full_name AS student_name,
            s.subject_code,
            s.subject_name,
            p.period_name,
            g.percentage,
            g.numeric_grade,
            g.status,
            CASE WHEN gc.request_id IS NOT NULL THEN 1 ELSE 0 END AS has_pending_request
        FROM grades g
        JOIN enrollments e ON g.enrollment_id = e.enrollment_id
        JOIN users u ON e.student_id = u.user_id
        JOIN subjects s ON e.subject_id = s.subject_id
        JOIN grading_periods p ON g.period_id = p.period_id
        LEFT JOIN grade_corrections gc ON g.grade_id = gc.grade_id AND gc.status IN ('Pending', 'Approved')
        WHERE s.faculty_id = ? AND g.is_locked = 1
        ORDER BY s.subject_code, u.full_name, p.period_name
    ";
    
    $stmt = $conn->prepare($grades_query);
    $stmt->bind_param("i", $faculty_id);
    $stmt->execute();
    $grades_result = $stmt->get_result();
    
    $locked_grades = [];
    if ($grades_result) {
        while ($row = $grades_result->fetch_assoc()) {
            $locked_grades[] = $row;
        }
    }
    
    ?>
    <style>
        .faculty-dashboard { padding: 2rem; }
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
        textarea { padding: 6px; border: 1px solid #ddd; border-radius: 4px; font-size: 0.9rem; width: 100%; min-height: 60px; }
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
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        form { margin: 0; display: inline; }
        .empty-message { text-align: center; padding: 2rem; color: #64748b; }
    </style>
    
    <div class="faculty-dashboard">
        <div class="page-header">
            <h1>Request Grade Correction</h1>
            <p>Submit requests to correct locked/approved grades.</p>
        </div>
        
        <div class="content-section">
            <div class="content-title" aria-hidden="true"></div>
            
            <?php if (count($locked_grades) > 0): ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Period</th>
                                <th>Grade</th>
                                <th>Status</th>
                                <th>Reason for Correction</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($locked_grades as $grade): ?>
                            <tr>
                                <td><?= htmlspecialchars($grade['student_name'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($grade['subject_code'] . ' - ' . $grade['subject_name'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($grade['period_name'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($grade['numeric_grade'], ENT_QUOTES) ?></td>
                                <td><?= htmlspecialchars($grade['status'], ENT_QUOTES) ?></td>
                                <td>
                                    <form method="post">
                                        <?php echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">'; ?>
                                        <input type="hidden" name="grade_id" value="<?= htmlspecialchars($grade['grade_id'], ENT_QUOTES) ?>">
                                        <textarea name="reason" placeholder="Explain why this grade needs correction..." required></textarea>
                                </td>
                                <td>
                                        <button type="submit" <?php echo $grade['has_pending_request'] ? 'disabled title="Request already pending"' : ''; ?>>
                                            <?php echo $grade['has_pending_request'] ? 'Pending' : 'Request'; ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-message">
                    <p>No locked/approved grades available for correction.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php
    exit;
}

if (empty($_POST['csrf_token'])) { http_response_code(400); die('Missing CSRF token'); }
csrf_validate_or_die($_POST['csrf_token']);

$grade_id = intval($_POST["grade_id"] ?? 0);
$reason = trim($_POST["reason"] ?? '');

// Validate that the grade belongs to this faculty and is locked (only locked/approved grades may be requested)
// Verify ownership and locked state
$check = $conn->prepare(
    "SELECT g.grade_id
     FROM grades g
     JOIN enrollments e ON g.enrollment_id = e.enrollment_id
     JOIN subjects s ON e.subject_id = s.subject_id
     WHERE g.grade_id = ? AND s.faculty_id = ? AND g.is_locked = 1"
);
$check->bind_param("ii", $grade_id, $_SESSION["user_id"]);
$check->execute();
$res = $check->get_result();
if ($res->num_rows === 0) {
    // Invalid request or not allowed
    http_response_code(403);
    logAction($conn, $_SESSION["user_id"], "Unauthorized correction request attempt for grade ID $grade_id");
    header("Location: ?page=dashboard&msg=not_allowed");
    exit;
}

// Atomic duplicate check + insert
$conn->begin_transaction();
try {
    // Check for existing active request for this grade
    $dup = $conn->prepare(
        "SELECT request_id FROM grade_corrections WHERE grade_id = ? AND status IN ('Pending','Approved') LIMIT 1 FOR UPDATE"
    );
    $dup->bind_param("i", $grade_id);
    $dup->execute();
    $dupRes = $dup->get_result();
    if ($dupRes && $dupRes->num_rows > 0) {
        // Duplicate exists: rollback and stop
        $conn->rollback();
        http_response_code(409);
        logAction($conn, $_SESSION["user_id"], "Duplicate correction request blocked for grade ID $grade_id");
        echo "A correction request already exists for this grade.";
        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO grade_corrections (grade_id, faculty_id, reason, status)
         VALUES (?, ?, ?, ?)"
    );
    $status = 'Pending';
    $stmt->bind_param("iiss", $grade_id, $_SESSION["user_id"], $reason, $status);
    $stmt->execute();

    logAction($conn, $_SESSION["user_id"], "Requested correction for grade ID $grade_id");

    $conn->commit();

    header("Location: ?page=dashboard&msg=correction_requested");
    exit;

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo "Server error";
    exit;
}

?>