<?php

class MessageApiDto {
    public $number;
    public $message;
    public $senderId;
    public $timer;

    public function __construct($number = null, $message = null, $senderId = null, $timer = null) {
        $this->number = $number;
        $this->message = $message;
        $this->senderId = $senderId;
        $this->timer = $timer;
    }
}

class MessageHandler {
    private $token;

    public function __construct($token) {
        $this->token = $token;
    }

    public function sendMessage($options) {
        $url = 'https://api.bitmoro.com/message/api';
        $data = json_encode($options);
        $headers = [
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/json",
            "Content-Length: " . strlen($data),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        echo($response);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($statusCode >= 400) {
            throw new Exception("Error: {$response}");
        }
        return true;
    }
}

class MessageSenderError extends Exception {}

class MessageSender {
    private $sms;

    public function __construct($token) {
        $this->sms = new MessageHandler($token);
    }

    public function sendSms($message, $number, $senderId = null) {
        $sendBody = new MessageApiDto($number, $message, $senderId);
        try {
            $this->sms->sendMessage($sendBody);
            return true;
        } catch (Exception $e) {
            throw new MessageSenderError($e->getMessage());
        }
    }
}

class MessageScheduler {
    private $sms;

    public function __construct($token) {
        $this->sms = new MessageHandler($token);
    }

    public function scheduleSms($message, $number, $timer, $senderId = null) {
        $sendBody = new MessageApiDto($number, $message, $senderId, $timer);

        $timeDifference = strtotime($timer) - time();

        if ($timeDifference < 0) {
            throw new Exception("Scheduled time must be in the future.");
        }

        sleep($timeDifference);

        try {
            $this->sms->sendMessage($sendBody);
            echo "Message sent successfully at the scheduled time.\n";
        } catch (Exception $e) {
            echo "Failed to send the scheduled message: {$e->getMessage()}\n";
        }
    }
}

class OtpHandler {
    private $token;
    public static $validOtp = [];
    private $sms;
    public static $exp = 180000;
    private $otpLength;

    public function __construct($token, $exp = 180000, $otpLength = 10) {
        self::$exp = $exp;
        $this->sms = new MessageHandler($token);
        $this->token = $token;
        $this->otpLength = $otpLength;
    }

    public function sendOtpMessage($number, $senderId = null) {
        $otp = $this->generateOtp($this->otpLength);
        $message = "Your OTP code is {$otp}";
    
        $sendBody = new MessageApiDto([$number], $message, $senderId);
    
        try {
            $this->sms->sendMessage($sendBody);
            $this->registerOtp($number, $otp);
            return true;
        } catch (Exception $e) {
            throw new MessageSenderError($e->getMessage());
        }
    }
    

    private function registerOtp($number, $otp) {
        if (isset(self::$validOtp[$number])) {
            $existingOtp = self::$validOtp[$number];
            $timeLeft = strtotime($existingOtp['time']) + self::$exp - time();
            throw new Exception("You can only request OTP after {$timeLeft} second(s)");
        }

        $otpBody = [
            'otp' => $otp,
            'time' => date('c'),
        ];

        self::$validOtp[$number] = $otpBody;
        self::clearOtp($number);
        return $otp;
    }

    public static function clearOtp($number) {
        $delay = self::$exp / 1000; // seconds
        sleep($delay);
        if (isset(self::$validOtp[$number])) {
            unset(self::$validOtp[$number]);
        }
    }

    private function generateOtp($length) {
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= rand(0, 9);
        }
        return $otp;
    }

    public function verifyOtp($number, $otp) {
        if (!isset(self::$validOtp[$number])) {
            throw new Exception("No OTP found for number {$number}");
        }
        return self::$validOtp[$number]['otp'] === $otp;
    }
}
?>
