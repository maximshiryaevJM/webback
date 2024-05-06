<?php

function findAdminByUsername($db, $username) {
    try {
        $adminStmt = $db->prepare("select * from admin where login = ?");
        $adminStmt->execute([$username]);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $adminStmt->fetch();
}