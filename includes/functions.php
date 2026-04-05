<?php
if (!defined('_HIEU')) {
    die('Truy cập không hợp lệ');
}

// Hàm cấu hình header, footer, title
function layout($layoutName, $data = [])
{
    if (file_exists(_PATH_URL_TEMPLATES . '/layout/' . $layoutName . '.php')) { // kiểm tra xem có tồn tại đường dẫn đó không
        require_once _PATH_URL_TEMPLATES . '/layout/' . $layoutName . '.php';
    }
};

//Hàm gửi email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendMail($emailTo, $subject, $content)
{

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'manu210924@gmail.com';                     //SMTP username
        $mail->Password   = 'wypwtdxwxjzclwjf';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('manu210924@gmail.com', 'Hieule course');
        $mail->addAddress($emailTo);     //Add a recipient


        //Content
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $content;

        $mail->SMTPOptions = array(
            'ssl' => [
                'verify_peer' => true,
                'verify_depth' => 3,
                'allow_self_signed' => true,
            ],
        );

        return $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

//Kiểm tra phương thức post 
function isPost()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    }
    return false;
}
//Kiểm tra phương thức get 
function isGet()
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    }
    return false;
}

// Lọc dữ liệu
function filterData($method = '')
{
    $filterArray = [];
    if (empty($method)) {
        if (isGet()) {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) { // filter ko phải dạng mảng
                        //FILTER_SANITIZE_SPECIAL_CHARS Loại bỏ kí tự đặc biệt
                        $filterArray[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArray[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS); // filter dạng mảng
                    }
                }
            }
        }
        if (isPost()) {
            if (!empty($_POST)) {
                foreach ($_POST as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArray[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArray[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS); // filter dạng mảng
                    }
                }
            }
        }
    } else {
        if ($method == 'GET') {
            if (!empty($_GET)) {
                foreach ($_GET as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) { // filter ko phải dạng mảng
                        //FILTER_SANITIZE_SPECIAL_CHARS Loại bỏ kí tự đặc biệt
                        $filterArray[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArray[$key] = filter_var($_GET[$key], FILTER_SANITIZE_SPECIAL_CHARS); // filter dạng mảng
                    }
                }
            }
        } else if ($method == 'POST') {
            if (!empty($_POST)) {
                foreach ($_POST as $key => $value) {
                    $key = strip_tags($key);
                    if (is_array($value)) {
                        $filterArray[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY);
                    } else {
                        $filterArray[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS); // filter dạng mảng
                    }
                }
            }
        }
    }
    return $filterArray;
}
