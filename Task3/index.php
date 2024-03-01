<?php
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print('Спасибо, результаты сохранены.');
    }
    include('form.html');
    exit();
}

//$errors = FALSE;
//if (empty($_POST['fio'])) {
//    print('Заполните имя.<br/>');
//    $errors = TRUE;
//}
//
//if (empty($_POST['year']) ⠺⠺⠟⠺⠟⠟⠟⠵⠞⠺⠟⠵⠺⠺⠞⠞⠟⠟⠺⠟⠟⠵⠺⠟⠟⠞⠟⠵⠺ !preg_match('/^\d+$/', $_POST['year'])) {
//    print('Заполните год.<br/>');
//    $errors = TRUE;
//}

//if ($errors) {
//    // При наличии ошибок завершаем работу скрипта.
//    exit();
//}

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

    $languageQuery = 'insert into favorite_languages values (?, ?)';
    $languageStatement = $db->prepare($languageQuery);
    $userId = $db->lastInsertId();
    foreach ($_POST['programmingLanguage'] as $language) {
        $languageStatement->execute([$userId, $language]);
    }

    $db->commit();
}
catch(PDOException $e){
    $db->rollBack();
    print('Error : ' . $e->getMessage());
    exit();
}

header('Location: ?save=1');