<?php
session_start();
include_once 'includes/config.php';
$pageTitle = "Register Goods - Biosecurity System";
include_once 'includes/header.php';

$errors = [];
$success = false;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input fields
    $applicantName = trim($_POST['applicant_name'] ?? '');
    if (empty($applicantName)) {
        $errors[] = "Applicant name is required";
    }
    
    $email = trim($_POST['email'] ?? '');
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required";
    }
    
    $phone = trim($_POST['phone'] ?? '');
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    $goodsType = trim($_POST['goods_type'] ?? '');
    if (empty($goodsType)) {
        $errors[] = "Type of goods is required";
    }
    
    $goodsOrigin = trim($_POST['goods_origin'] ?? '');
    if (empty($goodsOrigin)) {
        $errors[] = "Country of origin is required";
    }
    
    $goodsDescription = trim($_POST['goods_description'] ?? '');
    if (empty($goodsDescription)) {
        $errors[] = "Description of goods is required";
    }
    
    // Handle file upload
    $documentPath = '';
    if (isset($_FILES['supporting_document']) && $_FILES['supporting_document']['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $fileType = $_FILES['supporting_document']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            $errors[] = "Invalid file type. Only PDF, JPEG, PNG, and DOC/DOCX files are allowed.";
        } else {
            $uploadDir = 'uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $fileName = time() . '_' . basename($_FILES['supporting_document']['name']);
            $documentPath = $uploadDir . $fileName;
            
            if (!move_uploaded_file($_FILES['supporting_document']['tmp_name'], $documentPath)) {
                $errors[] = "Failed to upload document. Please try again.";
                $documentPath = '';
            }
        }
    }
    
    // Process form if no errors
    if (empty($errors)) {
        $reference = 'BIO-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $status = 'pending';
        
        $stmt = $conn->prepare("INSERT INTO registrations (reference_number, applicant_name, email, phone, goods_type, goods_origin, goods_description, supporting_document, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("sssssssss", $reference, $applicantName, $email, $phone, $goodsType, $goodsOrigin, $goodsDescription, $documentPath, $status);
        
        if ($stmt->execute()) {
            $success = true;
            
            // Send confirmation email
            $to = $email;
            $subject = "Biosecurity Registration Confirmation - $reference";
            $message = "
            <html>
            <head>
                <title>Biosecurity Registration Confirmation</title>
            </head>
            <body>
                <h2>Thank you for your registration</h2>
                <p>Your biosecurity registration has been submitted successfully.</p>
                <p><strong>Reference Number:</strong> $reference</p>
                <p><strong>Status:</strong> Pending Review</p>
                <p>Our team will review your application and notify you of any updates.</p>
                <p>You can check the status of your application using your reference number.</p>
                <p>Regards,<br>Biosecurity Registration Team</p>
            </body>
            </html>
            ";
            
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: noreply@biosecurity.org" . "\r\n";
            
            mail($to, $subject, $message, $headers);
            
            // Redirect to success page
            $_SESSION['registration_reference'] = $reference;
            header("Location: registration_success.php");
            exit;
        } else {
            $errors[] = "Database error: " . $stmt->error;
        }
        
        $stmt->close();
    }
}
?>

    <style>
        div.container {
            text-align: center;
        }       

        ul.myUL {
            display: inline-block;
            text-align: left;
        }
        img  {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
</style>
<div class="container">
    <h2>How to Apply for this Import Form Permit</h2>

        <ul class="myUL"> 
        <li>This application is for an import Permit only for importing regulated
            commodities from different Countries</li>
        <li>Application form can be obtained at our Agriculture and Livestock Division,
            Biosecurity Section (MELAD), or you can APPLY ONLINE through this Biosecurity Official
            Websites.</li>
        <li>For more information you can directly send your email to these emails: 
        <li>I.kataebati@melad.gov.ki & K.ieretita@melad.gov.ki or call our office through
            this number 752 28108/ 752 28109 for further information.</li>
        </ul> 
</div>

<div class="container">
    <h2>IMPORTANT REMINDERS</h2>

        <ul class="myUL"> 
        <li>An import permit must first be obtained before importation of goods</li>
        <li>All import conditions as per import permit must be complied with
             Processing time for an import permit is 3 working days</li>
        <li>Upon arrival in Kiribati, Consignments will be inspected with
            the availability of all required document (originals only) and a copy of import permit
        <li>on-Compliance will results with results in the destruction or re-export of consignment
            at the importer's expense</li>
        </ul> 
</div>

<img src="image/Capture.PNG" alt="Flowchart" class="center">
<P></p>
<P></p>
<P></p>


<div class="container">
    <div class="page-header">
        <h1>Biosecurity Registration Form</h1>
        <p>Complete this form to register your goods for biosecurity clearance</p>
    </div>
    
    <?php if (!empty($errors)): ?>
        <div class="error-container">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="registration-form">
        <form action="register.php" method="post" enctype="multipart/form-data">
            <div class="form-section">
                <h3>Applicant Information</h3>
                
                <div class="form-group">
                    <label for="applicant_name">Full Name / Company Name <span class="required">*</span></label>
                    <input type="text" id="applicant_name" name="applicant_name" class="form-control" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address <span class="required">*</span></label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" class="form-control" required>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Goods Information</h3>
                
                <div class="form-group">
                    <label for="goods_type">Type of Goods <span class="required">*</span></label>
                    <select id="goods_type" name="goods_type" class="form-control" required>
                        <option value="">Select Type</option>
                        <option value="agricultural">Agricultural Products</option>
                        <option value="animals">Animals and Animal Products</option>
                        <option value="plants">Plants and Plant Materials</option>
                        <option value="biological">Biological Materials</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="goods_origin">Country of Origin <span class="required">*</span></label>
                    <input type="text" id="goods_origin" name="goods_origin" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="goods_description">Description of Goods <span class="required">*</span></label>
                    <textarea id="goods_description" name="goods_description" class="form-control" rows="4" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="supporting_document">Supporting Documents (Optional)</label>
                    <div class="file-upload">
                        <input type="file" id="supporting_document" name="supporting_document" class="file-input">
                        <div class="file-label">
                            <span>Choose a file</span>
                            <small>PDF, JPEG, PNG, or DOC files (max 5MB)</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-section">
                <div class="form-group consent-group">
                    <input type="checkbox" id="consent" name="consent" required>
                    <label for="consent">I confirm that all information provided is accurate and I consent to the processing of this data for biosecurity registration purposes.</label>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Submit Application</button>
                <button type="reset" class="btn btn-secondary">Reset Form</button>
            </div>
        </form>
    </div>
</div>

<?php include_once 'includes/footer.php'; ?>