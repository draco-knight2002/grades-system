<?php
require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/session.php';

// Check if user is authenticated
if (!isset($_SESSION["user_id"], $_SESSION["role_id"])) {
    http_response_code(401);
    die("Unauthorized access");
}

$role_id = (int)$_SESSION["role_id"];
$user_id = (int)$_SESSION["user_id"];
$user_name = $_SESSION["user_name"] ?? "User";

// Define menu structure per role
$menus = [
    // Faculty (role_id = 1)
    1 => [
        ['icon' => 'bx-tachometer', 'label' => 'Dashboard', 'sublabel' => 'Overview', 'link' => '?page=dashboard'],
        ['icon' => 'bx-pencil', 'label' => 'Submit Grades', 'sublabel' => 'Encode grades', 'link' => '?page=submit_grades'],
        ['icon' => 'bx-show', 'label' => 'View Submitted Grades', 'sublabel' => 'Review encoded', 'link' => '?page=view_grades'],
        ['icon' => 'bx-edit', 'label' => 'Request Grade Correction', 'sublabel' => 'File corrections', 'link' => '?page=request_correction'],
    ],
    // Registrar (role_id = 2)
    2 => [
        ['icon' => 'bx-tachometer', 'label' => 'Dashboard', 'sublabel' => 'Overview', 'link' => '?page=dashboard'],
        ['icon' => 'bx-check-shield', 'label' => 'Pending Enrollment Requests', 'sublabel' => 'Approve/reject', 'link' => '?page=pending_enrollments'],
        ['icon' => 'bx-list-check', 'label' => 'Pending Grade Submissions', 'sublabel' => 'Verify grades', 'link' => '?page=pending_grades'],
        ['icon' => 'bx-comment-add', 'label' => 'Pending Correction Requests', 'sublabel' => 'Review corrections', 'link' => '?page=pending_corrections'],
    ],
    // Student (role_id = 3)
    3 => [
        ['icon' => 'bx-tachometer', 'label' => 'Dashboard', 'sublabel' => 'Overview', 'link' => '?page=dashboard'],
        ['icon' => 'bx-user-plus', 'label' => 'Request Enrollment', 'sublabel' => 'Enroll subjects', 'link' => '?page=request_enrollment'],
        ['icon' => 'bx-check-circle', 'label' => 'Enrollment Status', 'sublabel' => 'View requests', 'link' => '?page=enrollment_status'],
        ['icon' => 'bx-book-open', 'label' => 'View Grades', 'sublabel' => 'Your grades', 'link' => '?page=view_grades'],
    ],
];

// Get role label
$role_labels = [1 => 'Faculty', 2 => 'Registrar', 3 => 'Student'];
$role_label = $role_labels[$role_id] ?? 'User';
$role_icon = [1 => 'bx-chalkboard', 2 => 'bx-shield', 3 => 'bx-user'][($role_id)] ?? 'bx-user';

// Account title for sidebar section
$account_titles = [1 => 'FACULTY ACCOUNT', 2 => 'REGISTRAR ACCOUNT', 3 => 'STUDENT ACCOUNT'];
$account_title = $account_titles[$role_id] ?? 'USER ACCOUNT';

// Get current page
$current_page = $_GET['page'] ?? 'dashboard';

