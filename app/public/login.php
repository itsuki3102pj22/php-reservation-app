<?php
session_start();

// DB接続
require_once __DIR__ . '/../config/db.php';

// 変数初期化
$errors = [];
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // セッション再生成
                session_regenerate_id (true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                header('Location: service.php');
                exit();
            }
        } catch (PDOException $e) {
            $errors[] = "処理中にエラーが発生しました。" . $e->getMessage();
        }
    } else {
        $errors[] = "メールアドレスまたはパスワードが正しくありません。";
    }
} 
require_once __DIR__ . '/login_view.phtml';