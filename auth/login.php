<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/csrf.php';
require "../config/db.php";
require "../includes/audit.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (empty($_POST['csrf_token'])) {
        http_response_code(400);
        die('Missing CSRF token');
    }
    csrf_validate_or_die($_POST['csrf_token']);

    $email = $_POST["email"];
    $pass  = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT user_id, password_hash, role_id FROM users WHERE email = ?"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($pass, $row['password_hash'])) {
            secure_session_regenerate();
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["role_id"] = $row["role_id"];

            logAction($conn, $row["user_id"], "User logged in");

            header("Location: ../index.php");
            exit;
        }
    }
    $error = "Invalid credentials";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grades & Assessment Management Subsystem</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=Fraunces:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        :root {
            --navy:         #0f246c;
            --navy-mid:     #1a3a8f;
            --blue:         #3B82F6;
            --blue-dark:    #1E40AF;
            --blue-light:   #60A5FA;
            --blue-pale:    #DBEAFE;
            --blue-faint:   #EFF6FF;
            --accent:       #2563EB;
            --border-blue:  rgba(59, 130, 246, 0.18);
            --white:        #ffffff;
            --bg:           #F8FAFC;
            --bg-warm:      #F1F5F9;
            --rule:         #E2E8F0;
            --ink:          #0F172A;
            --ink-soft:     #374151;
            --ink-muted:    #64748B;
            --ink-faint:    #94A3B8;
            --shadow-sm:    0 2px 8px rgba(15, 36, 108, 0.07);
            --shadow-md:    0 8px 32px rgba(15, 36, 108, 0.10);
            --shadow-blue:  0 6px 24px rgba(37, 99, 235, 0.22);
            --radius-sm:    8px;
            --radius-md:    14px;
            --radius-lg:    20px;
            --transition:   all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--ink);
            line-height: 1.6;
        }

        /* LOGIN CONTAINER */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            border: 1px solid var(--rule);
        }

        .login-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .login-header h1 {
            font-family: 'Fraunces', serif;
            font-size: 1.75rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 0.5rem;
        }

        .login-header p {
            font-size: 0.9375rem;
            color: var(--ink-muted);
        }

        /* FORM */
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .field label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--ink-soft);
        }

        .field input {
            padding: 0.75rem 1rem;
            border: 1px solid var(--rule);
            border-radius: var(--radius-sm);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.9375rem;
            background: var(--white);
            color: var(--ink);
            transition: var(--transition);
        }

        .field input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }

        .field input::placeholder {
            color: var(--ink-faint);
        }

        /* ERROR MESSAGE */
        .error-message {
            padding: 0.875rem 1rem;
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: var(--radius-sm);
            color: #dc2626;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .error-message i {
            font-size: 1rem;
        }

        /* BUTTON */
        .submit-btn {
            padding: 0.75rem 1rem;
            background: linear-gradient(135deg, var(--accent), var(--blue-dark));
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 0.9375rem;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: var(--shadow-blue);
        }

        .submit-btn:hover {
            opacity: 0.88;
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(37, 99, 235, 0.32);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* FOOTER */
        .login-footer {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--rule);
            text-align: center;
        }

        .login-footer p {
            font-size: 0.8125rem;
            color: var(--ink-muted);
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .login-card {
                margin: 0 1rem;
                border-radius: var(--radius-md);
            }

            .login-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>

<!-- LOGIN CONTAINER -->
<div class="login-container">
    <div class="login-card">
        <div class="login-header">
            <h1>Welcome Back</h1>
            <p>Sign in to your account</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="error-message">
                <i class='bx bx-x-circle'></i>
                <span><?php echo htmlspecialchars($error, ENT_QUOTES); ?></span>
            </div>
        <?php endif; ?>

        <form method="post" class="login-form">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES); ?>">

            <div class="field">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required autofocus>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••••" required>
            </div>

            <button type="submit" class="submit-btn">Sign In</button>
        </form>

        <div class="login-footer">
            <p>&copy; 2026 · Grades &amp; Assessment Management Subsystem</p>
        </div>
    </div>
</div>

</body>
</html>