<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIEMS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=Fraunces:ital,wght@0,300;0,400;0,600;1,300;1,400&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<style>
:root {
    /* Colors */
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

    /* Backgrounds */
    --bg:           #F8FAFC;
    --bg-warm:      #F1F5F9;

    /* Borders */
    --rule:         #E2E8F0;

    /* Text */
    --ink:          #0F172A;
    --ink-soft:     #374151;
    --ink-muted:    #64748B;
    --ink-faint:    #94A3B8;

    /* Shadows */
    --shadow-sm:    0 2px 8px rgba(15, 36, 108, 0.07);
    --shadow-md:    0 8px 32px rgba(15, 36, 108, 0.10);
    --shadow-blue:  0 6px 24px rgba(37, 99, 235, 0.22);

    /* Border Radius */
    --radius-sm:    8px;
    --radius-md:    14px;
    --radius-lg:    20px;
    --radius-xl:    28px;

    /* Transition */
    --transition:   all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}


/* RESET & BASE */
*,
*::before,
*::after {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

html {
    scroll-behavior: smooth;
}

body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--ink);
    line-height: 1.6;
    overflow-x: hidden;
}


/* NAVIGATION */
nav.topnav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 200;
    background: rgba(15, 36, 108, 0.97);
    backdrop-filter: blur(16px);
    border-bottom: 1px solid rgba(59, 130, 246, 0.2);
}

.nav-inner {
    max-width: 1160px;
    margin: 0 auto;
    padding: 0 2rem;
    height: 68px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

/* Brand / Logo */
.brand {
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.brand-mark {
    width: 42px;
    height: 42px;
    flex-shrink: 0;
}

.brand-name {
    font-family: 'Fraunces', serif;
    font-size: 1.125rem;
    font-weight: 600;
    color: white;
    letter-spacing: -0.02em;
}

.brand-tagline {
    font-size: 0.6875rem;
    color: rgba(255, 255, 255, 0.4);
    font-weight: 400;
    margin-top: 1px;
}

/* Nav Links */
.nav-links {
    display: flex;
    align-items: center;
    gap: 2rem;
    list-style: none;
}

.nav-links a {
    color: rgba(255, 255, 255, 0.65);
    text-decoration: none;
    font-size: 0.9375rem;
    font-weight: 450;
    transition: color 0.2s;
}

.nav-links a:hover {
    color: white;
}

.nav-btn {
    padding: 0.5rem 1.25rem;
    background: var(--accent) !important;
    color: white !important;
    border-radius: var(--radius-sm);
    font-weight: 500 !important;
    border: none;
    box-shadow: var(--shadow-blue);
    transition: opacity 0.2s, transform 0.2s !important;
}

.nav-btn:hover {
    opacity: 0.88;
    transform: translateY(-1px);
}


/* HERO SECTION */
.hero {
    min-height: 100vh;
    padding-top: 68px;
    background: var(--white);
    display: grid;
    grid-template-columns: 1fr 1fr;
    align-items: center;
    gap: 0;
    max-width: 1160px;
    margin: 0 auto;
    padding-left: 2rem;
    padding-right: 2rem;
}

.hero-left {
    padding: 3rem 3rem 3rem 0;
}

/* Eyebrow Badge */
.hero-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: var(--blue-faint);
    border: 1px solid var(--blue-pale);
    color: var(--accent);
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.09em;
    text-transform: uppercase;
    padding: 0.375rem 0.875rem;
    border-radius: 50px;
    margin-bottom: 1.75rem;
    animation: fadeUp 0.6s ease backwards;
}

.hero-eyebrow span {
    width: 5px;
    height: 5px;
    background: var(--accent);
    border-radius: 50%;
}

/* Hero Title */
.hero-title {
    font-family: 'Fraunces', serif;
    font-size: 3.25rem;
    font-weight: 400;
    line-height: 1.15;
    letter-spacing: -0.03em;
    color: var(--ink);
    margin-bottom: 1.5rem;
    animation: fadeUp 0.6s ease 0.1s backwards;
}

.hero-title em {
    font-style: italic;
    color: var(--accent);
}

/* Hero Description */
.hero-desc {
    font-size: 1.0625rem;
    color: var(--ink-soft);
    line-height: 1.75;
    max-width: 460px;
    margin-bottom: 2.5rem;
    font-weight: 350;
    animation: fadeUp 0.6s ease 0.2s backwards;
}

/* Hero Buttons */
.hero-actions {
    display: flex;
    gap: 0.875rem;
    animation: fadeUp 0.6s ease 0.3s backwards;
}

.btn-solid {
    padding: 0.8125rem 1.75rem;
    background: linear-gradient(135deg, var(--accent), var(--blue-dark));
    color: white;
    border-radius: var(--radius-md);
    font-size: 0.9375rem;
    font-weight: 500;
    text-decoration: none;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: var(--shadow-blue);
}

.btn-solid:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(37, 99, 235, 0.32);
}

