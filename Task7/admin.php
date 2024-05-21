<?php
include 'repository/connection.php';
include 'repository/UserRepository.php';
include 'repository/LanguagesRepository.php';
include 'repository/AdminRepository.php';

header('Content-Type: text/html; charset=UTF-8');
header("X-XSS-Protection: 1; mode=block");

if (empty($_SERVER['PHP_AUTH_USER']) ||
    empty($_SERVER['PHP_AUTH_PW'])) {
    header('HTTP/1.1 401 Unanthorized');
    header('WWW-Authenticate: Basic realm="My site"');
    print('<h1>401 Требуется авторизация</h1>');
    exit();
} else {
    $username = $_SERVER['PHP_AUTH_USER'];
    $admin = findAdminByUsername($db, $username);

    if (empty($admin) ||
        md5($_SERVER['PHP_AUTH_PW']) != $admin['password']) {
        header('HTTP/1.1 401 Unanthorized');
        header('WWW-Authenticate: Basic realm="My site"');
        print('<h1>401 Неверный пароль или логин</h1>');
        exit();
    }
}

if (empty($_COOKIE["csrf"])) {
    $csrf_token = bin2hex(random_bytes(32));
    setcookie("csrf", strip_tags($csrf_token), time() + 3600);
    header('Location: ./admin.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $users = findALlFormData($db);
    $languages = [];
    foreach ($users as $user) {
        $languages[$user['user_id']] = findAllLanguagesByUser($db, $user['id']);
    }

    $validLanguages =  findAllLanguages($db);
    $statistics =  findCountByLanguage($db);

    if (isset($_COOKIE['edit'])) {
        setcookie('error', '', time() - 3600);
        print('Данные успешно изменены.');
    } else {
        print('Вы успешно авторизовались и видите защищенные паролем данные.');
    }

    include('admin-page.php');
} else {

    if (empty($_POST["csrf"])
        || empty($_COOKIE["csrf"])
        || $_COOKIE["csrf"] != $_POST["csrf"]) {
        die("CSRF валиадция не удалась");
    }

    if (isset($_POST['action']) && $_POST['action'] == 'delete') {
        $id = $_POST['id'];
        deleteLanguagesByUserId($db, $id);
        deleteUserById($db, $id);
    } else {
        $id = $_POST['id'];
        updateUserById($db, $id, $_POST['name'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biography']);
        deleteLanguagesByUserId($db, $id);
        saveLanguages($db, $_POST['languages'], $id);
    }


    $csrf_token = bin2hex(random_bytes(32));
    setcookie("csrf", strip_tags($csrf_token), time() + 3600);

    setcookie('edit', '1', time() + 24 * 3600);
    header('Location: ./admin.php');
}