// Determine project base path for absolute links
$base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades &amp; Assessment Management Subsystem</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3B82F6;
            --primary-dark: #1E40AF;
            --primary-light: #93C5FD;
            --accent-color: #2563EB;
            --accent-hover: #F8FAFC;
            --success-color: #3B82F6;
            --success-light: #60A5FA;
            --success-shadow: rgba(59, 130, 246, 0.4);
            --bg-color: #F8FAFC;
            --sidebar-bg: #0f246cff;
            --sidebar-secondary: #1E293B;
            --sidebar-border: rgba(59, 130, 246, 0.25);
            --topbar-bg: #FFFFFF;
            --border-color: rgba(59, 130, 246, 0.15);
            --shadow-light: 0 2px 12px rgba(59, 130, 246, 0.12);
            --shadow-lg: 4px 0 20px rgba(0, 0, 0, 0.4);
            --text-primary: #0F172A;
            --text-primary-light: #EFF6FF;
            --text-secondary: #64748B;
            --text-secondary-light: #CBD5E1;
            --text-light: rgba(255, 255, 255, 0.65);
            --hover-bg: rgba(59, 130, 246, 0.15);
            --hover-bg-light: #EFF6FF;
            --active-bg: rgba(59, 130, 246, 0.25);
            --sidebar-width: 300px;
            --navbar-height: 64px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            overflow-x: hidden;
        }
        .navbar-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .time-display {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.95rem;
        }

        .date-separator {
            color: var(--text-secondary);
            font-weight: 400;
            font-size: 0.7rem;
        }

        .icon-btn {
            background: transparent;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 10px;
            color: var(--text-secondary);
            font-size: 1.4rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
                position: relative;
        }

        .icon-btn:hover {
            background: var(--hover-bg-light);
            color: var(--text-primary);
        }

        .icon-btn:active {
            transform: scale(0.95);
        }

        .icon-btn .badge-dot {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            border: 2px solid var(--topbar-bg);
            animation: badgePulse 2s infinite;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            background: transparent;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            font-size: 1.4rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .profile-icon:hover {       
            background: var(--hover-bg-light);
            color: var(--text-primary);
            }

        /*  SIDEBAR  */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--sidebar-bg);            
            border-right: 1px solid var(--sidebar-border);
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 1000;
            transition: var(--transition);
            box-shadow: var(--shadow-lg);
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.15);
            border-radius: 10px;
            margin: 8px 0;
            border: 1px solid rgba(59, 130, 246, 0.1);
            box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.2);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            transition: all 0.4s ease;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.4);
            border-color: rgba(59, 130, 246, 0.3);
            box-shadow: 0 0 12px rgba(59, 130, 246, 0.4);
            transform: scaleX(1.2);
        }


        .sidebar-header {
            background: var(--sidebar-bg);
            padding: 1.5rem;
            border-bottom: 2px solid var(--sidebar-border);
            position: relative;
            overflow: hidden;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1rem;
            position: relative;
            z-index: 1;
        }

        .logo {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            border-radius: 12px;
            border: 2px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
            box-shadow: var(--shadow-light);
            animation: logoFloat 3s ease-in-out infinite;
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .brand-info h1 {
            font-size: .85rem;
            font-weight: 700;
            color: white;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .brand-info p {
            font-size: 0.7rem;
            color: var(--text-secondary-light);
            margin: 0;
            font-weight: 500;
        }

        .system-status {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 1px solid var(--sidebar-border);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .system-status {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            border: 1px solid var(--success-light);
            backdrop-filter: blur(10px);
            position: relative;
            z-index: 1;
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), inset 0 0 10px rgba(59, 130, 246, 0.1);
            animation: statusGlow 2s ease-in-out infinite;
        }

        @keyframes statusGlow {
            0%, 100% { 
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3), inset 0 0 10px rgba(59, 130, 246, 0.1);
            border-color: var(--success-light);
        }
        50% { 
            box-shadow: 0 0 30px rgba(59, 130, 246, 0.5), inset 0 0 20px rgba(59, 130, 246, 0.2);
            border-color: var(--accent-color);
        }
        }

        .status-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .status-left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-indicator {
            width: 8px;
            height: 8px;
            background: #22c55e;
            border-radius: 50%;
            animation: pulse 2s infinite;
            box-shadow: 0 0 8px var(--success-light);
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(0.95); }
        }

        .status-text {
            font-size: 0.75rem;
            color: #ffffffff;
        }

        .nav {
            padding: 1.5rem 1rem;
        }

        .section-title {
            font-size: 0.65rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.55) !important;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 1.5rem 1rem 0.75rem 1rem;
            margin-top: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
        }

        .section-title::before {
            content: '';
            width: 8px;
            height: 8px;
            background: var(--accent-color);
            border-radius: 50%;
            box-shadow: 0 0 12px var(--accent-color);
            animation: pulse 2s infinite;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.3), transparent);
            margin-left: 0.5rem;
        }

        .section-title:first-child {
            margin-top: 0;
        }

        .nav ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nav ul li {
            position: relative;
        }

        .nav ul li a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            color: var(--text-primary-light);
            text-decoration: none;
            border-radius: 12px;
            transition: var(--transition);
            font-size: 0.9rem;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .nav ul li a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(180deg, var(--accent-color), var(--primary-light));
            transform: scaleY(0);
            transition: var(--transition);
            box-shadow: 0 0 15px var(--accent-color);
        }

        .nav ul li a::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(147, 197, 253, 0.05));
            opacity: 0;
            transition: var(--transition);
            z-index: -1;
        }

        .nav ul li a:hover {
            background: var(--hover-bg);
            color: white;
            transform: translateX(8px);
            border-color: var(--sidebar-border);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .nav ul li a:hover::before {
            transform: scaleY(1);
        }

        .nav ul li a:hover::after {
            opacity: 1;
        }

        .nav ul li a:hover .main-text {
            color: white;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }

        .nav ul li a:hover .sub-text {
            color: rgba(255, 255, 255, 0.8);
        }

        .nav ul li a.active {
            background: var(--active-bg);
            color: white;
            font-weight: 600;
            border: 1px solid var(--sidebar-border);
            box-shadow: 0 4px 16px rgba(59, 130, 246, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .nav ul li a.active::before {
            transform: scaleY(1);
        }

        .nav ul li a.active::after {
            opacity: 1;
        }

        .nav ul li a.active .main-text {
            color: white;
        }

        .nav ul li a.active .sub-text {
            color: rgba(255, 255, 255, 0.9);
        }

        .left {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .icon {
            font-size: 1.3rem;
            transition: var(--transition);
        }

        .icon-wrapper {
            width: 36px;
            height: 36px;
            background: rgba(59, 130, 246, 0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .icon-wrapper::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), transparent);
            opacity: 0;
            transition: var(--transition);
        }

        .nav ul li a:hover .icon-wrapper {
            background: rgba(59, 130, 246, 0.25);
            transform: scale(1.15) rotate(8deg);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .nav ul li a:hover .icon-wrapper::before {
            opacity: 1;
        }

        .nav ul li a:hover .icon {
            transform: scale(1.1);
            filter: drop-shadow(0 0 8px rgba(255, 255, 255, 0.8));
        }

        .nav ul li a.active .icon-wrapper {
            background: rgba(59, 130, 246, 0.3);
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
        }

        .nav-label {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
            
        }

        .nav-label .main-text {
            font-size: 0.9rem;
            font-weight: 500;
            transition: var(--transition);
        }

        .nav-label .sub-text {
            font-size: 0.7rem;
            opacity: 0.7;
            font-weight: 400;
            transition: var(--transition);
        }

       .sub-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 0 0 12px 12px;
            position: relative;
            opacity: 0;
            margin-top: 0.25rem;
        }

        .nav ul li.open .sub-menu {
            max-height: 600px;
            padding: 0.5rem 0 0.5rem 0.5rem;
            opacity: 1;
        }

        .sub-menu::before {
            content: '';
            position: absolute;
            left: 1.3rem;
            top: 0.5rem;
            bottom: 0.5rem;
            width: 2px;
            background: linear-gradient(180deg, var(--accent-color), rgba(59, 130, 246, 0.1));
            border-radius: 2px;
        }

        .sub-menu li {
            position: relative;
            animation: slideIn 0.3s ease forwards;
            opacity: 0;
        }

        .nav ul li.open .sub-menu li:nth-child(1) { animation-delay: 0.05s; }
        .nav ul li.open .sub-menu li:nth-child(2) { animation-delay: 0.1s; }
        .nav ul li.open .sub-menu li:nth-child(3) { animation-delay: 0.15s; }
        .nav ul li.open .sub-menu li:nth-child(4) { animation-delay: 0.2s; }
        .nav ul li.open .sub-menu li:nth-child(5) { animation-delay: 0.25s; }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .sub-menu li::before {
            content: '';
            position: absolute;
                left: 1rem;
            top: 50%;
            width: 1rem;
            height: 2px;
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.5), rgba(59, 130, 246, 0.2));
            transition: var(--transition);
        }

        .sub-menu li:hover::before {
            width: 1.25rem;
            background: linear-gradient(90deg, var(--accent-color), rgba(59, 130, 246, 0.5));
        }

        .sub-menu li::after {
            display: none;
        }

        .sub-menu li a {
            padding: 0.65rem 0.75rem 0.65rem 1.5rem; /* Reduced left padding */
            font-size: 0.8rem;
            font-weight: 400;
            border-radius: 8px;
            margin: 0 0.5rem 0.25rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem; 
        }

        .sub-menu li a .icon {
            font-size: 1rem; 
            flex-shrink: 0;
        }

        .sub-menu li a span {
            flex: 1;
        }


        .sub-menu li::before {
            content: '';
            position: absolute;
            left: 1rem;
            top: 50%;
            width: 1rem;
            height: 2px;
            background: linear-gradient(90deg, rgba(59, 130, 246, 0.5), rgba(59, 130, 246, 0.2));
            transition: var(--transition);
            pointer-events: none; 
        }

        .sub-menu li a {
            padding: 0.65rem 0.75rem 0.65rem 2.5rem;
        }

        .sub-menu li a:hover {
            transform: translateX(4px);
            background: rgba(59, 130, 246, 0.2);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
        }

        .sub-menu li a .icon-wrapper {
            width: 16px;
            height: 16px;
            background: rgba(59, 130, 246, 0.15);
            border-radius: 6px;
            flex-shrink: 0;
            display: flex; 
            align-items: center; 
            justify-content: center;
        }

        .sub-menu li a .icon {
            font-size: 0.7rem;
        }

        .sub-menu li a .nav-label {
            display: flex;
            flex-direction: column;
            gap: 0.1rem; 
        }

        .sub-menu li a .nav-label .main-text {
            font-size: 0.8rem;
            line-height: 1.2;
        }

        .sub-menu li a .nav-label .sub-text {
            font-size: 0.65rem;
            line-height: 1.2;
        }
        .bx-chevron-down {
            font-size: 1.2rem;
            transition: transform 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            color: var(--accent-color);
        }

        .nav ul li.open > a .bx-chevron-down {
            transform: rotate(180deg);
            filter: drop-shadow(0 0 6px var(--accent-color));
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 7px;
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            border-radius: 11px;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
            animation: badgePulse 2s infinite;
        }

        @keyframes badgePulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .sidebar-footer {
            margin: 1rem;
            padding: 0;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid var(--sidebar-border);
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .sidebar-footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(
                90deg,
                var(--primary-color),
                var(--accent-color),
                var(--primary-light),
                var(--primary-color)
            );
            background-size: 200% 100%;
            animation: gradientShift 3s linear infinite;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }

        .footer-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem;
            background: linear-gradient(
                135deg,
                rgba(59, 130, 246, 0.15),
                rgba(59, 130, 246, 0.05)
            );
        }

        .footer-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(
                135deg,
                var(--primary-color),
                var(--primary-dark)
            );
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 16px var(--success-shadow);
            flex-shrink: 0;
            position: relative;
            animation: iconFloat 3s ease-in-out infinite;
            }

        @keyframes iconFloat {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-5px) rotate(5deg); }
            }

        .footer-icon::before {
            content: '';
            position: absolute;
            inset: -2px;
            background: linear-gradient(
            135deg,
                var(--primary-color),
                var(--accent-color)
            );
            border-radius: 14px;
            opacity: 0.5;
            filter: blur(8px);
            z-index: -1;
            animation: pulse 2s ease-in-out infinite;
        }

        .footer-icon i {
            font-size: 1.5rem;
            color: #ffffff;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
        }

        .footer-content {
            flex: 1;
            min-width: 0;
        }

        .footer-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary-light);
            margin-bottom: 0.25rem;
            line-height: 1.3;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .footer-subtitle {
            font-size: 0.75rem;
            color: var(--text-secondary-light);
            font-weight: 500;
            line-height: 1.2;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-subtitle::before {
            content: '';
            width: 6px;
            height: 6px;
            background: var(--success-color);
            border-radius: 50%;
            animation: pulse 2s infinite;
            box-shadow: 0 0 8px var(--success-color);
        }

        .footer-divider {
            height: 1px;
            background: linear-gradient(
                90deg,
                transparent,
                var(--sidebar-border),
                transparent
            );
            margin: 0;
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            background: rgba(0, 0, 0, 0.2);
        }

        .version {
            font-size: 0.75rem;
            color: var(--text-light);
            font-weight: 700;
            background: rgba(59, 130, 246, 0.15);
            padding: 0.35rem 0.75rem;
            border-radius: 8px;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .status-online {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            color: var(--success-color);
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-online::before {
            content: '';
            width: 8px;
            height: 8px;
            background: var(--success-color);
            border-radius: 50%;
            box-shadow: 0 0 0 0 var(--success-shadow);
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(59, 130, 246, 0); }
            100% { box-shadow: 0 0 0 0 rgba(59, 130, 246, 0); }
        }


        /* TOP NAVBAR */
        .top-navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--navbar-height);
            background: var(--topbar-bg);
            z-index: 999;
            transition: var(--transition);
            box-shadow: var(--shadow-light);
        }

        .top-navbar.expanded {
            left: 0 !important;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .navbar-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0 2rem;
            height: 100%;
        }

        .navbar-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 100%;
            gap: 1.5rem;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            flex: 1;
        }

        /*  TOGGLE BUTTON  */
        .toggle-btn {
            background: transparent;
            border: 1px solid var(--border-color);
            width: 44px;
            height: 44px;
            border-radius: 10px;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .toggle-btn:hover {
            background: var(--hover-bg-light);
            color: var(--text-primary);
            border-color: var(--accent-color);
        }

            .toggle-btn:active {
            transform: scale(0.95);
        }

        /*  MAIN CONTENT  */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 1.5rem 2rem;
            min-height: calc(100vh - var(--navbar-height));
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-sizing: border-box;
        }

        .main-content.expanded {
            margin-left: 0 !important;
        }

        /*  RESPONSIVE  */

        @media (max-width: 768px) {
        .sidebar {
            top: 0;
            transform: translateX(-100%);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .top-navbar {
            left: 0;
        }

        .navbar-container {
            padding: 0 1rem;
        }

        .time-display {
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
        }
    
        .icon-btn {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }

        .profile-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }
    
        .navbar-right {
            gap: 0.5rem;
        }
    }

        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .overlay.show {
            display: block;
            opacity: 1;
        }
    </style>
