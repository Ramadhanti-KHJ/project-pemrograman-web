<?php
session_start();
$masuk = isset($_SESSION['login']) && $_SESSION['login'] === true;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body class="bg-white text-dark">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-2">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary d-flex align-items-center gap-2">
                <span>HR Helpdesk & WBS</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLanding">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarLanding">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#beranda">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#layanan">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#alur">Complaint Process</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-medium" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <?php if ($masuk): ?>
                            <a href="dashboard.php" class="btn btn-outline-primary px-3">Dashboard</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-primary px-3">Login / Register</a>
                        <?php endif; ?>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section id="beranda" class="py-5 border-bottom">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <h1 class="display-5 fw-bold lh-sm mb-3">Employee HR Helpdesk & Whistleblowing System</h1>
                    <p class="text-muted mb-4">
                        Convey aspirations, consult with HR administration, complain about work facilities, and report indications of violations safely and reliably. The confidentiality of the reporter's identity is fully protected.
                    </p>
                    <div>
                        <a href="aduan.php" class="btn btn-primary btn-lg px-4 d-inline-flex align-items-center gap-2 shadow-sm">
                            <span>Make a Complaint</span>
                        </a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="bg-light rounded-4 p-4 p-md-5 shadow-sm">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div>
                                <h6 class="fw-bold mb-0">Guaranteed Confidentiality</h6>
                                <small class="text-muted">Anonymous reporting options for sensitive categories</small>
                            </div>
                        </div>
                        <div class="bg-white rounded-3 p-3 shadow-sm mb-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-danger-subtle text-danger">Whistleblowing • Very Urgent</span>
                                <small class="text-muted">Anonymous</small>
                            </div>
                            <p class="small text-secondary mb-0">Sender's identity and division are automatically hidden from the reporting system.</p>
                        </div>
                        <div class="bg-white rounded-3 p-3 shadow-sm border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-success-subtle text-success">HR Helpdesk • Resolved</span>
                                <small class="text-muted">Official HR Response</small>
                            </div>
                            <p class="small text-secondary mb-0">Monitor handling status and official HR responses directly from your account.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="py-5 bg-light border-top border-bottom">
        <div class="container py-3">
            <h3 class="fw-bold mb-2">Services</h3>
            <p class="text-muted mb-4">Choose the service category that matches your needs or situation.</p>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-2">
                        <div class="card-body">
                            <h5 class="fw-bold">HR Helpdesk</h5>
                            <p class="text-muted small mb-0">Consultation regarding salary, leave, BPJS, company policies, and employee administration.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-2">
                        <div class="card-body">
                            <h5 class="fw-bold">Suggestions & Complaints</h5>
                            <p class="text-muted small mb-0">Provide constructive feedback or complaints regarding facilities and workplace comfort.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 p-2">
                        <div class="card-body">
                            <h5 class="fw-bold">Whistleblowing System</h5>
                            <p class="text-muted small mb-0"> Report indications of fraud, abuse of authority, or ethical violations anonymously.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="alur" class="py-5 bg-light border-top border-bottom">
        <div class="container py-3">
            <h3 class="fw-bold mb-2">Complaint Process</h3>
            <p class="text-muted mb-4">Three simple steps to submit a report and monitor its follow-up.</p>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="border rounded-4 p-4 h-100">
                        <span class="badge bg-primary rounded-pill mb-3">Step 1</span>
                        <h6 class="fw-bold">Login Employee Account</h6>
                        <p class="text-muted small mb-0">Log in using your registered employee account to access the service form.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-4 p-4 h-100">
                        <span class="badge bg-primary rounded-pill mb-3">Step 2</span>
                        <h6 class="fw-bold">Fill Out Form & Attach Evidence</h6>
                        <p class="text-muted small mb-0">Determine the category, priority level, details of the incident, attachments of photos/videos/audio, and the option for anonymity.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-4 p-4 h-100">
                        <span class="badge bg-primary rounded-pill mb-3">Step 3</span>
                        <h6 class="fw-bold">Monitor HR Response</h6>
                        <p class="text-muted small mb-0">Check the progress of your ticket and read official responses from HR through the HR Response menu.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="faq" class="py-5 bg-light border-top">
        <div class="container py-3" style="max-width: 800px;">
            <h3 class="fw-bold mb-2">Frequently Asked Questions (FAQ)</h3>
            <p class="text-muted mb-4">Brief information about using the HR Helpdesk & WBS portal.</p>
            <div class="accordion shadow-sm" id="accordionFaq">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">Is my identity safe if I enable the Anonymous option?</button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#accordionFaq">
                        <div class="accordion-body text-muted small">
                            Yes. When the <strong>Submit as Anonymous</strong> option is checked, the system marks the report as confidential and hides your username and department information on the report display.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">What file formats can be attached as evidence?</button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                        <div class="accordion-body text-muted small">
                            You can attach supporting evidence in the form of image files (JPG, PNG), videos (MP4), and audio recordings (MP3, WAV) directly on the complaint form.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Can I change or cancel a complaint that has already been submitted?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#accordionFaq">
                        <div class="accordion-body text-muted small">
                            Yes. As long as the complaint status is still <strong>Pending</strong> (not processed or replied to by HR), you can still edit the details or delete the submission on the HR Response page.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-4 border-top text-center small">
        <div class="container">
            &copy; 2026 HR Helpdesk & WBS &mdash; Internal Employee Consultation & Whistleblowing Portal
        </div>
    </footer>
</body>
</html>