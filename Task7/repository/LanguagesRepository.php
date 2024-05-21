<?php

function findAllLanguages($db) {
    try {
        $testStatement = $db->prepare("SELECT language FROM favorite_languages");
        $testStatement->execute();
        $validOptions = [];
        foreach ($testStatement as $row) {
            $validOptions[] = strip_tags($row['language']);
        }
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
    return $validOptions;
}

function findAllLanguagesByUser($db, $userId) {
    try {
        $testStatement = $db->prepare("SELECT language 
                                        FROM favorite_languages l 
                                        JOIN users_languages ul ON ul.language_id = l.id
                                        WHERE ul.user_id = ?");
        $testStatement->execute([$userId]);
        $pLang = [];
        foreach ($testStatement as $row) {
            $pLang[] = strip_tags($row['language']);
        }
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
    return $pLang;
}

function saveLanguages($db, $languages, $id) {
    try {
        $languageQuery = 'SELECT id FROM favorite_languages WHERE language = ?';
        $linkQuery = 'INSERT INTO users_languages (user_id, language_id) VALUES (?, ?)';
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
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
}

function deleteLanguagesByUserId($db, $id) {
    try {
        $deleteLangs = $db->prepare("DELETE FROM users_languages WHERE user_id = ?");
        $deleteLangs->execute([$id]);
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
}

function findCountByLanguage($db) {
    try {
        $stmt = $db->prepare("SELECT l.language AS language, COUNT(*) AS c 
                              FROM favorite_languages l 
                              JOIN users_languages u ON u.language_id = l.id 
                              GROUP BY language");
        $stmt->execute();
        $statistics = [];
        foreach ($stmt as $row) {
            $statistics[strip_tags($row['language'])] = strip_tags($row['c']);
        }
    } catch (PDOException $e) {
        print('Error: ' . strip_tags($e->getMessage()));
        exit();
    }
    return $statistics;
}
