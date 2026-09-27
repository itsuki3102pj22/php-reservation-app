<?php

// DB接続
require_once __DIR__ . '/../config/db.php';

// 変数初期化
$errors = [];
$name = '';
$email = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // バリデーションチェック
    if (empty($name)) {
        $errors[] = "お名前を入力してください。";
    }

    if (empty($email)) {
        $errors[] = "メールアドレスを入力してください。";
    }

    if (empty($password)) {
        $errors[] = "パスワードを入力してください。";
    }

    // 登録処理
    if (empty($errors)) {
        try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->bindValue(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->execute();

        header('Location: login.php');
        exit();
        } catch (PDOException $e) {
            // メールアドレス重複時の対処
            if ($e->getCode() === '23000') {
                $errors[] = 'このメールアドレスは既に登録済みです。';
            } else {
                $errors[] = '登録処理中にエラーが発生しました。';
            }
        }
    }
}

require_once __DIR__ . '/register_view.phtml';