<?php

function getUserIdIfAuthenticated($db, $login, $password){
    $userId = -1;
    try {
        $userQuery = "select * from usert5 where login = ?";
        $userStatement = $db->prepare($userQuery);
        $userStatement->execute([$login]);

        $userInfo = $userStatement->fetch();
        if ($_POST['login'] == $userInfo['login'] &&
            md5($password) == $userInfo['password']) {
            $userId = $userInfo['id'];
        }
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $userId;
}

function findUserByUserId($db, $userId){
    try {
        $userStmt = $db->prepare("select a.* from users a join usert5 b on a.user_id = b.id where b.id = ?");
        $userStmt->execute([$userId]);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $userStmt->fetch();
}

function updateUserByUserId($db, $userId, $name, $phone, $email, $birthdate, $gender, $biography){
    try {
        $updateStmt = $db->prepare("update users set name = ?, phone = ?, email = ?, birth_date = ?, gender = ?, biography = ? where user_id = ?");
        $updateStmt->execute([
            $name,
            $phone,
            $email,
            $birthdate,
            $gender,
            $biography,
            $userId
        ]);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
}

function saveToUsert5($db, $id, $login, $password){
    try {
        $saveStmt = $db->prepare("insert into usert5 (id, login, password) values (?, ?, ?)");
        $saveStmt->execute([$id, $login, md5($password)]);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
}

function saveToUsers($db, $userId, $name, $phone, $email, $birthdate, $gender, $biography){
    try {
        $userQuery = 'insert into users 
(name, phone, email, birth_date, gender, biography, user_id) 
values (?, ?, ?, ?, ?, ?, ?)';
        $userStatement = $db->prepare($userQuery);
        $userStatement->execute(
            [$name,
                $phone,
                $email,
                $birthdate,
                $gender,
                $biography,
                $userId
            ]);

    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $db->lastInsertId();
}

function getFormId($db, $id) {
    try {
        $getFormId = $db->prepare("select id from users where user_id = ?");
        $getFormId->execute([$id]);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $getFormId->fetchColumn();
}