<?php

namespace App\Http\Controllers;

use App\Models\Chess;
use App\Models\Isa;
use App\Models\Messages;
use App\Models\Newsletter;
use App\Models\NewsletterTopic;
use App\Models\PackagePurchase;
use App\Models\Results;
use App\Models\User;
use App\Models\Review;
use App\Models\ServicePackage;
use App\Services\AnalysisService;
use App\Services\MailService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.index');
    }

    // public function indexUsers()
    // {
    //     $users = User::all();
    //     return view('admin.users.index', compact('users'));
    // }

    // public function editUser(User $user)
    // {
    //     return view('admin.users.edit', compact('user'));
    // }

    // public function updateUser(Request $request, User $user)
    // {
    //     $request->validate([
    //         'login' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255',

    //     ]);

    //     $user->login = $request->login;
    //     $user->email = $request->email;

    //     $user->save();
    //     return redirect()->route('admin.users.index')->with('success', 'Пользователь успешно обновлён!');
    // }

    // public function destroyUser(User $user)
    // {
    //     $user->delete();
    //     return redirect()->route('admin.users.index')->with('success', 'Пользователь успешно удалён!');
    // }


    public function indexResults()
    {
        $results = Results::with('user')->get();
        return view('admin.results.index', compact('results'));
    }

    // public function editResult(Results $result)
    // {
    //     $users = User::all();

    //     return view('admin.results.edit', compact('result', 'users'));
    // }

    // public function updateResult(Request $request, Results $result)
    // {

    //     $result->update($request->only('user_id'));


    //     return redirect()->route('admin.results.index')->with('success', 'Результат успешно обновлён!');
    // }

    // public function destroyResult(Results $result)
    // {
    //     $result->delete();
    //     return redirect()->route('admin.results.index')->with('success', 'Результат успешно удалён!');
    // }

    public function purchases()
    {
        $purchases = PackagePurchase::with(['user', 'package'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.purchases.index', compact('purchases'));
    }

    public function verifyPurshase(PackagePurchase $purchase)
    {
        $purchase->update([
            'payment_status' => 'paid',
            'admin_verified_at' => now(),
            'admin_verified_by' => Auth::id(),
            'paid_at' => now()
        ]);
        $this->sendPaymentNotifications($purchase);
        $userId = $purchase->user_id;
        $package = $purchase->package;

        // Логика автоматического приобретения пакетов
        if ($package->code == 'pro') {
            // При покупке Pro автоматически добавляем Basic и Standard
            $basicPackage = ServicePackage::where('code', 'basic')->first();
            $standardPackage = ServicePackage::where('code', 'standard')->first();

            if ($basicPackage && !PackagePurchase::hasPackage($userId, 'basic')) {
                PackagePurchase::create([
                    'user_id' => $userId,
                    'amount' => 0,
                    'service_package_id' => $basicPackage->id,
                    'payment_status' => 'paid',
                    'paid_at' => now()
                ]);
            }

            if ($standardPackage && !PackagePurchase::hasPackage($userId, 'standard')) {
                PackagePurchase::create([
                    'user_id' => $userId,
                    'amount' => 0,
                    'service_package_id' => $standardPackage->id,
                    'payment_status' => 'paid',
                    'paid_at' => now()
                ]);
            }
        } elseif ($package->code == 'standard') {
            // При покупке Standard автоматически добавляем Basic
            $basicPackage = ServicePackage::where('code', 'basic')->first();

            if ($basicPackage && !PackagePurchase::hasPackage($userId, 'basic')) {
                PackagePurchase::create([
                    'user_id' => $userId,
                    'amount' => 0,
                    'service_package_id' => $basicPackage->id,
                    'payment_status' => 'paid',
                    'paid_at' => now()
                ]);
            }
        }

        return redirect()->back()->with('success', 'Платеж подтвержден');
    }


    public function downloadUserPdf($userId)
    {
        $user = User::findOrFail($userId);
        $analysisService = new AnalysisService();
        return $analysisService->streamAnalysisPdf($user);
    }

    public function downloadUserDoc($userId)
    {
        $user = User::findOrFail($userId);
        $analysisService = new AnalysisService();
        return $analysisService->downloadAnalysisDoc($user);
    }

    private function sendPaymentNotifications(PackagePurchase $purchase)
    {
        try {
            $user = User::find($purchase->user_id);
            $mailService = new MailService();

            // Получаем email'ы администраторов
            $adminEmails = User::where('isAdmin', '1')
                ->whereNotNull('email')
                ->pluck('email')
                ->toArray();
            $attachments = [];
            try {
                // Генерируем PDF
                $pdfResponse = $this->downloadUserPdf($user->id);
                $pdfContent = $pdfResponse->getContent();
                $pdfPath = storage_path('app/temp/report_' . $user->id . '.pdf');
                file_put_contents($pdfPath, $pdfContent);

                // Генерируем DOC
                $doc = $this->downloadUserDoc($user->id);
                if ($doc['saved'] && file_exists($doc['path'])) {
                    $docPath = $doc['path'];
                    $attachments = [
                        ['path' => $pdfPath, 'name' => 'report.pdf'],
                        ['path' => $docPath, 'name' => 'report.docx']
                    ];
                } else {
                    // Если DOC не создался, прикрепляем только PDF
                    $attachments = [
                        ['path' => $pdfPath, 'name' => 'report.pdf']
                    ];
                    Log::warning('DOC файл не создался для пользователя ' . $user->id);
                }
            } catch (Exception $e) {
                Log::warning('Не удалось сгенерировать отчеты: ' . $e->getMessage());
            }
            // Отправляем уведомление пользователю
            if (!empty($user->email)) {
                $userResult = $mailService->send(
                    $user->email,
                    'Платеж подтвержден',
                    $this->getUserPaymentBody($user, $purchase, $adminEmails),
                    true,
                    $attachments
                );

                if (!$userResult['success']) {
                    Log::warning('Не удалось отправить уведомление пользователю: ' . $userResult['message']);
                }
            }

            // Отправляем уведомления администраторам
            if (!empty($adminEmails)) {
                foreach ($adminEmails as $adminEmail) {
                    $adminResult = $mailService->send(
                        $adminEmail,
                        'Платеж подтвержден администратором',
                        $this->getAdminPaymentBody($user, $purchase, Auth::user()->login),
                        true,
                        $attachments
                    );

                    if (!$adminResult['success']) {
                        Log::warning("Не удалось отправить уведомление админу {$adminEmail}: {$adminResult['message']}");
                    }
                }
            }
            foreach ($attachments as $attachment) {
                if (file_exists($attachment['path'])) {
                    unlink($attachment['path']);
                }
            }
        } catch (Exception $e) {
            Log::error('Ошибка отправки уведомлений о платеже: ' . $e->getMessage());
        }
    }
    private function getUserPaymentBody(User $user, PackagePurchase $purchase, array $adminEmails): string
    {
        $package = $purchase->package;
        $adminEmailsList = implode('<br>', $adminEmails);
        $userName = $user->name ?? $user->login;
        return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #4a6fa5; color: white; padding: 10px 20px; border-radius: 5px 5px 0 0; }
            .content { background-color: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
            .info { background-color: #e8f4f8; padding: 15px; border-radius: 5px; margin: 15px 0; }
            .footer { margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Платеж подтвержден</h2>
            </div>
            <div class='content'>
                <p>Здравствуйте, <strong>{$userName}</strong>!</p>
                <p>Ваш платеж успешно подтвержден администратором.</p>

                <div class='info'>
                    <p><strong>Детали платежа:</strong></p>
                    <p>Пакет: {$package->code}</p>
                    <p>Номер телефона для связи: {$user->phone}</p>
                    <p>Дата подтверждения: " . now()->format('d.m.Y H:i') . "</p>
                </div>

                <p>Спасибо за использование нашего сервиса!</p>
            </div>
            <div class='footer'>
                <p>Это автоматическое сообщение, пожалуйста, не отвечайте на него.</p>
                <p><strong>Контакты администраторов:</strong></p>
                    <p>{$adminEmailsList}</p>
            </div>
        </div>
    </body>
    </html>
    ";
    }

    /**
     * Текст письма для администраторов
     */
    private function getAdminPaymentBody(User $user, PackagePurchase $purchase, string $adminName): string
    {
        $package = $purchase->package;

        return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #4a6fa5; color: white; padding: 10px 20px; border-radius: 5px 5px 0 0; }
            .content { background-color: #f9f9f9; padding: 20px; border-radius: 0 0 5px 5px; }
            .info { background-color: #e8f4f8; padding: 15px; border-radius: 5px; margin: 15px 0; }
            .footer { margin-top: 20px; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Платеж подтвержден администратором</h2>
            </div>
            <div class='content'>
                <p>Администратор <strong>{$adminName}</strong> подтвердил платеж пользователя.</p>

                <div class='info'>
                    <p><strong>Детали:</strong></p>
                    <p>Пользователь: {$user->login}</p>
                    <p>Имя: {$user->name}</p>
                    <p>Номер телефона: {$user->phone}</p>
                    <p>Пакет: {$package->code}</p>
                    <p>Дата подтверждения: " . now()->format('d.m.Y H:i') . "</p>
                </div>
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
