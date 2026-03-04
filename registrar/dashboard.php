<?php
/*
 * REGISTRAR DASHBOARD CONTENT
 * This file is included by index.php (router)
 * Do not include HTML head/body tags - only content
 */

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

// Registrar access control
requireRole([2]);

$registrar_name = $_SESSION["user_name"] ?? "Registrar";
?>

<style>
    .registrar-dashboard { 
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

<div class="registrar-dashboard">

    <!-- Welcome Section -->
    <div class="dashboard-section">
        <div class="welcome-area">
            <h1>Welcome, <?= htmlspecialchars($registrar_name, ENT_QUOTES) ?></h1>
            <p>Use the menu on the left to manage enrollments and grades.</p>
        </div>
    </div>

    <!-- Placeholder -->
    <div class="dashboard-section">
        <div class="placeholder-card">
            <h3>Registrar Dashboard</h3>
            <p>Your tools are available in the sidebar.</p>
        </div>
    </div>

</div>

