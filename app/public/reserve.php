<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: register.php?message=require_auth');
}

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

    $service_id = $_GET['service_id'] ?? $_POST['service_id'] ?? null;

    if (!$service) {
        exit("該当データは存在しません。");
    }
} catch (PDOException $e) {
    exit("DBエラー：" . $e->getMessage());
}

// エラーメッセージ初期化
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reserved_at = trim($_POST['reserved_at'] ?? '');
    $user_id = $_SESSION['user_id'] ?? '';

    // TODO 1: 予約日時が未入力の場合は、予約処理を行わずエラーメッセージを用意する。
    if ($reserved_at === '') 
        $error = "予約日時を指定してください。";
    } else {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $reserved_at);
        $dateErrors = DateTimeimmutable::getLastErrors();
    }
    // TODO 2: 入力値を日時として扱える形式か確認する。不正な形式の場合もエラーメッセージを用意する。
    // TODO 3: 入力された予約日時と現在日時を比較し、過去の日時ならエラーメッセージを用意する。
    if ($resered_at =< CURRENT) {
        $error = "予約日時は未来の日時を指定してください。";
    }
    // TODO 4: エラーメッセージがない場合だけ、下の予約登録処理を実行するよう条件を組み立てる。
    if (empty($error)) {
        try {
            $formatted_reserved_at = date('Y-m-d H:i:s', strtotime($reserved_at));
            $stmt = $pdo->prepare("INSERT INTO reservations (user_id, service_id, reserved_at, status) VALUES (:user_id, :service_id, :reserved_at, :status)");
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':service_id', $service_id, PDO::PARAM_INT);
            $stmt->bindValue(':reserved_at', $formatted_reserved_at, PDO::PARAM_STR);
            $stmt->bindValue(':status', 'reserved', PDO::PARAM_STR);
            $stmt->execute();

            header('Location: mypage.php?status=success');
            exit();
        } catch (PDOException $e) {
            $error = "予約処理中にエラーが発生しました。" . $e->getMessage();
        }
    } else {
        // TODO 5: エラーがある場合は、reserve_view.phtml でその内容を表示できる変数に代入する。
        $error = "予約日時が空欄です。";
    }
}

require_once __DIR__ . '/reserve_view.phtml';
