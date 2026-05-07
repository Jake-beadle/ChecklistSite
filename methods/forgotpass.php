<?php
// Imports files needed for emailing through PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require './mail/Exception.php';
require './mail/PHPMailer.php';
require './mail/SMTP.php';
// Creates a connection to the database to be used later
$conn = mysqli_connect("", "", "", "");
// Gets the email from the posted form
if(isset($_POST["email"]))
{
    $email = $_POST["email"];
}   
// Gets the userid associated with that email (if no users are found with that email, an error message is returned instead)
$idQuery = "SELECT UserID FROM logininfo WHERE Email = '$email'";
$idResult = mysqli_query($conn,$idQuery); 
$emailTest = mysqli_num_rows($idResult);
if ($emailTest = 0) { 
    echo "Error: no users were found with that email, try again";
    exit;
}   
else {
    $idFetch = mysqli_fetch_assoc($idResult);
    $userID = $idFetch["UserID"];
    // Gets the username so that it can be used in the email
    $userQuery = "SELECT Username FROM logininfo WHERE UserID = '$userID'";
    $userResult = mysqli_query($conn,$userQuery); 
    $userFetch = mysqli_fetch_assoc($userResult);
    $username = $userFetch["Username"];
    // Generates a random link/token which can be used by the user to reset their password
    $passResetLink = "http://localhost:81/resetpass.php?token=";
    $token = bin2hex(random_bytes(32));
    $expiry = time() + 900;
    $passResetLink .= $token;
    $passResetQuery = "INSERT INTO resettokens (UserID, Token, ExpiryTime) VALUES('$userID','$token','$expiry')";
    if(!$result = mysqli_query($conn, $passResetQuery))
    {
        echo "Error when attempting to create password reset link: ".mysqli_error($conn);
        exit;
    }
    // Using the PHPMailer template on their Github site for this next part
    $mail = new PHPMailer(true);
    
    try {
    //Server settings. SMTP server needs to be added as the "host"
    $mail->isSMTP();                                            
    $mail->Host       = '';                     
    $mail->Port       = 25;                                    
    //These settings prevent emails from being blocked due to certificates
    $mail->SMTPOptions = array(                                
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
        )
    );

    //Recipients. Email needs to be set in the empty string below
    $mail->setFrom('', 'Checklist Site automated response');
    $mail->addAddress($email, $username);

    //Subject
    $mail->isHTML(true);
    $mail->Subject = 'Account Recovery - password reset';
    $mail->Body    = "Hello ".$username.",<br>A password reset was requested for this email. You can reset your password using this link, which expires in 15 minutes: <a href=".$passResetLink.">".$passResetLink."</a>.<br>If you didn't request this, please ignore this email, and for any further assistance, please notify admins";
    $mail->send();
    echo 'Email has been sent';
    } catch (Exception $e) {
        echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
} 
?>
?>