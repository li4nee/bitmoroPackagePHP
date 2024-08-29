
# Bitmoro Messaging Package

This PHP package provides a convenient way to integrate Bitmoro messaging services, including sending SMS, scheduling SMS, and handling OTP (One-Time Password) operations.

## Installation

To use this package, you'll need to include it in your project. If published on Packagist, you can install it via Composer:
``` bash
composer require bitmoro/bitmoro-package
```
Alternatively, you can clone the repository and include the files directly in your project.

## Usage
### Initial Setup
First, you need to include the package and initialize the main classes using your Bitmoro API token:
```
include 'index.php';

$token = 'your-api-token-here';

$messageSender = new MessageSender($token);
$messageScheduler = new MessageScheduler($token);
$otpHandler = new OtpHandler($token);

```
### Sending an SMS
You can send a simple SMS message as follows:
```
$message = "Hello, this is a test message.";
$number = "0123456789"; // Replace with a valid phone number
$senderId = "MD_Alert" ;

$result = $messageSender->sendSms($message, $number, $senderId);
echo $result ? "SMS sent successfully\n" : "Failed to send SMS\n";

```
### Scheduling an SMS
You can schedule an SMS to be sent at a future time:
```
$futureTime = date('Y-m-d H:i:s', strtotime('+1 minute')); // Set to one minute in the future
$messageScheduler->scheduleSms("Scheduled message", $number, $futureTime, $senderId);

```

### Sending an OTP
To send an OTP to a phone number:
```
$otpResult = $otpHandler->sendOtpMessage("0123456789", $senderId); // replace with real phone number
echo $otpResult ? "OTP sent successfully\n" : "Failed to send OTP\n";

```
### Verifying an OTP
To verify the OTP received:
```
$isOtpValid = $otpHandler->verifyOtp("0123456789", "9601903565"); // Replace with the actual OTP received
echo $isOtpValid ? "OTP is valid\n" : "OTP is invalid\n";

```
### Exception Handling
Each method call is wrapped in a try-catch block to handle exceptions gracefully:
```
try {
    // Your code here
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

```
### Requirements
- PHP 7.2 or higher
- cURL extension enabled in PHP
### License
This package is open-sourced software licensed under the MIT license.