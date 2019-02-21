$(document).ready(function () {
        //Функция exit(), ссылка "Выход" для разрушения текущей сессии
        function exit() {
            $('#exit').css('visibility', 'visible');
            $('#exit').on('click', function () {
                $.ajax({
                    url: 'php/logout.php',
                })
            });
        }

        //index- это ajax-объект который запускается при запуске или перезагрузке страницы index.html
        var index = $.ajax({
            url: 'php/form.php'//Запрос к файлу form.php
        });
        index.done(function (data) {
            try {
                JSON.parse(data);/*При возврате результата data из form. php проверяем или он к нам пришёл в json-формате,*/
            } catch (e) {        /*если нет, то отлавливаем ошибку и возращаем результат в dom-элемент (форма авторизации)*/
                return $('.login').html(data);
            }
            var result = $.parseJSON(data);
            if (result['status'] == 'success') {
                $('.login').html("Hello " + result['login']);// В случае success работаем с данными, если они в виде json-формата(трансформировали его в js-объект),и вывели элемент с клюxом 'login';
                exit();
            }
        });
        index.then(function () { //Чтобы точно убедиться что форма успела загрузиться после ajax index, мы используем метод then, и только после этого будет работать эта часть кода.
            $('#subLog').on('click', function (e) {
                e.preventDefault();//Отменили действие по умолчанию для submit;
                var login = $.ajax({
                    type: 'post',
                    data: $('#loginForm').serialize(),//собрали данные с формы с id=loginForm (авторизация) и отправляем на login.php
                    url: "php/login.php",
                });
                login.done(function (data) {
                    var result = $.parseJSON(data);
                    if (result['status'] == 'success') {
                        $('.login').html("Hello " + result['login'])//В случае возврата success выводим ссылку Выход и приветствие для юзера;
                        exit();
                        $('#loginForm').remove();//Удаляем форму авторизации
                        $('.info').remove();
                    } else {
                        $('.info').html(result['message'])//В случае неудачи выведим сообщение message;
                    }

                })
            });
        });

        $('#subReg').on('click', function (e) {//Работа с формой регистрации
            e.preventDefault();
            var reg = $.ajax({
                type: 'post',
                url: "php/registration.php",
                data: $('#registr').serialize(),
            });
            reg.done(function (data) {
                var result = $.parseJSON(data);
                if (result['status'] == 'success') {
                    $('#registr').remove();
                }
                $('.info').html(result['message'])
            })

        });
    }
);


