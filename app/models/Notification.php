<?php

class Notification
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function log(int $userId, string $channel, string $title, string $message, string $status = 'sent'): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, channel, title, message, status, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $ok = $stmt->execute([$userId, $channel, $title, $message, $status]);
        if ($ok) {
            // cleanup old notifications, simpan hanya N terbaru
            try {
                $this->cleanupOld(200);
            } catch (Throwable $e) {
                // jangan ganggu proses utama kalau cleanup gagal
            }
        }
        return $ok;
    }

    public function getAll(int $limit = 100): array
    {
        $stmt = $this->db->prepare("
            SELECT n.*, u.email 
            FROM notifications n
            JOIN users u ON u.id = n.user_id
            ORDER BY n.created_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Hapus notifikasi lama sehingga hanya menyimpan $keep entri terbaru.
     */
    private function cleanupOld(int $keep = 200): void
    {
        $keep = (int) $keep;
        if ($keep <= 0) return;

        $sql = "DELETE n FROM notifications n
                LEFT JOIN (SELECT id FROM (SELECT id FROM notifications ORDER BY created_at DESC LIMIT $keep) AS x) k
                ON n.id = k.id
                WHERE k.id IS NULL";

        $this->db->exec($sql);
    }
}
