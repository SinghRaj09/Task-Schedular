<?php
require_once 'functions.php';

if (isset($_GET['email'])) {
    $email = urldecode($_GET['email']);
    unsubscribeEmail($email);
    echo "<p>You have been unsubscribed successfully: <strong>$email</strong></p>";
} else {
    echo "<p>Invalid request.</p>";
}
