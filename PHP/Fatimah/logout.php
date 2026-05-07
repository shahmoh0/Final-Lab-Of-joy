<?php
// Destroy session and send user to home
require_once '../includes/session.php';
logoutUser();
header('Location: /LabOfJoy/fatimah/home.php');
exit;
