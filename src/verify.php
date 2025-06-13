<?php
require_once 'functions.php';

$email = $_GET['email'] ?? '';
$code = $_GET['code'] ?? '';

if ($email && $code) {
    if (verifySubscription($email, $code)) {
        echo "✅ Subscription verified successfully for $email.";
    } else {
        echo "❌ Verification failed. Invalid email or code.";
    }
} else {
    echo "❌ Missing email or code.";
}
?>
