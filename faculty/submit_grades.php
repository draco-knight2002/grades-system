<?php
/* 
 * FACULTY SUBMIT GRADES
 * This file is included by index.php (router)
 * Do not include HTML head/body tags - only content
 */

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/rbac.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/audit.php';
require_once __DIR__ . '/../includes/csrf.php';
require_once __DIR__ . '/../includes/flash.php';

// Faculty access control
requireRole([1]);

$faculty_id = $_SESSION['user_id'];
$message = '';
$msg_type = 'success';

// retrieve flash (if any)
if ($flash = getFlash()) {
    $message = $flash['msg'];
    $msg_type = $flash['type'];
}


// Grade scale mapping
function calculateGrade($percentage) {
    if ($percentage >= 98) return ['numeric' => 1.00, 'remarks' => 'Passed'];
    if ($percentage >= 95) return ['numeric' => 1.25, 'remarks' => 'Passed'];
    if ($percentage >= 92) return ['numeric' => 1.50, 'remarks' => 'Passed'];
    if ($percentage >= 89) return ['numeric' => 1.75, 'remarks' => 'Passed'];
    if ($percentage >= 86) return ['numeric' => 2.00, 'remarks' => 'Passed'];
    if ($percentage >= 83) return ['numeric' => 2.25, 'remarks' => 'Passed'];
    if ($percentage >= 80) return ['numeric' => 2.50, 'remarks' => 'Passed'];
    if ($percentage >= 77) return ['numeric' => 2.75, 'remarks' => 'Passed'];
    if ($percentage >= 75) return ['numeric' => 3.00, 'remarks' => 'Passed'];
    return ['numeric' => 5.00, 'remarks' => 'Failed'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token validation
    if (empty($_POST['csrf_token']) || !csrf_check($_POST['csrf_token'])) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
        $subject_id = isset($_POST['subject_id']) ? (int)$_POST['subject_id'] : 0;
        $academic_period = isset($_POST['academic_period']) ? $_POST['academic_period'] : '';
        $percentage = isset($_POST['percentage']) ? (float)$_POST['percentage'] : 0;

        // Validate inputs
        if ($student_id <= 0 || $subject_id <= 0 || empty($academic_period) || $percentage < 0 || $percentage > 100) {
            $error = 'Please provide valid inputs.';
        } else {
            // Calculate grade
            $grade_info = calculateGrade($percentage);
            $numeric_grade = $grade_info['numeric'];
            $remarks = $grade_info['remarks'];

            // Insert or update grade
            $stmt = $conn->prepare("
                INSERT INTO grades (student_id, subject_id, academic_period, percentage, numeric_grade, remarks, status, is_locked)
                VALUES (?, ?, ?, ?, ?, ?, 'Pending', 1)
                ON DUPLICATE KEY UPDATE
                    percentage = VALUES(percentage),
                    numeric_grade = VALUES(numeric_grade),
                    remarks = VALUES(remarks),
                    status = 'Pending',
                    is_locked = 1
            ");
            $stmt->bind_param("iissds", $student_id, $subject_id, $academic_period, $percentage, $numeric_grade, $remarks);

            if ($stmt->execute()) {
                setFlash('Grade submitted successfully.','success');
                logAction($conn, $faculty_id, "Grade submitted for student $student_id in subject $subject_id");
                $stmt->close();
                if (!headers_sent()) {
                    header('Location: ?page=submit_grades');
                    exit;
                } else {
                    echo "<script>window.location.href='?page=submit_grades';</script>";
                    exit;
                }
            } else {
                setFlash('Failed to submit grade: ' . $stmt->error,'error');
                $stmt->close();
                if (!headers_sent()) {
                    header('Location: ?page=submit_grades');
                    exit;
                } else {
                    echo "<script>window.location.href='?page=submit_grades';</script>";
                    exit;
                }
            }
        }
    }
}

// Fetch faculty's subjects
$subjects = [];
$stmt = $conn->prepare("SELECT subject_id, subject_code, subject_name FROM subjects WHERE faculty_id = ? ORDER BY subject_name");
$stmt->bind_param("i", $faculty_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $subjects[] = $row;
}
$stmt->close();

// Fetch all enrolled students for faculty's subjects (for filter)
$students = [];
$placeholders = implode(',', array_fill(0, count($subjects), '?'));
if (!empty($subjects)) {
    $subject_ids = array_column($subjects, 'subject_id');
    $stmt = $conn->prepare("
        SELECT DISTINCT u.user_id, u.full_name
        FROM enrollments e
        JOIN users u ON e.student_id = u.user_id
        WHERE e.subject_id IN ($placeholders) AND e.status = 'Active'
        ORDER BY u.full_name
    ");
    $stmt->bind_param(str_repeat('i', count($subject_ids)), ...$subject_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
    $stmt->close();
}

// Fetch existing locked grades for this faculty's subjects
$existing_grades = [];
$placeholders = implode(',', array_fill(0, count($subjects), '?'));
if (!empty($subjects)) {
    $subject_ids = array_column($subjects, 'subject_id');
    $stmt = $conn->prepare("
        SELECT g.student_id, g.subject_id, g.academic_period, g.percentage, g.numeric_grade, g.is_locked, g.status
        FROM grades g
        WHERE g.subject_id IN ($placeholders) AND g.is_locked = 1
    ");
    $stmt->bind_param(str_repeat('i', count($subject_ids)), ...$subject_ids);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $key = $row['student_id'] . '_' . $row['subject_id'] . '_' . $row['academic_period'];
        $existing_grades[$key] = $row;
    }
    $stmt->close();
}

// Period options
$periods = [
    '1st Year - 1st Semester',
    '1st Year - 2nd Semester',
    '2nd Year - 1st Semester',
    '2nd Year - 2nd Semester',
    '3rd Year - 1st Semester',
    '3rd Year - 2nd Semester'
];

$csrf_token = csrf_token();
?>

<style>
    :root {
        --primary: #3B82F6;
        --primary-dark: #1E40AF;
        --secondary: #10B981;
        --accent: #F59E0B;
        --danger: #EF4444;
        --success: #22C55E;
        --surface: #FFFFFF;
        --background: #F8FAFC;
        --text-primary: #1E293B;
        --text-secondary: #64748B;
        --border: #E2E8F0;
        --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --radius: 12px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .main-content {
        padding: 0.7rem 2rem 2rem 2rem;
        flex: 1;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-header h2 {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text-900);
        margin-bottom: 0.5rem;
    }

    .page-header p {
        color: var(--text-600);
        font-size: 0.9rem;
    }

    .header-card {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 2.5rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        position: relative;
        overflow: hidden;
    }

    .header-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
        50% { transform: translate(-50%, -50%) rotate(180deg); }
    }

    .header-card h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }

    .header-card p {
        font-size: 1.1rem;
        opacity: 0.9;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .alert-card {
        padding: 1rem 1.5rem;
        border-radius: var(--radius);
        margin-bottom: 2rem;
        border: 1px solid;
        position: relative;
        animation: slideIn 0.3s ease-out;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    @keyframes slideIn {
        from { transform: translateY(-10px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    .alert-success {
        background: rgba(34, 197, 94, 0.1);
        border-color: var(--success);
        color: #166534;
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border-color: var(--danger);
        color: #991b1b;
    }

    .filters-section {
        background: var(--surface);
        padding: 2rem;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        margin-bottom: 2rem;
    }

    .filters-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .filters-header h2 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }

    .filter-group {
        display: flex;
        flex-direction: column;
    }

    .filter-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-select {
        padding: 0.75rem 1rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 1rem;
        background: var(--surface);
        color: var(--text-primary);
        transition: var(--transition);
        cursor: pointer;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    /* table card styling like student pages */
    .table-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--r-md);
        box-shadow: var(--shadow-sm);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: var(--transition);
    }

    .table-card:hover {
        box-shadow: var(--shadow-md);
        border-color: var(--primary);
    }

    .table-wrap {
        overflow-x: auto;
    }

    .subject-title {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        flex-direction: column;
    }

    .title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .subtitle {
        font-size: 0.9rem;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-approved {
        background: #f0fdf4;
        color: #166534;
        border: 1px solid #22c55e;
    }

    .badge-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .subject-info p {
        font-size: 0.9rem;
        opacity: 0.9;
        margin: 0;
    }

    .grades-table-container {
        padding: 0;
    }

    .grades-table {
        width: 100%;
        border-collapse: collapse;
    }

    .grades-table thead {
        background: var(--bg);
        border-bottom: 1px solid var(--border);
    }

    .grades-table th {
        padding: 1rem 1.25rem;
        text-align: center;
        font-weight: 700;
        color: var(--text-400);
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .grades-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: var(--transition);
    }

    .grades-table tbody tr:hover {
        background: #f8f9ff;
    }

    .grades-table td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
        color: black;
        text-align: center;
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .student-details h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin: 0 0 0.25rem 0;
    }

    .student-details span {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .period-select {
        padding: 0.75rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 0.9rem;
        background: var(--surface);
        color: var(--text-primary);
        min-width: 180px;
        transition: var(--transition);
    }

    .period-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .percentage-input {
        padding: 0.75rem;
        border: 2px solid var(--border);
        border-radius: 8px;
        font-size: 0.9rem;
        background: var(--surface);
        color: var(--text-primary);
        width: 120px;
        transition: var(--transition);
    }

    .percentage-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .grade-display {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: rgba(16, 185, 129, 0.1);
        color: var(--secondary);
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .submit-btn {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        min-width: 100px;
        justify-content: center;
    }

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
    }

    .submit-btn:active {
        transform: translateY(0);
    }

    .no-data {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--text-secondary);
    }

    .no-data-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .no-data h3 {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .no-data p {
        font-size: 1rem;
        margin: 0;
    }

    @media (max-width: 768px) {
        .submit-grades-page {
            padding: 1rem;
        }

        .header-card {
            padding: 2rem;
        }

        .header-card h1 {
            font-size: 2rem;
        }

        .filters-grid {
            grid-template-columns: 1fr;
        }

        .grades-table {
            font-size: 0.85rem;
        }

        .grades-table th,
        .grades-table td {
            padding: 0.75rem 0.5rem;
        }

        .student-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }

        .period-select,
        .percentage-input {
            min-width: auto;
            width: 100%;
        }

        .submit-btn {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alertCards = document.querySelectorAll('.alert-card');
        alertCards.forEach(card => {
            setTimeout(() => {
                card.style.opacity = '0';
                card.style.transition = 'opacity 0.3s ease-out';
                setTimeout(() => card.remove(), 300);
            }, 3600);
        });
    });
</script>

    <!-- Messages -->
    <?php if ($message): ?>
        <div class="alert-card <?= $msg_type === 'error' ? 'alert-error' : 'alert-success' ?>">
            <i class='bx <?= $msg_type === 'error' ? 'bx-error-circle' : 'bx-check-circle' ?>'></i>
            <?= htmlspecialchars($message, ENT_QUOTES) ?>
        </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="page-header">
            <h2>Encode Grades</h2>
            <p>Encode and submit student grades for your assigned subjects</p>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <div class="filters-header">
            <i class='bx bx-filter-alt'></i>
            <h2>Filters</h2>
        </div>
        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">
                    <i class='bx bx-user'></i>
                    Filter by Student
                </label>
                <select id="filter_student" class="filter-select" onchange="filterTable()">
                    <option value="">All Students</option>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= htmlspecialchars($student['full_name'], ENT_QUOTES) ?>">
                            <?= htmlspecialchars($student['full_name'], ENT_QUOTES) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <!-- Subject Tables -->
    <div class="content-section">
    <?php if (count($students) > 0 && count($subjects) > 0): ?>
        <?php foreach ($subjects as $subject): ?>
            <?php
            // Fetch enrolled students for this subject
            $enrolled_students = [];
            $stmt = $conn->prepare("
                SELECT u.user_id, u.full_name
                FROM enrollments e
                JOIN users u ON e.student_id = u.user_id
                WHERE e.subject_id = ? AND e.status = 'Active'
                ORDER BY u.full_name
            ");
            $stmt->bind_param("i", $subject['subject_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $enrolled_students[] = $row;
            }
            $stmt->close();
            ?>
            <div class="table-card">
                <div class="subject-title">
                    <div class="title"><?= htmlspecialchars($subject['subject_code'], ENT_QUOTES) ?></div>
                    <div class="subtitle"><?= htmlspecialchars($subject['subject_name'], ENT_QUOTES) ?></div>
                </div>
                <div class="table-wrap">
                    <table class="grades-table">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Academic Period</th>
                                <th>Grade Percentage</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($enrolled_students as $student): ?>
                                <?php
                                    $default_key = $student['user_id'] . '_' . $subject['subject_id'] . '_3rd Year - 2nd Semester';
                                    $locked_grade = $existing_grades[$default_key] ?? null;
                                ?>
                                <tr class="grade-row" data-student="<?= htmlspecialchars($student['full_name'], ENT_QUOTES) ?>" data-locked="<?= $locked_grade ? '1' : '0' ?>">
                                    <td style="text-align: left;">
                                        <div class="student-info">
                                            <div class="student-avatar">
                                                <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                                            </div>
                                            <div class="student-details">
                                                <h4><?= htmlspecialchars($student['full_name'], ENT_QUOTES) ?></h4>
                                                <span>Student ID: <?= $student['user_id'] ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <select class="period-select" required <?= $locked_grade ? 'disabled' : '' ?>>
                                            <?php foreach ($periods as $period): ?>
                                                <option value="<?= htmlspecialchars($period, ENT_QUOTES) ?>"
                                                    <?= $period === '3rd Year - 2nd Semester' ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($period, ENT_QUOTES) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <?php if ($locked_grade): ?>
                                            <div class="grade-display">
                                                <i class='bx bx-lock-alt'></i>
                                                <?= number_format($locked_grade['percentage'], 2) ?>% (Locked)
                                            </div>
                                        <?php else: ?>
                                            <input type="number" class="percentage-input" min="0" max="100" step="0.01" placeholder="0-100" required>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($locked_grade): ?>
                                            <?php $badge_class = ($locked_grade['status'] === 'Approved') ? 'badge-approved' : 'badge-pending'; ?>
                                            <span class="badge-status <?= $badge_class ?>">
                                                <i class='bx bx-check-circle'></i>
                                                <?= htmlspecialchars($locked_grade['status'], ENT_QUOTES) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge-status badge-pending">
                                                <i class='bx bx-time'></i>
                                                Not Submitted
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!$locked_grade): ?>
                                            <form method="POST" class="grade-submit-form">
                                                <input type="hidden" name="student_id" value="<?= $student['user_id'] ?>">
                                                <input type="hidden" name="subject_id" value="<?= $subject['subject_id'] ?>">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES) ?>">
                                                <input type="hidden" name="academic_period" class="hidden-period">
                                                <input type="hidden" name="percentage" class="hidden-percentage">
                                                <button type="submit" class="submit-btn">
                                                    <i class='bx bx-send'></i>
                                                    Submit
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

        <script>
            // Existing grades data (passed from PHP)
            const existingGrades = <?= json_encode($existing_grades) ?>;

            function filterTable() {
                const studentFilter = document.getElementById('filter_student').value.toLowerCase();
                const rows = document.querySelectorAll('.grade-row');

                rows.forEach(row => {
                    const student = row.getAttribute('data-student').toLowerCase();
                    const matchStudent = !studentFilter || student.includes(studentFilter);
                    row.style.display = matchStudent ? '' : 'none';
                });
            }

            // Handle period change and check for locked grades
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.period-select').forEach(select => {
                    select.addEventListener('change', function() {
                        const tr = select.closest('tr');
                        if (!tr) return;

                        const percentInput = tr.querySelector('.percentage-input');
                        const form = tr.querySelector('.grade-submit-form');
                        const selectedPeriod = select.value;

                        // Find student_id and subject_id from the form (if form exists)
                        if (form) {
                            const studentId = form.querySelector('input[name="student_id"]').value;
                            const subjectId = form.querySelector('input[name="subject_id"]').value;
                            const key = studentId + '_' + subjectId + '_' + selectedPeriod;

                            if (existingGrades[key] && existingGrades[key].is_locked) {
                                // Lock found - hide input and form
                                if (percentInput) {
                                    percentInput.parentNode.innerHTML = '<div class="grade-display"><i class="bx bx-lock-alt"></i>' + parseFloat(existingGrades[key].percentage).toFixed(2) + '% (Locked)</div>';
                                }
                                if (form) form.style.display = 'none';
                            } else {
                                // No lock - show input and form
                                const td = tr.querySelector('td:nth-child(3)');
                                if (!percentInput || percentInput.style.display === 'none') {
                                    td.innerHTML = '<input type="number" class="percentage-input" min="0" max="100" step="0.01" placeholder="0-100" required>';
                                }
                                if (form) form.style.display = '';
                            }
                        }
                    });
                });

                // Copy visible selects/inputs into hidden fields before submitting
                document.querySelectorAll('.grade-submit-form').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        const tr = form.closest('tr');
                        if (!tr) return;
                        const period = tr.querySelector('.period-select');
                        const perc = tr.querySelector('.percentage-input');
                        if (period && perc) {
                            form.querySelector('.hidden-period').value = period.value;
                            form.querySelector('.hidden-percentage').value = perc.value;
                        }
                    });
                });
            });
        </script>
    <?php else: ?>
        <div class="no-data">
            <div class="no-data-icon">
                <i class='bx bx-book-open'></i>
            </div>
            <h3>No Data Available</h3>
            <p>No students or subjects are currently assigned to you.</p>
        </div>
    <?php endif; ?>
</div>
