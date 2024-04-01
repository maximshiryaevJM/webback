<?php

/**
 * Файл login.php для не авторизованного пользователя выводит форму логина.
 * При отправке формы проверяет логин/пароль и создает сессию,
 * записывает в нее логин и id пользователя.
 * После авторизации пользователь перенаправляется на главную страницу
 * для изменения ранее введенных данных.
 **/

header('Content-Type: text/html; charset=UTF-8');

$session_started = false;
if ($_COOKIE[session_name()] && session_start()) {
    $session_started = true;
    if (!empty($_SESSION['login'])) {
        // Если есть логин в сессии, то пользователь уже авторизован.
        // TODO: Сделать выход (окончание сессии вызовом session_destroy()
        //при нажатии на кнопку Выход).
        // Делаем перенаправление на форму.
        header('Location: ./');
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    ?>

    <form action="" method="post">
        <input name="login"/>
        <input name="pass"/>
        <input type="submit" value="Войти"/>
    </form>

    <?php
} // Иначе, если запрос был методом POST, т.е. нужно сделать авторизацию с записью логина в сессию.
else {
    // TODO: Проверть есть ли такой логин и пароль в базе данных.
    // Выдать сообщение об ошибках.

    $isAuth = FALSE;
    $userId = -1;
    $user = 'u67321';
    $pass = '6300196';
    $db = new PDO('mysql:host=localhost;dbname=u67321', $user, $pass,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    try {
        $userQuery = 'select * from usert5 where login = ? and password = ?';
        $userStatement = $db->prepare($userQuery);
        $userStatement->execute();

        $userInfo = $userStatement->fetch();
        if ($_POST['login'] == $userInfo['login'] &&
            md5($_POST['password'] == $userInfo['password'])) {
            $userId = $userInfo['id'];
            $isAuth = TRUE;
        }
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }

    if ($isAuth) {
        if (!$session_started) {
            session_start();
        }

        $_SESSION['login'] = $_POST['login'];
        $_SESSION['id'] = $userId;

        header('Location: ./');
    } else {
        $error = '<div class="error">Логин или пароль неверные</div>';
        setcookie('error', '1', time() + 24 * 60 * 60);
        include ('login.php');
    }
}