.btn-ghost {
    padding: 0.8125rem 1.75rem;
    background: transparent;
    color: var(--ink-soft);
    border: 1.5px solid var(--rule);
    border-radius: var(--radius-md);
    font-size: 0.9375rem;
    font-weight: 500;
    text-decoration: none;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-ghost:hover {
    border-color: var(--accent);
    color: var(--accent);
    background: var(--blue-faint);
    transform: translateY(-2px);
}

/* Trust Bar */
.hero-trust {
    margin-top: 3rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    animation: fadeUp 0.6s ease 0.4s backwards;
}

.trust-avatars {
    display: flex;
}

.trust-avatars .av {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid var(--white);
    background: var(--blue-faint);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--accent);
    margin-left: -8px;
    box-shadow: var(--shadow-sm);
}

.trust-avatars .av:first-child {
    margin-left: 0;
}

.trust-text {
    font-size: 0.8125rem;
    color: var(--ink-muted);
}

.trust-text strong {
    color: var(--ink-soft);
}


/* LOGIN FORM (Hero Right)*/
.hero-right {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 3rem 0 3rem 2rem;
    animation: fadeRight 0.8s cubic-bezier(0.16, 1, 0.3, 1) backwards;
}

.login-card {
    width: 100%;
    max-width: 400px;
    background: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;
    overflow: visible;
}

.login-card-bar {
    display: none;
}

.card-header {
    padding: 0 0 1.5rem 0;
    border-bottom: none;
    background: transparent;
}

.card-header-top {
    display: flex;
    align-items: center;
    gap: 0.875rem;
}

.card-icon {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
}

.card-header h2 {
    font-family: 'Fraunces', serif;
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--ink);
    letter-spacing: -0.02em;
}

.card-header p {
    font-size: 0.8125rem;
    color: var(--ink-muted);
    margin-top: 2px;
}

.card-body {
    padding: 0;
}

/* Form Fields */
.field {
    margin-bottom: 1.25rem;
}

.field label {
    display: block;
    font-size: 0.8125rem;
    font-weight: 550;
    color: var(--ink-soft);
    margin-bottom: 0.4375rem;
}

.input-wrap {
    position: relative;
}

.input-wrap .icon {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--ink-faint);
    font-size: 1.1rem;
    pointer-events: none;
    transition: color 0.2s;
}

.field input {
    width: 100%;
    padding: 0.75rem 1rem 0.75rem 2.5rem;
    background: var(--bg);
    border: 1.5px solid var(--rule);
    border-radius: var(--radius-sm);
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9375rem;
    color: var(--ink);
    transition: var(--transition);
    -webkit-appearance: none;
}

.field input::placeholder {
    color: var(--ink-faint);
}

.field input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    background: var(--white);
}

.input-wrap:focus-within .icon {
    color: var(--accent);
}

/* Password Toggle */
.toggle-pw {
    position: absolute;
    right: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--ink-faint);
    cursor: pointer;
    font-size: 1.1rem;
    transition: color 0.2s;
}

.toggle-pw:hover {
    color: var(--ink-soft);
}

/* Field Footer */
.field-footer {
    display: flex;
    justify-content: flex-end;
    margin-top: 0.375rem;
}

.forgot {
    font-size: 0.8125rem;
    color: var(--accent);
    text-decoration: none;
    font-weight: 500;
}

.forgot:hover {
    text-decoration: underline;
}

/* Login Button */
.btn-login {
    width: 100%;
    padding: 0.875rem;
    background: linear-gradient(135deg, var(--accent), var(--blue-dark));
    color: white;
    border: none;
    border-radius: var(--radius-sm);
    font-family: 'DM Sans', sans-serif;
    font-size: 0.9375rem;
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 0.5rem;
    box-shadow: var(--shadow-blue);
}

.btn-login:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 28px rgba(37, 99, 235, 0.32);
}

.btn-login:active {
    transform: translateY(0);
}


/* SUB-SYSTEMS SECTION*/
.subsystems {
    padding: 6rem 2rem;
    max-width: 1160px;
    margin: 0 auto;
}

/* Section Labels */
.section-label {
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--accent);
    margin-bottom: 0.875rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-label::before {
    content: '';
    width: 8px;
    height: 8px;
    background: var(--accent);
    border-radius: 50%;
    flex-shrink: 0;
}

.section-title {
    font-family: 'Fraunces', serif;
    font-size: 2.5rem;
    font-weight: 400;
    letter-spacing: -0.03em;
    color: var(--ink);
    line-height: 1.2;
    margin-bottom: 1rem;
}

.section-sub {
    font-size: 1.0625rem;
    color: var(--ink-muted);
    max-width: 560px;
    line-height: 1.7;
    font-weight: 350;
}

