<?php
require_once 'functions.php';

if (isset($_GET['email'])) {
    $email = $_GET['email'];
    unsubscribeEmail($email);
    echo "<p>You have been unsubscribed from task reminders.</p>";
} else {
    echo "<p>Invalid unsubscribe request.</p>";
}