</head>
<body>
    <div class="overlay" id="overlay"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="brand">
                <div class="logo">
                    <i class='bx <?= htmlspecialchars($role_icon) ?>'></i>
                </div>
                <div class="brand-info">
                    <h1>GRADES & ASSESSMENT</h1>
                    <p><?= htmlspecialchars($role_label) ?></p>
                </div>
            </div>

            <div class="system-status">
                <div class="status-content">
                    <div class="status-left">
                        <span class="status-indicator"></span>
                        <span class="status-text">Online & Operational</span>
                    </div>
                </div>
            </div>
        </div>

        <nav class="nav">
            <?php if (!empty($menus[$role_id])): ?>
                <span class="section-title"><?= htmlspecialchars(
                    $account_title,
                    ENT_QUOTES
                ) ?></span>
                <ul>
                    <?php foreach ($menus[$role_id] as $item): 
                        $is_active = (strpos($item['link'], $current_page) !== false) ? ' active' : '';
                        $has_submenu = isset($item['submenu']);
                    ?>
                        <li class="<?= $is_active ? 'open' : '' ?>">
                            <a href="<?= htmlspecialchars($item['link']) ?>" class="<?= $is_active ?>" <?= $has_submenu ? 'onclick="toggleSubmenu(event)"' : '' ?>>
                                <span class="left">
                                    <div class="icon-wrapper">
                                        <i class='bx <?= htmlspecialchars($item['icon']) ?> icon'></i>
                                    </div>
                                    <span class="nav-label">
                                        <span class="main-text"><?= htmlspecialchars($item['label']) ?></span>
                                        <span class="sub-text"><?= htmlspecialchars($item['sublabel']) ?></span>
                                    </span>
                                </span>
                                <?php if ($has_submenu): ?>
                                    <i class='bx bx-chevron-down'></i>
                                <?php endif; ?>
                            </a>
                            <?php if ($has_submenu): ?>
                                <ul class="sub-menu">
                                    <?php foreach ($item['submenu'] as $subitem): ?>
                                        <li>
                                            <a href="<?= htmlspecialchars($subitem['link']) ?>">
                                                <i class='bx <?= htmlspecialchars($subitem['icon']) ?> icon'></i>
                                                <span><?= htmlspecialchars($subitem['label']) ?></span>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <span class="section-title">ACCOUNT</span>
                <ul>
                    <li>
                        <a href="<?= htmlspecialchars($base_path) ?>/logout.php">
                            <span class="left">
                                <div class="icon-wrapper">
                                    <i class='bx bx-log-out icon'></i>
                                </div>
                                <span class="nav-label">
                                    <span class="main-text">Logout</span>
                                    <span class="sub-text">Sign out</span>
                                </span>
                            </span>
                        </a>
                    </li>
                </ul>
            <?php endif; ?>

            <div class="sidebar-footer">
                <div class="footer-header">
                    <div class="footer-icon">
                        <i class='bx bx-shield-alt-2'></i>
                    </div>
                    <div class="footer-content">
                        <div class="footer-title">Secure Platform</div>
                        <div class="footer-subtitle">All systems operational</div>
                    </div>
                </div>

                <div class="footer-divider"></div>

                <div class="footer-bottom">
                    <span class="version">System</span>
                    <div class="status-online">Online</div>
                </div>
            </div>
        </nav>
    </aside>

    <nav class="top-navbar" id="topNavbar">
        <div class="navbar-container">
