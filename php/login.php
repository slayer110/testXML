<?php
session_start();
require_once __DIR__ . '/passFunctions.php';
if (isset($_POST['login'])) {
    $login = $_POST['login'];
    if ($login == '') {
        unset($login);
    }
} //заносим введенный пользователем логин в переменную $login, если он пустой, то уничтожаем переменную
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    if ($password == '') {
        unset($password);
    }
}
//заносим введенный пользователем пароль в переменную $password, если он пустой, то уничтожаем переменную
if (empty($login) or empty($password)) //если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт
{
    $array['status'] = 'error';
    $array['message'] = 'Вы ввели не всю информацию, вернитесь назад и заполните все поля!';
    echo json_encode($array);
    die;
}
//если все данные введены, то обрабатываем их, чтобы теги и скрипты не работали
$login = stripslashes($login);
$login = htmlspecialchars($login);
$login = trim($login);
$password = stripslashes($password);
$password = htmlspecialchars($password);
//удаляем лишние пробелы
$password = trim($password);
$smpl_xml = simplexml_load_file('../bd/users.xml');
$count = 0;
$array = [];
foreach ($smpl_xml as $user) {
    if ($user == $login) {
        if (confirmPassword($user['password'], $user['salt'], $password)) {
            $_SESSION['auth'] = true;
            $array['status'] = 'success';
            $_SESSION['login'] = (string)$user;
            $array['login'] = (string)$user;
            $salt = generateSalt();// Получаем соль
            $key = hashPassword($salt, (string)$user);//засаливаем куку с логином пользователя
            $user['cookies'] = $key;//Записываем куку в бд для юзера;
            file_put_contents('../bd/users.xml', $smpl_xml->asXML());
            //Пишем куки (имя куки, значение, время жизни - сейчас+месяц)
            setcookie('login', $array['login'], time() + 60 * 60 * 24 * 30); //логин
            setcookie('key', $key, time() + 60 * 60 * 24 * 30); //случайная строка
            break;
        } else {
            $array['status'] = 'error';
            $array['message'] = 'Неверный пароль';
            break;
        }
    } else {
        if ($count == count($smpl_xml) - 1) {
            $array['status'] = 'error';
            $array['message'] = 'Извините, введённый вами login или пароль неверный.';
        }
        $count++;
    }
}
echo json_encode($array);