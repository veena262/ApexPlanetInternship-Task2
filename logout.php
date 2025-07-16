<?php
session_start();
session_unset(); // Clear session variables
session_destroy(); // Destroy session completely

// Optional: Redirect with a message
header("Location: login.php");
exit();
