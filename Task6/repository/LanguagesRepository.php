<?php

function findAllLanguages($db) {
    try {
        $testStatement = $db->prepare("select language from favorite_languages");
        $testStatement->execute();
        $validOptions = [];
        foreach ($testStatement as $row) {
            $validOptions[] = strip_tags($row['language']);
        }
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $validOptions;
}

function findAllLanguagesByUser($db, $userId) {
    try {
        $testStatement = $db->prepare("select language 
from favorite_languages l 
join users_languages ul on ul.language_id = l.id
where ul.user_id = ?");
        $testStatement->execute([$userId]);
        $pLang = [];
        foreach ($testStatement as $row) {
            $pLang[] = strip_tags($row['language']);
        }
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
    return $pLang;
}

function saveLanguages($db, $languages, $id) {
    try {
        $languageQuery = 'select id from favorite_languages where language = ?';
        $linkQuery = 'insert into users_languages (user_id, language_id) values (?, ?)';
        $languageStatement = $db->prepare($languageQuery);
        $linkStatement = $db->prepare($linkQuery);
        foreach ($languages as $language) {
            $languageStatement->execute([$language]);
            $languageId = $languageStatement->fetchColumn();
            if (!$languageId) {
                throw new PDOException("Could not find presented language");
            }
            $linkStatement->execute([$id, $languageId]);
        }
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }
}

function deleteLanguagesByUserId($db, $id) {
    try {
        $deleteLangs = $db->prepare("delete from users_languages where user_id = ?");
        $deleteLangs->execute([$id]);
    } catch (PDOException $e) {
        print('Error : ' . $e->getMessage());
        exit();
    }

}