<?php

// DB接続
require_once __DIR__ . '/../config/db.php';

// service_idを取得（未指定なら一覧へ）
if (empty($_GET['service_id'])) {
    header('Location: services.php');
    exit();
}

$service_id = $_GET['service_id'];

// 取得したservice_idのサービス情報をservicesテーブルから取得
try {
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = :id");
    $stmt->bindValue(':id', $service_id, PDO::PARAM_INT);
    $stmt->execute();
    $service = $stmt->fetch();

    if (!$service) {
        exit("該当データは存在しません。");
    }
} catch (PDOException $e) {
    exit("DBエラー：" . $e->getMessage());
}

// エラーメッセージ初期化
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserved_at = $_POST['reserved_at'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    if (!empty($reserved_at) && !empty($user_id)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO reservations (user_id, service_id, reserved_at, status) VALUES (:user_id, :service_id, :reserved_at, :status)");
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':service_id', $service_id, PDO::PARAM_INT);
            $stmt->bindValue(':reserved_at', $reserved_at, PDO::PARAM_STR);
            $stmt->bindValue(':status', 'reserved', PDO::PARAM_STR);
            $stmt->execute();

            header('Location: services.php');
            exit();
        } catch (PDOException $e) {
            $errorMessage = "予約処理中にエラーが発生しました。" . $e->getMessage();
        }
    } else {
        $errorMessage = "予約日時が空欄です。";
    }
}

require_once __DIR__ . '/../public/reserve_view.phtml';
