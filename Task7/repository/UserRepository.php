<?php

function getUserIdIfAuthenticated($db, $login, $password) {
    $userId = -1;
    try {
        $userQuery = "SELECT * FROM usert5 WHERE login = ?";
        $userStatement = $db->prepare($userQuery);
        $userStatement->execute([$login]);

        $userInfo = $userStatement->fetch();
        if ($userInfo && $login == $userInfo['login'] && md5($password) == $userInfo['password']) {
            $userId = (int)$userInfo['id'];
        }
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
    return $userId;
}

function findUserByUserId($db, $userId) {
    $user = [];
    try {
        $userStmt = $db->prepare("SELECT a.* FROM users a JOIN usert5 b ON a.user_id = b.id WHERE b.id = ?");
        $userStmt->execute([$userId]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            foreach ($user as $key => $value) {
                $user[strip_tags($key)] = strip_tags($value);
            }
        }
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
    return $user;
}

function updateUserByUserId($db, $userId, $name, $phone, $email, $birthdate, $gender, $biography) {
    try {
        $updateStmt = $db->prepare("UPDATE users SET name = ?, phone = ?, email = ?, birth_date = ?, gender = ?, biography = ? WHERE user_id = ?");
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
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
}

function saveToUsert5($db, $id, $login, $password) {
    try {
        $saveStmt = $db->prepare("INSERT INTO usert5 (id, login, password) VALUES (?, ?, ?)");
        $saveStmt->execute([$id, $login, md5($password)]);
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
}

function saveToUsers($db, $userId, $name, $phone, $email, $birthdate, $gender, $biography) {
    try {
        $userQuery = 'INSERT INTO users (name, phone, email, birth_date, gender, biography, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $userStatement = $db->prepare($userQuery);
        $userStatement->execute([
            $name,
            $phone,
            $email,
            $birthdate,
            $gender,
            $biography,
            $userId
        ]);
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
    return $db->lastInsertId();
}

function getFormId($db, $id) {
    $formId = null;
    try {
        $getFormId = $db->prepare("SELECT id FROM users WHERE user_id = ?");
        $getFormId->execute([$id]);
        $formId = (int)$getFormId->fetchColumn();
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
    return $formId;
}

function findAllFormData($db) {
    $formData = [];
    try {
        $getFormId = $db->prepare("SELECT * FROM users");
        $getFormId->execute();
        $formData = $getFormId->fetchAll();
        foreach ($formData as &$row) {
            foreach ($row as $key => $value) {
                $row[strip_tags($key)] = strip_tags($value);
            }
        }
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
    return $formData;
}

function deleteUserById($db, $id) {
    try {
        $deleteStmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $deleteStmt->execute([$id]);
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
}

function updateUserById($db, $id, $name, $phone, $email, $birthdate, $gender, $biography) {
    try {
        $updateStmt = $db->prepare("UPDATE users SET name = ?, phone = ?, email = ?, birth_date = ?, gender = ?, biography = ? WHERE id = ?");
        $updateStmt->execute([
            $name,
            $phone,
            $email,
            $birthdate,
            $gender,
            $biography,
            $id
        ]);
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
}
