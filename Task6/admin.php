<?php
include '/home/u67321/www/Task6/repository/connection.php';
include '/home/u67321/www/Task6/repository/UserRepository.php';
include '/home/u67321/www/Task6/repository/LanguagesRepository.php';
include '/home/u67321/www/Task6/repository/AdminRepository.php';

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

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $users = findALlFormData($db);
    $languages = [];
    foreach ($users as $user) {
        $languages[$user['user_id']] = findAllLanguagesByUser($db, $user['id']);
    }

    $validLanguages = findAllLanguages($db);

    if (isset($_COOKIE['edit'])) {
        setcookie('error', '', 100000);
        print('Данные успешно изменены.');
    } else {
        print('Вы успешно авторизовались и видите защищенные паролем данные.');
    }

    include('admin-page.php');
} else {
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

    setcookie('edit', '1', time() + 24 * 60 * 60);
    header('Location: admin.php');
}