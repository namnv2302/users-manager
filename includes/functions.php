<?php 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function layout($layoutName = 'header', $data = []) {
    if(file_exists(_WEB_PATH_TEMPLATE.'/layouts/'.$layoutName.'.php')) {
        require_once _WEB_PATH_TEMPLATE.'/layouts/'.$layoutName.'.php';
    }
}

function sendMail($to, $subject, $content) {
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                   
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'firstnam2002@gmail.com';                   
        $mail->Password   = 'svmqhwzptiuuugmk';                             
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            
        $mail->Port       = 465;                                    

        //Recipients
        $mail->setFrom('firstnam2002@gmail.com', 'GS.Nam');
        $mail->addAddress($to);

        //Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->SMTPOptions = array(
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ],
        );

        return $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function isPost() {
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    }
    return false;
}

function isGet() {
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    }
    return false;
}

function getBody() {
    $bodyArr = [];
    if(isGet()) {
        if(!empty($_GET)) {
            foreach($_GET as $key => $value) {
                $key = strip_tags($key);
                if(is_array($value)) {
                    $bodyArr[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                } else {
                    $bodyArr[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }
    if(isPost()) {
        if(!empty($_POST)) {
            foreach($_POST as $key => $value) {
                $key = strip_tags($key);
                if(is_array($value)) {
                    $bodyArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                } else {
                    $bodyArr[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
            }
        }
    }

    return $bodyArr;
}

function isEmail($email) {
    $check = filter_var($email, FILTER_VALIDATE_EMAIL);
    return $check;
}

function isNumberInt($number, $range = []) {
    if(!empty($range)) {
        $options = ['options' => $range];
        $check = filter_var($number, FILTER_VALIDATE_INT, $options);
    } else {
        $check = filter_var($number, FILTER_VALIDATE_INT);
    }
    return $check;
}

function isNumberFloat($number, $range = []) {
    if(!empty($range)) {
        $options = ['options' => $range];
        $check = filter_var($number, FILTER_VALIDATE_FLOAT, $options);
    } else {
        $check = filter_var($number, FILTER_VALIDATE_FLOAT);
    }
    return $check;
}

function checkPhone($phone) {
    if(!$phone[0] == '0') {
        return false;
    } else {
        $phone = substr($phone, 1);
        if(isNumberInt($phone) && strlen($phone) == 9) {
            return true;
        }
    }
}

function createNotifi($msg, $type = 'success') {
    if(!empty($msg)) {
        echo '<div class="alert alert-'.$type.' mt-3">'.$msg.'</div>';
    }
}

function redirect($path = 'index.php') {
    header("Location: $path");
    exit;
}

function displayInputValuePre($fieldName, $data) {
    return (!empty($data[$fieldName])) ? $data[$fieldName] : false;
}

function checkLogin() {
    $checkLogin = false;
    if(getSession('tokenLogin')) {
        $tokenLogin = getSession('tokenLogin');
    
        $queryData = firstRaw("SELECT userId FROM login_token WHERE token='$tokenLogin'");
        if(!empty($queryData)) {
            $checkLogin = true;
        } else {
            removeSession('tokenLogin');
        }
    }
    return $checkLogin;
}