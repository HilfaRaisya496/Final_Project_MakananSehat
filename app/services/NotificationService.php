<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../services/RecommendationService.php';

class NotificationService
{
    private PDO $db;
    private Notification $notificationModel;
    private RecommendationService $recommendationService;

    public function __construct(PDO $db, RecommendationService $recommendationService)
    {
        $this->db = $db;
        $this->notificationModel = new Notification($db);
        $this->recommendationService = $recommendationService;
    }

    public function sendDailyMenuReminder(int $userId): bool
    {
        // ambil email user
        $stmt = $this->db->prepare("SELECT email, name FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || empty($user['email'])) {
            return false;
        }

        // generate plan singkat untuk hari ini
        $plan = $this->recommendationService->generateForUser($userId, null, null);
        if (($plan['status'] ?? 'success') === 'failure') {
            // tetap log tapi status failed
            $this->notificationModel->log(
                $userId,
                'email',
                'Pengingat Menu Sehat Harian',
                'Gagal generate meal plan: ' . ($plan['message'] ?? 'unknown'),
                'failed'
            );
            return false;
        }

        // susun isi email simpel dari 3 meal
        $lines = [];
        foreach ($plan['meals'] ?? [] as $i => $meal) {
            $labels = ['Sarapan', 'Makan siang', 'Makan malam'];
            $label  = $labels[$i] ?? 'Menu';
            $lines[] = sprintf(
                "%s: %s (~%d kkal / porsi)",
                $label,
                $meal['title'] ?? '-',
                (int)($meal['calories'] ?? 0)
            );
        }
        $bodyText = "Halo " . ($user['name'] ?? 'Mahasiswa') . ",\n\n"
            . "Berikut rekomendasi menu sehat untuk hari ini:\n\n"
            . implode("\n", $lines)
            . "\n\nJangan lupa catat apa yang kamu makan di aplikasi supaya pola makanmu bisa dianalisis.\n\n"
            . "Salam sehat,\nAplikasi Rekomendasi Makanan Sehat";

        $mail = new PHPMailer(true);
        $status = 'sent';
        $title  = 'Pengingat Menu Sehat Harian';

        try {
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'] ?? 'smtp.example.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'] ?? '';
            $mail->Password   = $_ENV['SMTP_PASS'] ?? '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = (int)($_ENV['SMTP_PORT'] ?? 587);

            $mail->setFrom($_ENV['SMTP_FROM_EMAIL'] ?? 'no-reply@example.com', $_ENV['SMTP_FROM_NAME'] ?? 'Meal App');
            $mail->addAddress($user['email'], $user['name'] ?? '');

            $mail->Subject = $title;
            $mail->Body    = $bodyText;
            $mail->AltBody = $bodyText;

            $mail->send();
        } catch (Exception $e) {
            $status = 'failed';
        }

        // log ke tabel notifications
        $this->notificationModel->log($userId, 'email', $title, $bodyText, $status);

        return $status === 'sent';
    }
}
