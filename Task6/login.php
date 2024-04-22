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
    ?>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <title>Войти</title>
        <style>
            .error {
                border: 2px solid red;
            }
        </style>
    </head>
    <body>
    <div class="messages">
        <?php
        if (!empty($_COOKIE['error'])) {
            print '<div class="error">Логин или пароль неверные</div>';
            setcookie('error', '', 100000);
        }
        ?>
    </div>
    <?php
    if ($session_started && !empty($_SESSION['login'])) {
        print '<form method="post" action="" style="position: fixed; right: 10px; top: 10px">
        <input hidden="hidden" name="logout" value="1">
        <button type="submit" class="btn btn-secondary ml-2">Sign out</button>
    </form>';
    }
    ?>
    <div class="container mt-5">
        <form method="post" action="" class="">
            <div class="form-group">
                <label for="login">Username</label>
                <input type="text" class="form-control <?php
                if (!empty($_COOKIE['error']))
                    print 'error';
                ?>" name="login" id="login" placeholder="Enter username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control <?php
                if (!empty($_COOKIE['error']))
                    print 'error';
                ?>" name="password" id="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form
    </div>
    </body>
    <?php
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