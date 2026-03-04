<?php
/* 
 * STUDENT DASHBOARD CONTENT
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
$student_name = $_SESSION["user_name"] ?? "Student";
?>

<style>
    .student-dashboard { 
        padding: 0;
    }
    .dashboard-section { 
        margin-bottom: 3rem;
    }
    .welcome-area {
        background: linear-gradient(135deg, #3B82F6 0%, #1E40AF 100%);
        color: white;
        padding: 3rem 2rem;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 2rem;
    }
    .welcome-area h1 {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .welcome-area p {
        font-size: 1rem;
        opacity: 0.9;
        margin: 0;
    }
    .placeholder-card {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
        color: #64748b;
    }
    .placeholder-card h3 {
        color: #0f246c;
        margin-bottom: 0.5rem;
    }
</style>

<div class="student-dashboard">

    <!-- Welcome Section -->
    <div class="dashboard-section">
        <div class="welcome-area">
            <h1>Welcome, <?= htmlspecialchars($student_name, ENT_QUOTES) ?></h1>
            <p>Your profile and academic summary will appear here.</p>
        </div>
    </div>

    <!-- Placeholder -->
    <div class="dashboard-section">
        <div class="placeholder-card">
            <h3>Student Dashboard</h3>
            <p>Use the menu on the left to request enrollment, check your enrollment status, or view your grades.</p>
        </div>
    </div>

</div>
