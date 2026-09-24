<?php

// DB接続
require_once __DIR__ . '/../config/db.php';

// servicesテーブルからサービス情報取得
$stmt = $pdo->prepare("SELECT * FROM services ORDER BY id ASC");
$stmt->execute();
$services = $stmt->fetchAll();

// 表示ファイル読み込み
require_once __DIR__ . '/services_view.phtml';