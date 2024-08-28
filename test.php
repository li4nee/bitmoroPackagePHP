<?php

include 'index.php';

$token = '6e385330ebef39c4c0878c6bf1c6a2e585a4a257b3d4fa65426040c9a28c';

// Initialize the classes
$messageSender = new MessageSender($token);
$messageScheduler = new MessageScheduler($token);
$otpHandler = new OtpHandler($token);

try {
    // Test sending an SMS
    $message = "Hello, this is a test message.";
    $number = ["9869363132"]; // Replace with a valid phone number
    $senderId = "MD_Alert";
    // $result = $messageSender->sendSms($message, $number);
    // echo $result ? "SMS sent successfully\n" : "Failed to send SMS\n";

    // // Test scheduling an SMS
    // $futureTime = date('Y-m-d H:i:s', strtotime('+1 minute')); // Set to one minute in the future
    // $messageScheduler->scheduleSms("Scheduled message", $number, $futureTime, $senderId);

    // // Test sending an OTP
    // $otpResult = $otpHandler->sendOtpMessage("9869363132");
    // echo $otpResult ? "OTP sent successfully\n" : "Failed to send OTP\n";

    // // Verify the OTP (replace '1234' with the actual OTP received)
    $isOtpValid = $otpHandler->verifyOtp("9869363132", "0051741905");
    echo $isOtpValid ? "OTP is valid\n" : "OTP is invalid\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
