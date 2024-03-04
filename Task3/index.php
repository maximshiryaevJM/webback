<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print('Спасибо, результаты сохранены.<br>');
    }
    include('form.html');
    exit();
}

$errors = FALSE;
if (!preg_match("/^[a-zA-Z\s]{1,150}$/", $_POST['name'])) {
    print('Неверно введено фио.<br/>');
    $errors = TRUE;
}

if (!preg_match("/^\+\d{1,10}$/", $_POST['phone'])) {
    print('Неверно введен номер телефона.<br/>');
    $errors = TRUE;
}

if (!preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2, }$/", $_POST['email'])) {
    print('Неверно введен email.<br/>');
    $errors = TRUE;
}

if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $_POST['email'])) {
    print('Неверно введена дата рождения.<br/>');
    $errors = TRUE;
}

if ($_POST['gender'] != 'male' || $_POST['gender'] != 'female') {
    print('Неверно введен пол.<br/>');
    $errors = TRUE;
}

if (!preg_match("/^[a-zA-Z0-9\s.,!?'\"()]+$/", $_POST['biography'])) {
    print('Неверный формат биографии.<br/>');
    $errors = TRUE;
}

if ($errors) {
    exit();
}

$user = 'u67321';
$pass = '6300196';
$db = new PDO('mysql:host=localhost;dbname=u67321', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

try {
    $db->beginTransaction();
    $userQuery = 'insert into users 
(name, phone, email, birth_date, gender, biography) 
values (?, ?, ?, ?, ?, ?)';
    $userStatement = $db->prepare($userQuery);
$userStatement->execute(
        [$_POST['name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birthdate'],
        $_POST['gender'],
        $_POST['biography']
    ]);

    $userId = $db->lastInsertId();

    $languageQuery = 'select id from favorite_languages where language = ?';
    $linkQuery =  'insert into users_languages (user_id, language_id) values (?, ?)';
    $languageStatement = $db->prepare($languageQuery);
    $linkStatement = $db->prepare($linkQuery);
    foreach ($_POST['programmingLanguage'] as $language) {
        $languageStatement->execute([$language]);
        $languageId = $languageStatement->fetchColumn();
        if (!$languageId) {
            throw new PDOException("Could not find presented language");
        }
        $linkStatement->execute([$userId, $languageId]);
    }

    $db->commit();
}
catch(PDOException $e){
    $db->rollBack();
    print('Error : ' . $e->getMessage());
    exit();
}

header('Location: ?save=1');