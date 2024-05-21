<?php

function findAdminByUsername($db, $username) {
    try {
        $adminStmt = $db->prepare("SELECT * FROM admin WHERE login = ?");
        $adminStmt->execute([$username]);
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }

    $admin = $adminStmt->fetch();

    if ($admin) {
        foreach ($admin as $key => $value) {
            $admin[$key] = strip_tags($value);
        }
    }

    return $admin;
}