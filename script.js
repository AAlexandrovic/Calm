$(document).ready(function(){
    var messages__container = $('#messages');
    var messageInput = $('#message-text');
    var sendForm = $('#chat-form');
    var interval = null;
    var var1 = null;
    var var2 = null;
    var show = 3;
    var send = $('.auth_test').val();

    //Добавление ещё 3 комментариев
    $('.lazy_button').on('click', function (e){
        e.preventDefault();
        // $('.chat__message:hidden').slice(0,3).slideDown();
        var sum = show + 3;
        show = sum;

    });

    function send_request(act, login = null, password = null ) {
        if(act == 'auth') {
            //Если нужно авторизоваться, получаем логин и пароль, которые были переданы в функцию
            var1 = login;
            var2 = password;


        }

        if(act == 'send') {
//Если нужно отправить сообщение, то получаем текст из поля ввода
            if(messageInput.val() == '')
            {
                return false;
            }else {
                var1 = messageInput.val();
            }
        }
        $.post('chat.php',{ //Отправляем переменные
            act: act,
            var1: var1,
            var2: var2
        }).done(function (data) {
            //Заносим в контейнер ответ от сервера
            $('#messages').html(data);
            //messages__container.innerHTML = data;
            if(act == 'send') {
                //Если нужно было отправить сообщение, очищаем поле ввода
                messageInput.val('');
            }
            //Очищаем поля авторизации после ввода
            if(act == 'auth'){
                $('.login').val('');
                $('.password').val('');
            }
            //
            $('.chat__message').slice(0,show).show();
            //убираем окно авторизации
            if ($('.auth_test').length > 0) {
                $('#auth-form').css('display','none');
                $('.exit').css({'display':'block','padding-bottom':'4%'});

            }


        });
    }
    $('#chat-form').on('submit',function (){
        send_request('send');
        return false; //Возвращаем ложь, чтобы остановить классическую отправку формы
    });

    //Отправляем данные авторизации
    $('#auth-form').on('submit',function (){
        var log = $('.login').val();
        var pas = $('.password').val();

        send_request('auth',log,pas);

        return false;
    });


    $('.exit').css({'display':'none'});

    function update() {
        send_request('load');
    }
    setInterval(update,500);
});
