<?php
include '/home/u67321/www/variables.php';
/**
 * Реализовать проверку заполнения обязательных полей формы в предыдущей
 * с использованием Cookies, а также заполнение формы по умолчанию ранее
 * введенными значениями.
 */

header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Массив для временного хранения сообщений пользователю.
    $messages = array();

    // В суперглобальном массиве $_COOKIE PHP хранит все имена и значения куки текущего запроса.
    // Выдаем сообщение об успешном сохранении.
    if (!empty($_COOKIE['save'])) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('save', '', 100000);
        // Если есть параметр save, то выводим сообщение пользователю.
        $messages[] = 'Спасибо, результаты сохранены.';
    }

    // Складываем признак ошибок в массив.
    $errors = array();
    $errors['name'] = !empty($_COOKIE['name_error']);
    $errors['phone'] = !empty($_COOKIE['phone_error']);
    $errors['email'] = !empty($_COOKIE['email_error']);
    $errors['birthdate'] = !empty($_COOKIE['birthdate_error']);
    $errors['gender'] = !empty($_COOKIE['gender_error']);
    $errors['programmingLanguage'] = !empty($_COOKIE['programmingLanguage_error']);
    $errors['biography'] = !empty($_COOKIE['biography_error']);
    $errors['agreement'] = !empty($_COOKIE['agreement_error']);

    // Выдаем сообщения об ошибках.
    if ($errors['name']) {
        // Удаляем куку, указывая время устаревания в прошлом.
        setcookie('name_error', '', 100000);
        setcookie('name_value', '', 100000);
        // Выводим сообщение.
        $messages[] = '<div class="error">Поле имя должно быть не длиннее 150 символов и содержать только пробелы и буквы</div>';
    }
    if ($errors['phone']) {
        setcookie('phone_error', '', 100000);
        setcookie('phone_value', '', 100000);
        $messages[] = '<div class="error">Поле телефон должно быть не длиннее 12 символов и содержать только цифры и знак +</div>';
    }
    if ($errors['email']) {
        setcookie('email_error', '', 100000);
        setcookie('email_value', '', 100000);
        $messages[] = '<div class="error">Неверный формат email</div>';
    }
    if ($errors['birthdate']) {
        setcookie('birthdate_error', '', 100000);
        setcookie('birthdate_value', '', 100000);
        $messages[] = '<div class="error">Неверный формат даты, используйте формат yyyy-mm-dd</div>';
    }
    if ($errors['gender']) {
        setcookie('gender_error', '', 100000);
        setcookie('gender_value', '', 100000);

        $messages[] = '<div class="error">Выберите пол</div>';
    }
    if ($errors['programmingLanguage']) {
        setcookie('programmingLanguage_error', '', 100000);
        setcookie('programmingLanguage_value', '', 100000);

        $messages[] = '<div class="error">Выберите язык</div>';
    }
    if ($errors['biography']) {
        setcookie('biography_error', '', 100000);
        setcookie('biography_value', '', 100000);

        $messages[] = '<div class="error">Поле биография может содержать только буквы, цифры, символы .,!?\'\"()</div>';
    }
    if ($errors['agreement']) {
        setcookie('agreement_error', '', 100000);
        setcookie('agreement_value', '', 100000);
        $messages[] = '<div class="error">Необходимо ознакомиться с контрактом</div>';
    }

    // Складываем предыдущие значения полей в массив, если есть.
    $values = array();
    $values['name'] = empty($_COOKIE['name_value']) ? '' : $_COOKIE['name_value'];
    $values['phone'] = empty($_COOKIE['phone_value']) ? '' : $_COOKIE['phone_value'];
    $values['email'] = empty($_COOKIE['email_value']) ? '' : $_COOKIE['email_value'];
    $values['birthdate'] = empty($_COOKIE['birthdate_value']) ? '' : $_COOKIE['birthdate_value'];
    $values['gender'] = empty($_COOKIE['gender_value']) ? '' : $_COOKIE['gender_value'];
    $values['programmingLanguage'] = empty($_COOKIE['programmingLanguage_value']) ? '' : json_decode($_COOKIE['programmingLanguage_value']);
    $values['biography'] = empty($_COOKIE['biography_value']) ? '' : $_COOKIE['biography_value'];
    $values['agreement'] = empty($_COOKIE['agreement_value']) ? '' : $_COOKIE['agreement_value'];

    $db = new PDO('mysql:host=localhost;dbname=u67321', $user, $pass,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $testStatement = $db->prepare("select language from favorite_languages");
    $testStatement->execute();
    $validOptions = [];
    foreach ($testStatement as $row) {
        $validOptions[] = $row['language'];
    }

    // Включаем содержимое файла form.php.
    // В нем будут доступны переменные $messages, $errors и $values для вывода
    // сообщений, полей с ранее заполненными данными и признаками ошибок.
    include('form.php');
} // Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {
    // Проверяем ошибки.
    $errors = FALSE;
    if (!preg_match("/^[a-zA-Z\s]{1,150}$/", $_POST['name'])) {
        // Выдаем куку на день с флажком об ошибке в поле fio.
        setcookie('name_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('name_value', $_POST['name'], time() + 30 * 24 * 60 * 60);

    if (!preg_match("/^\+\d{1,12}$/", $_POST['phone'])) {
        setcookie('phone_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('phone_value', $_POST['phone'], time() + 30 * 24 * 60 * 60);

    if (!preg_match("/^([a-z0-9_.-]+)@([\da-z.-]+)\.([a-z.]{2,6})$/", $_POST['email'])) {
        setcookie('email_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('email_value', $_POST['email'], time() + 30 * 24 * 60 * 60);

    if (!preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $_POST['birthdate'])) {
        setcookie('birthdate_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('birthdate_value', $_POST['birthdate'], time() + 30 * 24 * 60 * 60);

    if (empty($_POST['gender']) || $_POST['gender'] != 'male' && $_POST['gender'] != 'female') {
        setcookie('gender_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('gender_value', $_POST['gender'], time() + 30 * 24 * 60 * 60);

    if (!preg_match("/^[a-zA-Z0-9\s.,!?'\"()]+$/", $_POST['biography'])) {
        setcookie('biography_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('biography_value', $_POST['biography'], time() + 30 * 24 * 60 * 60);

    if (!isset($_POST['agreement'])) {
        setcookie('agreement_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('agreement_value', $_POST['agreement'], time() + 30 * 24 * 60 * 60);

    $db = new PDO('mysql:host=localhost;dbname=u67321', $user, $pass,
        [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $testStatement = $db->prepare("select language from favorite_languages");
    $testStatement->execute();
    $validOptions = [];
    foreach ($testStatement as $row) {
        $validOptions[] = $row['language'];
    }

    $plError = FALSE;
    if (isset($_POST['programmingLanguage'])) {
        $invalidOptions = array_diff($_POST['programmingLanguage'], $validOptions);
        if (!empty($invalidOptions)) {
            $plError = TRUE;
        }
    } else {
        $plError = TRUE;
    }
    if ($plError) {
        setcookie('programmingLanguage_error', '1', time() + 24 * 60 * 60);
        $errors = TRUE;
    }
    setcookie('programmingLanguage_value', json_encode($_POST['programmingLanguage']), time() + 30 * 24 * 60 * 60);

    if ($errors) {
        // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
        header('Location: index.php');
        exit();
    } else {
        // Удаляем Cookies с признаками ошибок.
        setcookie('name_error', '', 100000);
        setcookie('phone_error', '', 100000);
        setcookie('email_error', '', 100000);
        setcookie('birthdate_error', '', 100000);
        setcookie('gender_error', '', 100000);
        setcookie('programmingLanguage_error', '', 100000);
        setcookie('biography_error', '', 100000);
        setcookie('agreement_error', '', 100000);
    }

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
        $linkQuery = 'insert into users_languages (user_id, language_id) values (?, ?)';
        $languageStatement = $db->prepare($languageQuery);
        $linkStatement = $db->prepare($linkQuery);
        foreach ($_POST['programmingLanguage'] as $language) {
            $languageStatement->execute([$language]);
            $languageId = $languageStatement->fetchColumn();
            print_r($language);
            print_r($languageId);
            if (!$languageId) {
                throw new PDOException("Could not find presented language");
            }
            $linkStatement->execute([$userId, $languageId]);
        }

        $db->commit();
    } catch (PDOException $e) {
        $db->rollBack();
        print('Error : ' . $e->getMessage());
        exit();
    }

    // Сохраняем куку с признаком успешного сохранения.
    setcookie('save', '1');

    // Делаем перенаправление.
    header('Location: index.php');
}