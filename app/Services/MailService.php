<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class MailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->setupMailer();
    }

    private function setupMailer()
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host = env('MAIL_HOST', 'smtp.mail.ru');
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = env('MAIL_ADDRESS');
            $this->mailer->Password = env('MAIL_PASSWORD');
            $this->mailer->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
            $this->mailer->Port = env('MAIL_PORT', 25);
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->setFrom(env('MAIL_ADDRESS'), env('MAIL_FROM_NAME', 'ColortestAdmin'));
        } catch (Exception $e) {
            Log::error('Ошибка настройки PHPMailer: ' . $e->getMessage());
        }
    }

    /**
     * Отправка письма одному получателю
     */
    public function send(string $to, string $subject, string $body, bool $isHtml = true)
    {
        $result['success'] = false;
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);

            $this->mailer->isHTML($isHtml);
            $this->mailer->Subject = $subject;
            $this->mailer->Body = $body;
            $this->mailer->AltBody = $isHtml ? strip_tags($body) : $body;

            $this->mailer->send();
            $result['success'] = true;
            $result['message'] = "Письмо успешно отправлено на адрес: {$to}";
            Log::info("Письмо успешно отправлено на адрес: {$to}");
        } catch (Exception $e) {
            Log::error("Ошибка отправки письма на {$to}: " . $e->getMessage());
            if (strpos($e, 'non-local recipient verification failed') !== false) {
                $result['message'] = "Email {$to} не существует или недоступен, не удалось отправить письмо";
            }
        }
        return $result;
    }

    /**
     * Отправка письма нескольким получателям
     */
    public function sendToMany(array $recipients, string $subject, string $body, bool $isHtml = true): array
    {
        $results = [];

        foreach ($recipients as $recipient) {
            $results[$recipient] = $this->send($recipient, $subject, $body, $isHtml);
        }

        return $results;
    }

    /**
     * Отправка уведомления о прохождении теста
     */
    public function sendTestCompletedNotification(string $userEmail, string $userName, string $testName)
    {
        $subject = "Подтверждение прохождения теста";
        $body = $this->getTestCompletedUserBody($userName, $testName);

        return $this->send($userEmail, $subject, $body);
    }

    /**
     * Отправка уведомления администраторам о прохождении теста
     */
    public function sendTestCompletedToAdmins(string $userName, string $userEmail, string $testName, array $adminEmails): array
    {
        $subject = "Пользователь прошел тестирование";
        $body = $this->getTestCompletedAdminBody($userName, $userEmail, $testName);

        return $this->sendToMany($adminEmails, $subject, $body);
    }

    /**
     * Текст письма для пользователя
     */
    private function getTestCompletedUserBody(string $userName, string $testName): string
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4a6fa5; color: white; padding: 10px 20px; border-radius: 5px 5px 0 0; }
                .content { background-color: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
                .footer { margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Подтверждение прохождения теста</h2>
                </div>
                <div class='content'>
                    <p>Здравствуйте, <strong>{$userName}</strong>!</p>
                    <p>Вы успешно прошли тестирование: <strong>{$testName}</strong>.</p>
                    <p>Результаты сохранены в системе. Вы можете просмотреть их в своем личном кабинете.</p>
                    <p>Спасибо за участие!</p>
                </div>
                <div class='footer'>
                    <p>Это автоматическое сообщение, пожалуйста, не отвечайте на него.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    /**
     * Текст письма для администратора
     */
    private function getTestCompletedAdminBody(string $userName, string $userEmail, string $testName): string
    {
        return "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #4a6fa5; color: white; padding: 10px 20px; border-radius: 5px 5px 0 0; }
                .content { background-color: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
                .info { background-color: #e8f4f8; padding: 10px; border-radius: 5px; margin: 10px 0; }
                .footer { margin-top: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Уведомление о прохождении теста</h2>
                </div>
                <div class='content'>
                    <p>Пользователь прошел тестирование:</p>

                    <div class='info'>
                        <p><strong>Имя пользователя:</strong> {$userName}</p>
                        <p><strong>Email пользователя:</strong> {$userEmail}</p>
                        <p><strong>Название теста:</strong> {$testName}</p>
                        <p><strong>Дата прохождения:</strong> " . now()->format('d.m.Y H:i') . "</p>
                    </div>

                    <p>Результаты сохранены в системе.</p>
                </div>
                <div class='footer'>
                    <p>Это автоматическое сообщение, пожалуйста, не отвечайте на него.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
}
