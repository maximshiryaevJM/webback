<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Администратор</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <div class="row">
        <div class="col">
            <?php
            foreach ($users as $user) {
                echo '<h5> Введенные данные с id ' .$user['id']. ' пользователя ' .$user['name']. '</h5>';
                echo '<form class="form-inline" method="post" action="">';
                echo '<input type="hidden" name="id" value="' . $user['id'] . '">';
                echo '<div class="form-group mr-2">';
                echo '<label for="name"ФИО:</label>';
                echo '<input type="text" class="form-control" name="name" placeholder="Enter Name" value="' . $user['name'] . '">';
                echo '</div>';
                echo '<div class="form-group mr-2">';
                echo '<label for="email">E-mail:</label>';
                echo '<input type="email" class="form-control" name="email" placeholder="Enter Email" value="' . $user['email'] . '">';
                echo '</div>';
                echo '<div class="form-group mr-2">';
                echo '<label for="phone">Телефон:</label>';
                echo '<input type="text" class="form-control" name="phone" placeholder="Enter Phone Number" value="' . $user['phone'] . '">';
                echo '</div>';
                echo '<div class="form-group mr-2">';
                echo '<label for="birthdate">Дата рождения:</label>';
                echo '<input type="date" class="form-control" name="birthdate" value="' . $user['birth_date'] . '">';
                echo '</div>';
                echo '<div class="form-group mr-2">';
                echo '<label for="gender">Пол:</label>';
                echo '<select class="form-control" name="gender">';
                echo '<option value="Male" ' . ($user['gender'] === 'male' ? 'selected' : '') . '>Male</option>';
                echo '<option value="Женский" ' . ($user['gender'] === 'female' ? 'selected' : '') . '>Female</option>';
                echo '</select>';
                echo '</div>';
                echo '<div class="form-group mr-2">';
                echo '<label for="biography">Биография:</label>';
                echo '<textarea class="form-control" name="biography" rows="3">' . $user['biography'] . '</textarea>';
                echo '</div>';
                echo '<div class="form-group mr-2">';
                echo '<label for="languages">Любимый язык программирования</label><br>';
                echo '<select multiple class="form-control" name="languages[]">';
                echo json_encode($languages[$user['user_id']]);
                foreach ($validLanguages as $language) {
                    echo '<option value="' . $language . '" ' . (in_array($language, $languages[$user['user_id']]) ? 'selected' : '') . '>' . $language . '</option>';
                }
                echo '</select>';
                echo '</div>';
                echo '<button type="submit" class="btn btn-primary mr-2">Сохранить</button>';
                echo '</form>';
                echo '<form class="form-inline" action="" method="post">';
                echo '<input type="hidden" name="id" value="' . $user['id'] . '">';
                echo '<input type="hidden" name="action" value="delete">';
                echo '<button type="submit" class="btn btn-danger">Удалить</button>';
                echo '</form>';
                echo '<hr>';
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>