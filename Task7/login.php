<?php
include 'repository/UserRepository.php';
include 'repository/connection.php';

header('Content-Type: text/html; charset=UTF-8');
header("X-XSS-Protection: 1; mode=block");

$session_started = false;
if (session_start()) {
    $session_started = true;
    if (!empty($_SESSION['login']) && $_COOKIE[session_name()]) {
        header('Location: ./');
        exit();
    }
}

if (empty($_COOKIE["csrf"])) {
    $csrf_token = bin2hex(random_bytes(32));
    setcookie("csrf", strip_tags($csrf_token), time() + 3600);
    // перезагрузка страницы,
    // чтобы куки корректно отображались
    // при первом переходе на страницу
    header('Location: ./login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    include ("login-page.php");
} // Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {

    if (empty($_POST["csrf"])
        || empty($_COOKIE["csrf"])
        || $_COOKIE["csrf"] != $_POST["csrf"]) {
        die("CSRF валиадция не удалась");
    }

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

    $csrf_token = bin2hex(random_bytes(32));
    setcookie("csrf", strip_tags($csrf_token), time() + 3600);
}