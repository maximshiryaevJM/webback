<?php
include 'repository/connection.php';
include 'repository/UserRepository.php';
include 'repository/LanguagesRepository.php';

header('Content-Type: text/html; charset=UTF-8');
header("X-XSS-Protection: 1; mode=block");

if (empty($_COOKIE["csrf"])) {
    $csrf_token = bin2hex(random_bytes(32));
    setcookie("csrf", strip_tags($csrf_token), time() + 3600);
    header('Location: ./');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $messages = array();

    if (!empty($_COOKIE['save'])) {
        setcookie('save', '', 100000);
        setcookie('login', '', 100000);
        $messages[] = 'Спасибо, результаты сохранены.';
        if (!empty($_COOKIE['pass'])) {
            $messages[] = sprintf('Вы можете <a href="login.php">войти</a> с логином <strong>%s</strong>
        и паролем <strong>%s</strong> для изменения данных.',
                strip_tags($_COOKIE['login']),
                strip_tags($_COOKIE['pass']));
        }
        setcookie('pass', '', 100000);

    }

    // Складываем признак ошибок в массив.
    $errors = handleErrors($messages);

    // Складываем предыдущие значения полей в массив, если есть.
    // При этом санитизуем все данные для безопасного отображения в браузере.
    $values = restoreValues();
    $validOptions = findAllLanguages($db);

    // Если нет предыдущих ошибок ввода, есть кука сессии, начали сессию и
    // ранее в сессию записан факт успешного логина.
    if (empty($errors) && !empty($_COOKIE[session_name()]) &&
        session_start() && !empty($_SESSION['login'])) {

       $row = findUserByUserId($db, $_SESSION['id']);

        $values['name'] = strip_tags($row['name_value']);
        $values['phone'] = strip_tags($row['phone_value']);
        $values['email'] = strip_tags($row['email_value']);
        $values['birthdate'] = strip_tags($row['birthdate_value']);
        $values['gender'] = strip_tags($row['gender_value']);
        $values['biography'] = strip_tags($row['biography_value']);
        $values['agreement'] = strip_tags($row['agreement_value']);
        $values['programmingLanguage'] = findAllLanguagesByUser($db, $row['id']);

        printf('Вход с логином %s, id %d', strip_tags($_SESSION['login']), strip_tags($_SESSION['id']));
    }

    // Включаем содержимое файла form.php.
    // В нем будут доступны переменные $messages, $errors и $values для вывода
    // сообщений, полей с ранее заполненными данными и признаками ошибок.
    include('form.php');
} // Иначе, если запрос был методом POST, т.е. нужно проверить данные и сохранить их в XML-файл.
else {

    if (empty($_POST["csrf"])
        || empty($_COOKIE["csrf"])
        || $_COOKIE["csrf"] != $_POST["csrf"]) {
        die("CSRF валиадция не удалась");
    }

    // Проверяем ошибки.
    $errors = testForErrors($db);

    if ($errors) {
        // При наличии ошибок перезагружаем страницу и завершаем работу скрипта.
        header('Location: index.php');
        exit();
    } else {
        // Удаляем Cookies с признаками ошибок.
        setcookie('name_error', '', time() - 3600);
        setcookie('phone_error', '', time() - 3600);
        setcookie('email_error', '', time() - 3600);
        setcookie('birthdate_error', '', time() - 3600);
        setcookie('gender_error', '', time() - 3600);
        setcookie('programmingLanguage_error', '', time() - 3600);
        setcookie('biography_error', '', time() - 3600);
        setcookie('agreement_error', '', time() - 3600);
    }

    // Проверяем меняются ли ранее сохраненные данные или отправляются новые.
    if (!empty($_COOKIE[session_name()]) &&
        session_start() && !empty($_SESSION['login'])) {

        updateUserByUserId($db, $_SESSION['id'], $_POST['name'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biography']);
        $formId = getFormId($db, $_SESSION['id']);
        deleteLanguagesByUserId($db, $formId);
        saveLanguages($db, $_POST['programmingLanguage'], $formId);
    } else {
        $id = mt_rand(1, 100000000);
        $login = 'user' . $id;
        $pass = substr(str_shuffle("!@#$%^&*()-_+=0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 12);
        // Сохраняем в Cookies.
        setcookie('login', strip_tags($login), time() + 24 * 60 * 60);
        setcookie('pass', strip_tags($pass), time() + 24 * 60 * 60);

        saveToUsert5($db, $id, $login, $pass);
        $userId = saveToUsers($db, $id, $_POST['name'], $_POST['phone'], $_POST['email'], $_POST['birthdate'], $_POST['gender'], $_POST['biography']);

        saveLanguages($db, $_POST['programmingLanguage'], $userId);
    }

    $csrf_token = bin2hex(random_bytes(32));
    setcookie("csrf", strip_tags($csrf_token), time() + 3600);

    setcookie('save', '1');

    header('Location: ./');
}

function handleErrors(&$messages) {
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
        setcookie('name_error', '', time() - 3600);
        setcookie('name_value', '', time() - 3600);

        // Выводим сообщение.
        $messages[] = '<div class="error">Поле имя должно быть не длиннее 150 символов и содержать только пробелы и буквы</div>';
    }
    if ($errors['phone']) {
        setcookie('phone_error', '', time() - 3600);
        setcookie('phone_value', '', time() - 3600);

        $messages[] = '<div class="error">Поле телефон должно быть не длиннее 12 символов и содержать только цифры и знак +</div>';
    }
    if ($errors['email']) {
        setcookie('email_error', '', time() - 3600);
        setcookie('email_value', '', time() - 3600);

        $messages[] = '<div class="error">Неверный формат email</div>';
    }
    if ($errors['birthdate']) {
        setcookie('birthdate_error', '', time() - 3600);
        setcookie('birthdate_value', '', time() - 3600);

        $messages[] = '<div class="error">Неверный формат даты, используйте формат yyyy-mm-dd</div>';
    }
    if ($errors['gender']) {
        setcookie('gender_error', '', time() - 3600);
        setcookie('gender_value', '', time() - 3600);

        $messages[] = '<div class="error">Выберите пол</div>';
    }
    if ($errors['programmingLanguage']) {
        setcookie('programmingLanguage_error', '', time() - 3600);
        setcookie('programmingLanguage_value', '', time() - 3600);

        $messages[] = '<div class="error">Выберите язык</div>';
    }
    if ($errors['biography']) {
        setcookie('biography_error', '', time() - 3600);
        setcookie('biography_value', '', time() - 3600);

        $messages[] = '<div class="error">Поле биография может содержать только буквы, цифры, символы .,!?\'\"()</div>';
    }
    if ($errors['agreement']) {
        setcookie('agreement_error', '', time() - 3600);
        setcookie('agreement_value', '', time() - 3600);

        $messages[] = '<div class="error">Необходимо ознакомиться с контрактом</div>';
    }
    return $errors;
}

function restoreValues() {
    $values = array();
    $values['name'] = empty($_COOKIE['name_value']) ? '' : strip_tags($_COOKIE['name_value']);
    $values['phone'] = empty($_COOKIE['phone_value']) ? '' : strip_tags($_COOKIE['phone_value']);
    $values['email'] = empty($_COOKIE['email_value']) ? '' : strip_tags($_COOKIE['email_value']);
    $values['birthdate'] = empty($_COOKIE['birthdate_value']) ? '' : strip_tags($_COOKIE['birthdate_value']);
    $values['gender'] = empty($_COOKIE['gender_value']) ? '' : strip_tags($_COOKIE['gender_value']);
    $plValues = json_decode(empty($_COOKIE['programmingLanguage_value']) ? '' : strip_tags($_COOKIE['programmingLanguage_value']));
    if (!empty($plValues)) {
        foreach ($plValues as &$plValue) {
            $plValue = strip_tags($plValue);
        }
    }
    $values['programmingLanguage'] = $plValues;
    $values['biography'] = empty($_COOKIE['biography_value']) ? '' : strip_tags($_COOKIE['biography_value']);
    $values['agreement'] = empty($_COOKIE['agreement_value']) ? '' : strip_tags($_COOKIE['agreement_value']);

    return $values;
}

function testForErrors($db) {
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

    $validOptions = findAllLanguages($db);

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
    return $errors;
}