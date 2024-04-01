<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Форма</title>
    <style>
        /* Сообщения об ошибках и поля с ошибками выводим с красным бордюром. */
        .error {
            border: 2px solid red;
        }
    </style>
</head>
<body>
<?php
if (!empty($messages)) {
    print('<div id="messages">');
    // Выводим все сообщения.
    foreach ($messages as $message) {
        print($message);
    }
    print('</div>');
}
// Далее выводим форму отмечая элементы с ошибками классом error
// и задавая начальные значения элементов ранее сохраненными.
?>
<div class="container mt-5">
    <h1 class="text-center">Форма</h1>
    <form id="myForm" method="post" action="">
        <div class="form-group">
            <label for="name">ФИО:</label>
            <input type="text" class="form-control <?php
            if ($errors['name']) {
                print 'error';
            }
            ?>" value="<?php
            print $values['name'];
            ?>" id="name" name="name">
        </div>

        <div class="form-group">
            <label for="phone">Телефон:</label>
            <input type="tel" class="form-control <?php
            if ($errors['phone']) {
                print 'error';
            }
            ?>" value="<?php
            print $values['phone'];
            ?>" id="phone" name="phone">
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" class="form-control <?php
            if ($errors['email']) {
                print 'error';
            }
            ?>" value="<?php
            print $values['email'];
            ?>" id="email" name="email">
        </div>

        <div class="form-group">
            <label for="birthdate">Дата рождения:</label>
            <input type="date" class="form-control <?php
            if ($errors['birthdate']) {
                print 'error';
            }
            ?>" value="<?php
            print $values['birthdate'];
            ?>" id="birthdate" name="birthdate">
        </div>

        <div class="form-group">
            <label>Пол:</label>
            <div class="check wrapper <?php
            if ($errors['gender']) {
                print 'error';
            }
            ?>">
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="male" name="gender" value="male" <?php
                    if ($values['gender'] == 'male') {
                        print 'checked';
                    }
                    ?>>
                    <label class="form-check-label" for="male">Мужской</label>
                </div>
                <div class="form-check">
                    <input type="radio" class="form-check-input" id="female" name="gender" value="female" <?php
                    if ($values['gender'] == 'female') {
                        print 'checked';
                    }
                    ?>>
                    <label class="form-check-label" for="female">Женский</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="programmingLanguage">Любимый язык программирования:</label>
            <select multiple class="form-control <?php
            if ($errors['programmingLanguage']) {
                print 'error';
            }
            ?>" id="programmingLanguage" name="programmingLanguage[]">
                <?php
                $selected = $values['programmingLanguage'];
                if (!empty($selected)) {
                    foreach ($validOptions as $option) {
                        if (in_array($option, $selected)) {
                            print "<option selected>$option</option>";
                        } else {
                            print "<option>$option</option>";
                        }
                    }
                } else {
                    foreach ($validOptions as $option) {
                        print "<option>$option</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="biography">Биография:</label>
            <textarea class="form-control <?php
            if ($errors['biography']) {
                print 'error';
            }
            ?>" id="biography" name="biography" rows="4"><?php
                print $values['biography']
                ?></textarea>
        </div>

        <div class="form-check <?php
        if ($errors['agreement']) {
            print 'error';
        }
        ?>">
            <input type="checkbox" class="form-check-input" id="agreement" name="agreement" <?php
            if (!empty($values['agreement'])) {
                print 'checked';
            }
            ?>>
            <label class="form-check-label" for="agreement">С контрактом ознакомлен(а)</label>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Сохранить</button>
    </form>
</div>
</body>
</html>