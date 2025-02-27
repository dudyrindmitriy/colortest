<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerController extends Controller
{

    public function send($emailTo, $subject, $body)
    {
       
        // require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = 'smtp.mail.ru';             //  smtp host
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_ADDRESS');   //  sender username
            $mail->Password = env('MAIL_PASSWORD');       // sender password
            $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
            $mail->Port = 25;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom(env('MAIL_ADDRESS'), 'ColortestAdmin');
            $mail->addAddress($emailTo); 
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);
            if (!$mail->send()) {
                throw new Exception('Ошибка отправки: ' . $mail->ErrorInfo);
            }
            
        } catch (Exception $e) {
            throw new Exception('Ошибка отправки: ' . $mail->ErrorInfo);
        }
    }
}