.section-head {
    margin-bottom: 3.5rem;
}

/* Sub-system Cards */
.subsystems-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.sub-card {
    background: var(--white);
    border: 1.5px solid var(--rule);
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: var(--transition);
}

.sub-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--blue-pale);
}

.sub-card-header {
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.5rem 2rem;
    cursor: pointer;
    user-select: none;
    background: var(--white);
    transition: background 0.2s;
}

.sub-card-header:hover {
    background: var(--bg-warm);
}

.sub-num {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    background: var(--blue-faint);
    border: 1px solid var(--blue-pale);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Fraunces', serif;
    font-size: 1rem;
    font-weight: 600;
    color: var(--accent);
}

.sub-icon {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--accent), var(--blue-dark));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 3px 10px rgba(37, 99, 235, 0.25);
}

.sub-info {
    flex: 1;
}

.sub-info h3 {
    font-size: 1.0625rem;
    font-weight: 600;
    color: var(--ink);
    letter-spacing: -0.01em;
}

.sub-info p {
    font-size: 0.875rem;
    color: var(--ink-muted);
    margin-top: 2px;
    font-weight: 350;
}

.sub-toggle {
    color: var(--ink-faint);
    font-size: 1.3rem;
    transition: transform 0.3s ease;
    flex-shrink: 0;
}

.sub-card.open .sub-toggle {
    transform: rotate(180deg);
}

/* Sub-system Body */
.sub-body {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    border-top: 0px solid var(--rule);
}

.sub-card.open .sub-body {
    max-height: 600px;
    border-top: 1px solid var(--rule);
}

.sub-body-inner {
    padding: 1.5rem 2rem 2rem;
}

/* Modules Grid */
.modules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
    gap: 1rem;
}

.module-item {
    background: var(--bg);
    border: 1px solid var(--rule);
    border-radius: var(--radius-md);
    padding: 1.125rem 1.25rem;
    transition: var(--transition);
}

.module-item:hover {
    background: var(--blue-faint);
    border-color: var(--blue-pale);
}

.module-item-num {
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--ink-faint);
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: 0.375rem;
}

.module-item h4 {
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--ink);
    margin-bottom: 0.5rem;
}

.module-item ul {
    list-style: none;
    padding: 0;
}

.module-item ul li {
    font-size: 0.8125rem;
    color: var(--ink-muted);
    line-height: 1.6;
    padding-left: 1rem;
    position: relative;
    font-weight: 350;
}

.module-item ul li::before {
    content: '—';
    position: absolute;
    left: 0;
    color: var(--ink-faint);
    font-size: 0.7rem;
    top: 2px;
}


/* STATS SECTION */
.stats-strip {
    background: var(--navy);
    padding: 5rem 2rem;
}

.stats-inner {
    max-width: 1160px;
    margin: 0 auto;
}

.stats-inner .section-label {
    color: rgba(147, 197, 253, 0.85);
}

.stats-inner .section-label::before {
    background: var(--blue-light);
}

.stats-inner .section-title {
    color: white;
}

.stats-inner .section-sub {
    color: rgba(255, 255, 255, 0.5);
}

.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1px;
    margin-top: 3rem;
    background: rgba(59, 130, 246, 0.2);
    border: 1px solid rgba(59, 130, 246, 0.2);
    border-radius: var(--radius-lg);
    overflow: hidden;
}

.stat-item {
    background: rgba(255, 255, 255, 0.04);
    padding: 2.25rem 2rem;
    text-align: center;
    transition: background 0.2s;
}

.stat-item:hover {
    background: rgba(59, 130, 246, 0.1);
}

.stat-num {
    font-family: 'Fraunces', serif;
    font-size: 2.75rem;
    font-weight: 300;
    color: white;
    letter-spacing: -0.04em;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.stat-num span {
    color: var(--blue-light);
}

.stat-lbl {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.45);
    font-weight: 400;
}


/* CTA SECTION */
.cta-section {
    padding: 6rem 2rem;
    max-width: 1160px;
    margin: 0 auto;
}

.cta-box {
    background: var(--blue-faint);
    border: 1.5px solid var(--blue-pale);
    border-radius: var(--radius-xl);
    padding: 4rem;
    display: grid;
    grid-template-columns: 1fr auto;
    align-items: center;
    gap: 3rem;
    position: relative;
    overflow: hidden;
}

.cta-box::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, var(--accent), var(--blue-light));
}

