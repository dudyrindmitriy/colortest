<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerController extends Controller
{

    public function composeEmail($emailTo, $subject, $body)
    {
       
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);

        try {
            $mail->SMTPDebug = 2;
            $mail->isSMTP();
            $mail->Host = 'smtp.mail.ru';             //  smtp host
            $mail->SMTPAuth = true;
            $mail->Username = 'dvdudyrintest@mail.ru';   //  sender username
            $mail->Password = 'YfSGJam48T14MDVe9pH0';       // sender password
            $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
            $mail->Port = 25;
            $mail->CharSet = 'UTF-8';
            // Отправитель и получатель
            $mail->setFrom('dvdudyrintest@mail.ru', 'ColortestAdmin');
            $mail->addAddress($emailTo); 
            // $mail->addAddress('dvdudyrin@mail.ru'); // Получатель
            // $mail->addCC('dvdudyrin@mail.ru'); // CC
            // $mail->addBCC('dvdudyrin@mail.ru'); // BCC

            // $mail->addAddress($request->emailTo); // Получатель
            // $mail->addCC($request->emailTo); // CC
            // $mail->addBCC($request->emailTo); // BCC

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            if ($mail->send()) {
                echo 'Письмо отправлено успешно';
            } else {
                echo 'Ошибка при отправке письма: ' . $mail->ErrorInfo;
            }
        } catch (Exception $e) {
            echo 'Ошибка: ' . $e->getMessage();
        }
    }
}
