<?php
require 'vendor/autoload.php'; // Убедитесь, что путь к autoload.php правильный

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Настройки сервера
    $mail->SMTPDebug = 2;
    $mail->isSMTP();
    $mail->Host = 'smtp.mail.ru';             //  smtp host
    $mail->SMTPAuth = true;
    $mail->Username = 'dvdudyrin@mail.ru';   //  sender username
    $mail->Password = 'g2LtAyJCG4tbPaNSu23g';       // sender password
    $mail->SMTPSecure = 'tls';                  // encryption - ssl/tls
    $mail->Port = 25;       
    $mail->CharSet = 'UTF-8';
    // Отправитель и получатель
    $mail->setFrom('dvdudyrin@mail.ru', 'Your Name');
    $mail->addAddress('evdudyrin@mail.ru'); // Получатель
    $mail->addCC('dvdudyrin@mail.ru'); // CC
    $mail->addBCC('dvdudyrin@mail.ru'); // BCC

    // Содержимое письма
    $mail->isHTML(true);
    $mail->Subject = "Тестовое письмо";
    $mail->Body    = "Это тестовое сообщение, отправленное с помощью PHPMailer.";
    $mail->AltBody = "Это тестовое сообщение, отправленное с помощью PHPMailer.";

    // Отправка
    if ($mail->send()) {
        echo 'Письмо отправлено успешно';
    } else {
        echo 'Ошибка при отправке письма: ' . $mail->ErrorInfo;
    }
} catch (Exception $e) {
    echo 'Ошибка: ' . $e->getMessage();
}