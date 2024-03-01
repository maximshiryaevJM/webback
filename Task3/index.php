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
    $userStatement = $db->prepare("insert into users
(name, phone, email, birth_date, gender, biography)
values (:name, :phone, :email, :birth_date, :gender, :biography)");
    $userStatement->bindParam(':name', $_POST['name']);
    $userStatement->bindParam(':phone', $_POST['phone']);
    $userStatement->bindParam(':email', $_POST['email']);
    $userStatement->bindParam(':birthdate', $_POST['birth_date']);
    $userStatement->bindParam(':gender', $_POST['gender']);
    $userStatement->bindParam(':biography', $_POST['biography']);
    $userStatement->execute();
}
catch(PDOException $e){
    print('Error : ' . $e->getMessage());
    exit();
}

header('Location: ?save=1');