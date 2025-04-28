<?php
session_start();
include_once 'includes/config.php';
$pageTitle = "Biosecurity Registration System";
include_once 'includes/header.php';
?>

<div class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1>Welcome to ALD Biosecurity Registration Portal</h1>
            <p>Register your goods for biosecurity clearance and compliance</p>
            <div class="button-group">
                <a href="register.php" class="btn btn-primary">Submit New Application</a>
                <a href="status.php" class="btn btn-secondary">Check Application Status</a>
            </div>
        </div>
    </div>
</div>

</head>
<body>




<div class="info-section">
    <div class="container">
        <div class="info-grid">
            <div class="info-card">
                <div class="icon-wrapper">
                    <i class="icon-register"></i>
                </div>
                <h3>Register</a></h3>
                <p>Complete the registration form with details about your goods for biosecurity assessment.</p>
            </div>
            <div class="info-card">
                <div class="icon-wrapper">
                    <i class="icon-review"></i>
                </div>
                <h3>Review</h3>
                <p>Our team will review your application and assess compliance with biosecurity standards.</p>
            </div>
            <div class="info-card">
                <div class="icon-wrapper">
                    <i class="icon-approve"></i>
                </div>
                <h3>Approval</h3>
                <p>Receive notification of approval status and next steps for your goods.</p>
            </div>
        </div>
    </div>
</div>

<div class="faq-section">
    <div class="container">
        <h2>Frequently Asked Questions</h2>
        <div class="faq-container">
            <div class="faq-item">
                <div class="faq-question">What goods require biosecurity registration?</div>
                <div class="faq-answer">All agricultural products, plants, animals, and biological materials must be registered for biosecurity clearance before importation or transportation.</div>
            </div>
            <div class="faq-item">
                <div class="faq-question">How long does the registration process take?</div>
                <div class="faq-answer">Standard applications are typically processed within 5-7 business days. Complex cases may require additional time for thorough assessment.</div>
            </div>
            <div class="faq-item">
                <div class="faq-question">What documents do I need to prepare?</div>
                <div class="faq-answer">You will need to provide proof of ownership, detailed descriptions of goods, origin certificates, and any relevant treatment documentation.</div>
            </div>
        </div>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>