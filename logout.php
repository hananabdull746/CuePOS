<?php require_once __DIR__.'/config/db.php'; startAppSession(); $_SESSION=[]; session_destroy(); header('Location: '.url('index.php')); exit;