<div class="navbar-content">
    <div class="navbar-left">
        <button class="toggle-btn" id="toggleBtn">
            <i class='bx bx-menu'></i>
        </button>
    </div>
    
    <div class="navbar-right">
        <!-- Time Display with Day and Date -->
        <div class="time-display" id="timeDisplay">
            <span id="currentTime"></span>
            <span class="date-separator">•</span>
            <span id="currentDate"></span>
        </div>
        
        <!-- Fullscreen Button -->
        <button class="icon-btn" id="fullscreenBtn" title="Toggle Fullscreen">
            <i class='bx bx-fullscreen'></i>
        </button>
        
        <!-- Search Button -->
        <button class="icon-btn" id="searchBtn" title="Search">
            <i class='bx bx-search'></i>
        </button>

        <!-- Notification Button -->
        <button class="icon-btn" id="notificationBtn" title="Notifications">
            <i class='bx bx-bell'></i>
            <span class="badge-dot"></span>
        </button>

        <!-- Profile Icon -->
        <div class="profile-icon" id="profileBtn" title="Profile">
            <i class='bx bx-user'></i>
        </div>
    </div>
</div>

                    
    </nav>

<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleBtn');
    const overlay = document.getElementById('overlay');
    const topbar = document.getElementById('topNavbar');

    // Real-Time Clock Display - Philippines Time (UTC+8)
    function updateTime() {
        const now = new Date();
        
        // Get Philippines time (UTC+8)
        const phTime = new Date(now.toLocaleString('en-US', { timeZone: 'Asia/Manila' }));
        
        // Format time (without seconds)
        let hours = phTime.getHours();
        const minutes = String(phTime.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        
        hours = hours % 12;
        hours = hours ? hours : 12;
        
        const timeString = `${hours}:${minutes} ${ampm}`;
        
        // Format date (abbreviated format)
        const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        const dayName = days[phTime.getDay()];
        const monthName = months[phTime.getMonth()];
        const day = phTime.getDate();
        
        const dateString = `${dayName}, ${monthName} ${day}`;
        
        // Update DOM
        const timeElement = document.getElementById('currentTime');
        const dateElement = document.getElementById('currentDate');
        
        if (timeElement) {
            timeElement.textContent = timeString;
        }
        if (dateElement) {
            dateElement.textContent = dateString;
        }
    }

    // Start clock immediately
    updateTime();
    setInterval(updateTime, 1000);

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            topbar.classList.toggle('expanded'); 
        }
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('show');
        overlay.classList.remove('show');
    });

    function toggleSubmenu(element) {
        if (element.preventDefault) {
            element.preventDefault();
        }
        const parent = element.currentTarget ? element.currentTarget.parentElement : element.parentElement;
        document.querySelectorAll('.nav ul li.open').forEach(item => {
            if (item !== parent) item.classList.remove('open');
        });
        parent.classList.toggle('open');
    }

    // Make toggleSubmenu available globally
    window.toggleSubmenu = toggleSubmenu;

    document.querySelectorAll('.nav a').forEach(link => {
        link.addEventListener('click', function() {
            if (!this.hasAttribute('onclick')) {
                document.querySelectorAll('.nav a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
                if (window.innerWidth <= 768) {
                    sidebar.classList.remove('show');
                    overlay.classList.remove('show');
                }
            }
        });
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('expanded');
            topbar.classList.remove('expanded');
        }
    });

    // Fullscreen Toggle
    const fullscreenBtn = document.getElementById('fullscreenBtn');
    
    fullscreenBtn.addEventListener('click', () => {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.log('Fullscreen error:', err);
            });
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    });

    // Update icon when fullscreen changes
    document.addEventListener('fullscreenchange', () => {
        const icon = fullscreenBtn.querySelector('i');
        if (document.fullscreenElement) {
            icon.className = 'bx bx-exit-fullscreen';
        } else {
            icon.className = 'bx bx-fullscreen';
        }
    });

    // Search Button 
    const searchBtn = document.getElementById('searchBtn');
    
    searchBtn.addEventListener('click', () => {
        const searchQuery = prompt('Enter search query:');
        if (searchQuery && searchQuery.trim()) {
            console.log('Searching for:', searchQuery);
            alert('Searching for: ' + searchQuery);
        }
    });

    // Notification Button 
    const notificationBtn = document.getElementById('notificationBtn');
    
    notificationBtn.addEventListener('click', () => {
        alert('Notifications panel will open here');
    });

    // Profile Button 
    const profileBtn = document.getElementById('profileBtn');
    
    profileBtn.addEventListener('click', () => {
        alert('Profile menu will open here');
    });
});
</script>
</body>
</html>