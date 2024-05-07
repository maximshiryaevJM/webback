<?php
include '/home/u67321/www/Task6/repository/UserRepository.php';
include '/home/u67321/www/Task6/repository/connection.php';

header('Content-Type: text/html; charset=UTF-8');

$session_started = false;
if (session_start()) {
    $session_started = true;
    if (!empty($_SESSION['login']) && $_COOKIE[session_name()]) {
        header('Location: ./');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include ("login-page.php");
} // Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
    $userId = getUserIdIfAuthenticated($db, $_POST['login'], $_POST['password']);
    if ($userId != -1) {
        if (!$session_started) {
            session_start();
        }

        setcookie('error', '', 100000);
        $_SESSION['login'] = $_POST['login'];
        $_SESSION['id'] = $userId;

        header('Location: ./');
    } else {
        setcookie('error', '1', time() + 24 * 60 * 60);
        header('Location: ./login.php');
    }
}