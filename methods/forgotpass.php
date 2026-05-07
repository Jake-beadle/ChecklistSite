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
// Gets the password associated with that email ready to be sent to the user (if no users are found with that email, an error message is returned instead)
$passwordQuery = "SELECT Password FROM logininfo WHERE Email = '$email'";
$passwordResult = mysqli_query($conn,$passwordQuery); 
$emailTest = mysqli_num_rows($passwordResult);
if ($emailTest = 0) { 
    echo "Error: username entered has already been used, try another";
    exit;
}   
else {
    $userQuery = "SELECT Username FROM logininfo WHERE Email = '$email'";
    $userResult = mysqli_query($conn,$userQuery); 
    $userFetch = mysqli_fetch_assoc($userResult);
    $username = $userFetch["Username"];
    $passwordFetch = mysqli_fetch_assoc($passwordResult);
    $password = $passwordFetch["Password"];
    // Using the PHPMailer template on their Github site
    $mail = new PHPMailer(true);
    
    try {
    //Server settings
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = '';                                     //Set the SMTP server to send through. Will need to be added by the user
    $mail->Port       = 25;                                    //TCP port to connect to; use
    $mail->SMTPOptions = array(                                //These settings prevent emails from being blocked due to certificates
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
        )
    );
    
    //Recipients
    $mail->setFrom('', 'Checklist Site');       // First string will need to be replaced by an email address that is being used by the server
    $mail->addAddress($email, $username);     //Add a recipient

    //Subject
    $mail->Subject = 'Account Recovery';
    $mail->Body    = "Hello ".$username.",\nYour password is ".$password.".\n If you need further assistance, please notify admins. Thanks";
    $mail->send();
    echo 'Email has been sent';
} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
} 
?>