.cta-box::after {
    content: '';
    position: absolute;
    right: -60px;
    top: -60px;
    width: 240px;
    height: 240px;
    background: radial-gradient(circle, rgba(37, 99, 235, 0.08) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.cta-box h2 {
    font-family: 'Fraunces', serif;
    font-size: 2rem;
    font-weight: 400;
    color: var(--navy);
    letter-spacing: -0.03em;
    margin-bottom: 0.75rem;
}

.cta-box p {
    font-size: 1rem;
    color: var(--ink-soft);
    font-weight: 350;
    line-height: 1.7;
    max-width: 480px;
}

.cta-actions {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}

.cta-actions .btn-ghost {
    border-color: var(--blue-pale);
    color: var(--accent);
}

.cta-actions .btn-ghost:hover {
    background: white;
    border-color: var(--accent);
}


/* FOOTER */
footer {
    background: var(--navy);
    color: white;
    padding: 3.5rem 2rem 2.5rem;
}

.footer-inner {
    max-width: 1160px;
    margin: 0 auto;
}

.footer-top {
    display: grid;
    grid-template-columns: 1.75fr 1fr 1fr 1fr;
    gap: 3rem;
    padding-bottom: 2.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}

.footer-about .brand-name {
    color: white;
}

.footer-about .brand-tagline {
    color: rgba(255, 255, 255, 0.35);
}

.footer-about p {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.4);
    line-height: 1.7;
    margin-top: 0.875rem;
    font-weight: 350;
}

.footer-col h5 {
    font-size: 0.75rem;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.4);
    text-transform: uppercase;
    letter-spacing: 0.09em;
    margin-bottom: 1.125rem;
}

.footer-col ul {
    list-style: none;
}

.footer-col li {
    margin-bottom: 0.625rem;
}

.footer-col a {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.5);
    text-decoration: none;
    transition: color 0.2s;
    font-weight: 400;
}

.footer-col a:hover {
    color: white;
}

.footer-bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 2rem;
}

.footer-bottom p {
    font-size: 0.8125rem;
    color: rgba(255, 255, 255, 0.25);
}

.footer-dots {
    display: flex;
    gap: 6px;
}

.footer-dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.15);
}

.footer-dots span:first-child {
    background: var(--blue-light);
}


