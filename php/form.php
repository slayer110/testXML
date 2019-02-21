<?php
$array = [];
if (empty($_SESSION['auth']) or $_SESSION['auth'] == false) {//Если сессии нет у юзера
    if (!empty($_COOKIE['login']) and !empty($_COOKIE['key'])) {//Если куки непустые в браузере
        //Пишем логин и ключ из КУК в переменные (для удобства работы):
        $login = $_COOKIE['login'];
        $key = $_COOKIE['key']; //ключ из кук (аналог пароля, в базе атрибут сookies)
        /*

           выбираем из файла запись, где login=$user, и где куки в браузере равны кукам в бд, если находим
        соответствие запускаем сессию для это юзера.
        */
        $smpl_xml = simplexml_load_file('../bd/users.xml');
        foreach ($smpl_xml as $user) {
            if ((string)$user == $login && $user['cookies'] == $key) {
                session_start();
                $_SESSION['auth'] = true;
                $_SESSION['login'] = (string)$user;
                break;
            }

        }
        $array['login'] = $_SESSION['login'];
        $array['status'] = 'success';
        echo json_encode($array);

    } else {
        echo '<p class="auth">Авторизация</p>
<form id="loginForm" method="post" action="">
            <label>Логин<p><input type="text" name="login" id="login"/></p></label>
            <label>Пароль<p><input type="password" name="password" id="password"/></p></label>
            <input name="submit" type="submit" id="subLog" value="Войти">
            </p>
        </form>';
    }
} else {
    $array['login'] = $_SESSION['login'];
    $array['status'] = 'success';
    echo json_encode($array);
}