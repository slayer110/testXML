<?php
//Функции для хэширования пароля и кук
function confirmPassword($hash, $salt, $password)
{
    return hashPassword($salt, $password) == $hash;
}

function hashPassword($salt, $password)
{
    return md5($salt . $password);
}

function generateSalt()
{
    return substr(md5(uniqid('some_prefix', true)), 1, 10);}

