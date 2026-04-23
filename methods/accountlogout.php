<?php
session_start();
// Clears all session data
$_SESSION = [];
// then ends the session
session_destroy();
// before redirecting back to the login page
header('Location: /login.php');
?>
