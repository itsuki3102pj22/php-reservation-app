<?php
session_start();

// ログインチェック
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// DB接続
require_once __DIR__ . '/../config/db.php';

try {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['user_name'] ?? 'ゲスト';

    // 該当ユーザーの予約一覧をDBから取得
    $stmt = $pdo->prepare("SELECT r.id, s.name, r.reserved_at FROM reservations AS r JOIN services AS s ON r.service_id = s.id WHERE r.user_id = :user_id ORDER BY r.reserved_at DESC");
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $reservations = [];
}

require_once __DIR__ . '/mypage_view.phtml';