/* 10. ANIMATIONS */
@keyframes fadeUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeRight {
    from {
        opacity: 0;
        transform: translateX(30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}


/* RESPONSIVE — TABLET (max-width: 1024px) */
@media (max-width: 1024px) {
    .hero {
        grid-template-columns: 1fr;
        padding-top: calc(68px + 2rem);
        padding-bottom: 4rem;
    }

    .hero-left {
        padding: 0;
    }

    .hero-right {
        padding: 2rem 0 0;
    }

    .hero-title {
        font-size: 2.5rem;
    }

    .stats-row {
        grid-template-columns: repeat(2, 1fr);
    }

    .cta-box {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .cta-box p {
        max-width: 100%;
    }

    .cta-actions {
        flex-direction: row;
        justify-content: center;
    }

    .footer-top {
        grid-template-columns: 1fr 1fr;
    }
}


/* RESPONSIVE — MOBILE (max-width: 768px) */
@media (max-width: 768px) {
    .nav-links li:not(:last-child) {
        display: none;
    }

    .hero-title {
        font-size: 2rem;
    }

    .section-title {
        font-size: 1.875rem;
    }

    .stats-row {
        grid-template-columns: 1fr 1fr;
    }

    .footer-top {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .footer-bottom {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .cta-box {
        padding: 2.5rem;
    }

    .modules-grid {
        grid-template-columns: 1fr;
    }
}
    </style>
</head>
<body>

<!-- NAV -->
<nav class="topnav">
    <div class="nav-inner">
        <a class="brand" href="#">
            <img src="./images/logo.png" alt="SIEMS Logo" width="42" height="42" style="border-radius:10px; background:linear-gradient(135deg,#2563EB,#1E40AF); display:block; object-fit:contain;">
            <div>
                <div class="brand-name">SIEMS</div>
                <div class="brand-tagline">Student Information & Enrollment</div>
            </div>
        </a>
        <ul class="nav-links">
            <li><a href="#subsystems">Sub-systems</a></li>
            <li><a href="#about">About</a></li>
            <li><a href="#contact">Contact</a></li>
            <li><a href="#" class="nav-btn">Sign In</a></li>
        </ul>
    </div>
</nav>

<!-- HERO -->
<section class="hero" id="home">
    <div class="hero-left">
        <div class="hero-eyebrow"><span></span>Built for Educational Institutions</div>
        <h1 class="hero-title">
            Student Information<br>
            & <em>Enrollment</em><br>
            Management System.
        </h1>
        <p class="hero-desc">
            A unified platform to manage student records, enrollment, academic data, and institutional workflows — clean, fast, and reliable.
        </p>
        <div class="hero-actions">
            <a href="#subsystems" class="btn-solid">Explore Sub-systems <i class='bx bx-right-arrow-alt'></i></a>
            <a href="#" class="btn-ghost">Learn More</a>
        </div>
        <div class="hero-trust">
            <div class="trust-avatars">
                <div class="av">JL</div>
                <div class="av">MR</div>
                <div class="av">SC</div>
                <div class="av">+</div>
            </div>
            <div class="trust-text">Trusted by <strong>500+ institutions</strong> worldwide</div>
        </div>
    </div>
    <div class="hero-right">
        <div class="login-card">
            <div class="login-card-bar"></div>
            <div class="card-header">
                <div class="card-header-top">
                    <div class="card-icon">
                        <img src="./images/logo.png" alt="SIEMS Logo" width="44" height="44" style="border-radius:10px; background:linear-gradient(135deg,#2563EB,#1E40AF); display:block; object-fit:contain;">
                    </div>
                    <div>
                        <h2>Welcome back</h2>
                        <p>Sign in to your account</p>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form id="loginForm">
                    <div class="field">
                        <label for="username">Username or Email</label>
                        <div class="input-wrap">
                            <input type="text" id="username" name="username" placeholder="Enter your username or email" autocomplete="username" required>
                            <i class='bx bx-user icon'></i>
                        </div>
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <input type="password" id="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>
                            <i class='bx bx-lock icon'></i>
                            <i class='bx bx-show toggle-pw' id="togglePw" role="button" tabindex="0" aria-label="Toggle password visibility"></i>
                        </div>
                        <div class="field-footer">
                            <a href="#" class="forgot">Forgot password?</a>
                        </div>
                    </div>
                    <button type="submit" class="btn-login">
                        <i class='bx bx-log-in'></i> Sign In
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- SUB-SYSTEMS -->
<section class="subsystems" id="subsystems">
    <div class="section-head">
        <div class="section-label">System Architecture</div>
        <h2 class="section-title">10 Integrated Sub-systems.</h2>
        <p class="section-sub">Each sub-system is purpose-built for a specific institutional function, working together as one unified platform.</p>
    </div>
    <div class="subsystems-list">

        <!-- 1 -->
        <div class="sub-card">
            <div class="sub-card-header" onclick="toggleCard(this)">
                <div class="sub-num">01</div>
                <div class="sub-icon"><i class='bx bx-user-plus'></i></div>
                <div class="sub-info">
                    <h3>Student Registration Sub-system</h3>
                    <p>Manages the complete student admission and registration process.</p>
                </div>
                <i class='bx bx-chevron-down sub-toggle'></i>
            </div>
            <div class="sub-body">
                <div class="sub-body-inner">
                    <div class="modules-grid">
                        <div class="module-item">
                            <div class="module-item-num">Module 01</div>
                            <h4>New Student Admission</h4>
                            <ul><li>Collects personal, academic, and document data for new enrollees.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 02</div>
                            <h4>Returning / Transferee Processing</h4>
                            <ul><li>Handles re-enrollment and transfer student intake workflows.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 03</div>
                            <h4>Student Profile Management</h4>
                            <ul><li>Stores and updates all student information and contact details.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 04</div>
                            <h4>ID & Requirements Tracking</h4>
                            <ul><li>Monitors submission of required documents and ID issuance.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 05</div>
                            <h4>Enrollment Status Management</h4>
                            <ul><li>Tracks whether a student is officially enrolled, pending, or withdrawn.</li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 -->
        <div class="sub-card">
            <div class="sub-card-header" onclick="toggleCard(this)">
                <div class="sub-num">02</div>
                <div class="sub-icon"><i class='bx bx-check-shield'></i></div>
                <div class="sub-info">
                    <h3>Enrollment Management Sub-system</h3>
                    <p>Manages course enrollment, subject loading, and section assignment.</p>
                </div>
                <i class='bx bx-chevron-down sub-toggle'></i>
            </div>
            <div class="sub-body">
                <div class="sub-body-inner">
                    <div class="modules-grid">
                        <div class="module-item">
                            <div class="module-item-num">Module 01</div>
                            <h4>Subject / Course Enrollment</h4>
                            <ul><li>Students select and enroll in subjects per semester.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 02</div>
                            <h4>Section Assignment</h4>
                            <ul><li>Assigns students to appropriate class sections.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 03</div>
                            <h4>Enrollment Validation</h4>
                            <ul><li>Checks prerequisites and co-requisites before confirming enrollment.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 04</div>
                            <h4>Add / Drop Management</h4>
                            <ul><li>Allows modification of enrolled subjects within the adjustment period.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 05</div>
                            <h4>Enrollment Report Generation</h4>
                            <ul><li>Produces enrollment statistics per section, year, and program.</li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3 -->
        <div class="sub-card">
            <div class="sub-card-header" onclick="toggleCard(this)">
                <div class="sub-num">03</div>
                <div class="sub-icon"><i class='bx bx-folder-open'></i></div>
                <div class="sub-info">
                    <h3>Student Records Management Sub-system</h3>
                    <p>Centralizes all student academic and personal records.</p>
                </div>
                <i class='bx bx-chevron-down sub-toggle'></i>
            </div>
            <div class="sub-body">
                <div class="sub-body-inner">
                    <div class="modules-grid">
                        <div class="module-item">
                            <div class="module-item-num">Module 01</div>
                            <h4>Academic History Viewer</h4>
                            <ul><li>Displays complete subject history, grades, and units earned.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 02</div>
                            <h4>Curriculum Tracking</h4>
                            <ul><li>Maps student progress against required curriculum per program.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 03</div>
                            <h4>Document Repository</h4>
                            <ul><li>Stores uploaded credentials, certificates, and forms securely.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 04</div>
                            <h4>Record Integrity & Audit</h4>
                            <ul><li>Logs changes to records and enforces data integrity policies.</li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 -->
        <div class="sub-card">
            <div class="sub-card-header" onclick="toggleCard(this)">
                <div class="sub-num">04</div>
                <div class="sub-icon"><i class='bx bx-calendar'></i></div>
                <div class="sub-info">
                    <h3>Class Scheduling & Section Management Sub-system</h3>
                    <p>Organizes class sections, schedules, faculty assignments, and prevents conflicts.</p>
                </div>
                <i class='bx bx-chevron-down sub-toggle'></i>
            </div>
            <div class="sub-body">
                <div class="sub-body-inner">
                    <div class="modules-grid">
                        <div class="module-item">
                            <div class="module-item-num">Module 01</div>
                            <h4>Section Creation & Assignment</h4>
                            <ul><li>Creates class sections per subject.</li><li>Assigns year-level and category.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 02</div>
                            <h4>Class Timetable Generation</h4>
                            <ul><li>Generates the weekly schedule per class or instructor.</li><li>Ensures optimized distribution of time slots.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 03</div>
                            <h4>Room Assignment & Availability Checking</h4>
                            <ul><li>Assigns rooms to sections.</li><li>Tracks room availability.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 04</div>
                            <h4>Teacher Loading Management</h4>
                            <ul><li>Monitors faculty teaching loads to avoid overload.</li><li>Ensures fair distribution.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 05</div>
                            <h4>Schedule Conflict Detection</h4>
                            <ul><li>Automatically identifies overlapping schedules.</li><li>Prevents double-booking of rooms, teachers, and students.</li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5 -->
        <div class="sub-card">
            <div class="sub-card-header" onclick="toggleCard(this)">
                <div class="sub-num">05</div>
                <div class="sub-icon"><i class='bx bx-graduation'></i></div>
                <div class="sub-info">
                    <h3>Grades & Assessment Management Sub-system</h3>
                    <p>Handles grade encoding, verification, and storage for academic evaluation.</p>
                </div>
                <i class='bx bx-chevron-down sub-toggle'></i>
            </div>
            <div class="sub-body">
                <div class="sub-body-inner">
                    <div class="modules-grid">
                        <div class="module-item">
                            <div class="module-item-num">Module 01</div>
                            <h4>Grade Encoding</h4>
                            <ul><li>Faculty input grades for each enrolled student.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 02</div>
                            <h4>Grade Verification & Approval</h4>
                            <ul><li>Registrar reviews grades before final posting to ensure correctness.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 03</div>
                            <h4>Student Grade Viewer</h4>
                            <ul><li>Students can view their grades once approved.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 04</div>
                            <h4>Grade Correction / Request Handling</h4>
                            <ul><li>Allows faculty to file requests for grade changes.</li><li>Includes validation workflow.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 05</div>
                            <h4>Grade Reports & Summary</h4>
                            <ul><li>Generates class grade sheets, pass/fail summaries, and analytics.</li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 6 -->
        <div class="sub-card">
            <div class="sub-card-header" onclick="toggleCard(this)">
                <div class="sub-num">06</div>
                <div class="sub-icon"><i class='bx bx-money'></i></div>
                <div class="sub-info">
                    <h3>Payment & Accounting Sub-system</h3>
                    <p>Manages billing, payments, scholarships, and financial transactions.</p>
                </div>
                <i class='bx bx-chevron-down sub-toggle'></i>
            </div>
            <div class="sub-body">
                <div class="sub-body-inner">
                    <div class="modules-grid">
                        <div class="module-item">
                            <div class="module-item-num">Module 01</div>
                            <h4>Assessment of Fees</h4>
                            <ul><li>Calculates tuition and miscellaneous fees based on student load.</li><li>Generates billing statements.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 02</div>
                            <h4>Payment Posting & Validation</h4>
                            <ul><li>Records payments (cash, GCash, online banking).</li><li>Validates payment receipts.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 03</div>
                            <h4>Billing & Statement of Account</h4>
                            <ul><li>Provides updated SOAs for students.</li><li>Includes balance, adjustments, and penalties.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 04</div>
                            <h4>Scholarships / Discounts Processing</h4>
                            <ul><li>Applies discounts, vouchers, grants.</li><li>Ensures correct fee calculation.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 05</div>
                            <h4>Financial Transactions Log</h4>
                            <ul><li>Stores all payment history.</li><li>Useful for audit trails.</li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 7 -->
        <div class="sub-card">
            <div class="sub-card-header" onclick="toggleCard(this)">
                <div class="sub-num">07</div>
                <div class="sub-icon"><i class='bx bx-file'></i></div>
                <div class="sub-info">
                    <h3>Document & Credentials Sub-system</h3>
                    <p>Automates the processing of student credential requests and academic documents.</p>
                </div>
                <i class='bx bx-chevron-down sub-toggle'></i>
            </div>
            <div class="sub-body">
                <div class="sub-body-inner">
                    <div class="modules-grid">
                        <div class="module-item">
                            <div class="module-item-num">Module 01</div>
                            <h4>Document Request Module</h4>
                            <ul><li>Students request documents like TOR, Good Moral, Registration Form.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 02</div>
                            <h4>Document Processing Workflow</h4>
                            <ul><li>Admin verifies, prepares, and approves documents.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 03</div>
                            <h4>Document Generation (PDF/Printing)</h4>
                            <ul><li>Produces digital or printable versions.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 04</div>
                            <h4>Document Release Tracking</h4>
                            <ul><li>Logs completed and released documents.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 05</div>
                            <h4>Archived Records Management</h4>
                            <ul><li>Archives old credentials and secures them for future verification.</li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 8 -->
        <div class="sub-card">
            <div class="sub-card-header" onclick="toggleCard(this)">
                <div class="sub-num">08</div>
                <div class="sub-icon"><i class='bx bx-bar-chart-alt-2'></i></div>
                <div class="sub-info">
                    <h3>Reports & Analytics Sub-system</h3>
                    <p>Generates institutional reports and surfaces data-driven insights.</p>
                </div>
                <i class='bx bx-chevron-down sub-toggle'></i>
            </div>
            <div class="sub-body">
                <div class="sub-body-inner">
                    <div class="modules-grid">
                        <div class="module-item">
                            <div class="module-item-num">Module 01</div>
                            <h4>Enrollment Reports</h4>
                            <ul><li>Tracks headcount per program, year level, and section.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 02</div>
                            <h4>Academic Performance Reports</h4>
                            <ul><li>Analyzes grade distributions, pass/fail rates, and academic standings.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 03</div>
                            <h4>Financial Summary Reports</h4>
                            <ul><li>Aggregates collections, outstanding balances, and payment trends.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 04</div>
                            <h4>Custom Report Builder</h4>
                            <ul><li>Allows admin to generate ad-hoc reports with configurable filters.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 05</div>
                            <h4>Dashboard & Data Visualization</h4>
                            <ul><li>Real-time charts and KPIs for administrators and department heads.</li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 9 -->
        <div class="sub-card">
            <div class="sub-card-header" onclick="toggleCard(this)">
                <div class="sub-num">09</div>
                <div class="sub-icon"><i class='bx bx-plus-medical'></i></div>
                <div class="sub-info">
                    <h3>Clinic & Medical Services Sub-system</h3>
                    <p>Maintains student medical data, consultations, and medicine inventory.</p>
                </div>
                <i class='bx bx-chevron-down sub-toggle'></i>
            </div>
            <div class="sub-body">
                <div class="sub-body-inner">
                    <div class="modules-grid">
                        <div class="module-item">
                            <div class="module-item-num">Module 01</div>
                            <h4>Student Medical Records</h4>
                            <ul><li>Stores health history, medical notes, past illnesses.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 02</div>
                            <h4>Consultation & Treatment Logs</h4>
                            <ul><li>Logs each clinic visit and treatment given.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 03</div>
                            <h4>Medicine Inventory & Dispensing</h4>
                            <ul><li>Tracks medicines available and issued.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 04</div>
                            <h4>Medical Clearance Issuance</h4>
                            <ul><li>Generates clearance for enrollment or school activities.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 05</div>
                            <h4>Health Incident Reporting</h4>
                            <ul><li>Records accidents, injuries, and emergency responses.</li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 10 -->
        <div class="sub-card">
            <div class="sub-card-header" onclick="toggleCard(this)">
                <div class="sub-num">10</div>
                <div class="sub-icon"><i class='bx bx-lock-alt'></i></div>
                <div class="sub-info">
                    <h3>User Management Sub-system</h3>
                    <p>Controls access, security, and permissions for all system users.</p>
                </div>
                <i class='bx bx-chevron-down sub-toggle'></i>
            </div>
            <div class="sub-body">
                <div class="sub-body-inner">
                    <div class="modules-grid">
                        <div class="module-item">
                            <div class="module-item-num">Module 01</div>
                            <h4>User Account Creation</h4>
                            <ul><li>Admin creates accounts for staff, faculty, students.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 02</div>
                            <h4>Role & Permission Management</h4>
                            <ul><li>Assigns roles (admin, registrar, faculty) and access rights.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 03</div>
                            <h4>Authentication & Login Security</h4>
                            <ul><li>Implements login, password hashing, and session control.</li><li>Uses multi-factor authentication if needed.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 04</div>
                            <h4>User Activity Logs & Audit Trail</h4>
                            <ul><li>Tracks user actions for security and accountability.</li></ul>
                        </div>
                        <div class="module-item">
                            <div class="module-item-num">Module 05</div>
                            <h4>Password Reset & Account Recovery</h4>
                            <ul><li>Supports secure password reset workflows.</li></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<!-- STATS -->
<section class="stats-strip" id="about">
    <div class="stats-inner">
        <div class="section-head">
            <div class="section-label">By the numbers</div>
            <h2 class="section-title">Trusted at scale.</h2>
            <p class="section-sub">Thousands of institutions rely on SIEMS daily to keep their operations running smoothly.</p>
        </div>
        <div class="stats-row">
            <div class="stat-item">
                <div class="stat-num">100<span>K+</span></div>
                <div class="stat-lbl">Student Records</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">500<span>+</span></div>
                <div class="stat-lbl">Institutions</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">50<span>K+</span></div>
                <div class="stat-lbl">Enrollments</div>
            </div>
            <div class="stat-item">
                <div class="stat-num">99<span>.9%</span></div>
                <div class="stat-lbl">Uptime</div>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="cta-section" id="contact">
    <div class="cta-box">
        <div>
            <h2>Ready to get started?</h2>
            <p>Join hundreds of institutions already simplifying their student information management with SIEMS. Setup is quick — your team will be up and running in no time.</p>
        </div>
        <div class="cta-actions">
            <a href="#home" class="btn-solid">Get Started</a>
            <a href="#" class="btn-ghost">Contact Sales</a>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer>
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-about">
                <a class="brand" href="#">
                    <img src="./images/logo.png" alt="SIEMS Logo" width="42" height="42" style="border-radius:10px; background:linear-gradient(135deg,#2563EB,#1E40AF); display:block; object-fit:contain;">
                    <div>
                        <div class="brand-name">SIEMS</div>
                        <div class="brand-tagline">Student Information & Enrollment</div>
                    </div>
                </a>
                <p>A comprehensive student information and enrollment management system designed for modern educational institutions.</p>
            </div>
            <div class="footer-col">
                <h5>Product</h5>
                <ul>
                    <li><a href="#subsystems">Sub-systems</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">Demo</a></li>
                    <li><a href="#">Updates</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Company</h5>
                <ul>
                    <li><a href="#about">About Us</a></li>
                    <li><a href="#">Careers</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h5>Resources</h5>
                <ul>
                    <li><a href="#">Documentation</a></li>
                    <li><a href="#">Support</a></li>
                    <li><a href="#">Guides</a></li>
                    <li><a href="#">API</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 SIEMS · Student Information & Enrollment Integrated Management System. All rights reserved.</p>
            <div class="footer-dots">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>
</footer>

<script>
    function toggleCard(header) {
        const card = header.closest('.sub-card');
        const isOpen = card.classList.contains('open');
        // Close all
        document.querySelectorAll('.sub-card.open').forEach(c => c.classList.remove('open'));
        // Open clicked if it was closed
        if (!isOpen) card.classList.add('open');
    }

    const togglePw = document.getElementById('togglePw');
    const pwField  = document.getElementById('password');
    if (togglePw && pwField) {
        const toggle = () => {
            const isText = pwField.type === 'text';
            pwField.type = isText ? 'password' : 'text';
            togglePw.className = `bx bx-${isText ? 'show' : 'hide'} toggle-pw`;
        };
        togglePw.addEventListener('click', toggle);
        togglePw.addEventListener('keypress', e => { if (e.key === 'Enter') toggle(); });
    }

    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const t = document.querySelector(a.getAttribute('href'));
            if (t) { e.preventDefault(); t.scrollIntoView({ behavior:'smooth', block:'start' }); }
        });
    });

    document.querySelectorAll('.field input').forEach(inp => {
        inp.addEventListener('blur', function() {
            this.style.borderColor = this.value.trim() === '' ? '#ef4444' : '';
            this.style.boxShadow   = this.value.trim() === '' ? '0 0 0 3px rgba(239,68,68,0.1)' : '';
        });
        inp.addEventListener('input', function() {
            this.style.borderColor = '';
            this.style.boxShadow   = '';
        });
    });
</script>
</body>
</html>