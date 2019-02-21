<?php
require_once __DIR__ . '/passFunctions.php';
//заносим введенный пользователем логин в переменную $login, если он пустой, то уничтожаем переменную
$array = [];
if (isset($_POST['login'])) {
    $login = $_POST['login'];
    if ($login == '') {
        unset($login);
    }
}
//заносим введенный пользователем пароль в переменную $password, если он пустой, то уничтожаем переменную
if (isset($_POST['password'])) {
    $password = $_POST['password'];
    if ($password == '') {
        unset($password);
    }
}
//заносим введенный пользователем email в переменную $email, если он пустой, то уничтожаем переменную
if (isset($_POST['email'])) {
    $email = $_POST['email'];
    if ($email == '') {
        unset($email);
    }
}
//заносим введенное пользователем имя в переменную $name, если она пустая, то уничтожаем переменную
if (isset($_POST['name'])) {
    $name = $_POST['name'];
    if ($name == '') {
        unset($name);
    }
}
if (empty($login) or empty($password) or empty($email) or empty($name)) //если пользователь не ввел какие-либо данные, то выдаем ошибку и останавливаем скрипт
{
    $array['status'] = 'error';
    $array['message'] = 'Вы ввели не всю информацию, вернитесь назад и заполните все поля!';
    echo json_encode($array);
    die;
}
if (
    $_POST['password'] != $_POST['confirmPass']
) {
    $array['status'] = 'error';
    $array['message'] = 'Пароли не совпадают!';
    echo json_encode($array);
    die;
}
//если все данные введены, то обрабатываем их, чтобы теги и скрипты не работали
$login = stripslashes($login);
$login = htmlspecialchars($login);
$password = stripslashes($password);
$password = htmlspecialchars($password);
//удаляем лишние пробелы
$login = trim($login);
$password = trim($password);
$smpl_xml = simplexml_load_file('../bd/users.xml');
foreach ($smpl_xml as $user) {
    if ($user == $_POST['login']) {
        $array['status'] = 'error';
        $array['message'] = 'Извините, введённый вами логин уже зарегистрирован. Введите другой логин.!';
        echo json_encode($array);
        die;
    }
}
$salt = generateSalt();
$password = hashPassword($salt, $password);
$user = $smpl_xml->addChild('user', $_POST['login']);
$user->addAttribute('password', $password);
$user->addAttribute('email', $_POST['email']);
$user->addAttribute('name', $_POST['name']);
$user->addAttribute('salt', $salt);
file_put_contents('../bd/users.xml', $smpl_xml->asXML());
$array['status']='success';
$array['message'] = 'Поздравляю! Вы успешно зарегистрированы!';
echo json_encode($array);
?>