<?php
session_start();

$link = mysqli_connect('127.0.0.1', 'app', 'fM1oQ4mV', 'app');
if ($link == false)
    print("Ошибка: Невозможно подключиться к MySQL " . mysqli_connect_error());
//} else {
////    print("Соединение установлено успешно") . '<br>';
////}

function load($link) {
    $echo = "";

        $result = mysqli_query($link,"SELECT * FROM newchat"); //Запрашиваем сообщения из базы в обратном порядке
        if($result) {
            if(mysqli_num_rows($result) >= 1) {
                    //$sql = "SELECT * FROM newchat";
                    $sql = "SELECT n.text, na.name, n.author_id FROM newchat n
                        RIGHT JOIN newauthor AS na ON n.author_id = na.id
                        ORDER BY n.id DESC";
    $result = $link->query($sql);
    $row = $result->fetch_assoc();
    foreach($result  as  $rows) {
        $echo .= "<div class='chat__message chat__message'><b>". $rows["name"]." : " . $rows["text"] . "</b></div>"; //Добавляем сообщения в переменную $echo

    }
            } else {
                $echo = "Нет сообщений!";//В базе ноль записей
            }
        }

    return $echo;//Возвращаем результат работы функции
}

function send($link,$message) {
        $id = $_SESSION['id'];
        $message = htmlspecialchars($message);//Заменяем символы ‘<’ и ‘>’на ASCII-код
        $message = trim($message); //Удаляем лишние пробелы
        $message = addslashes($message); //Экранируем запрещенные символы
        $result = mysqli_query($link,"INSERT INTO newchat (text,author_id) VALUES ('$message','$id')");//Заносим сообщение в базу данных

    return load($link); //Вызываем функцию загрузки сообщений
}

function auth($db,$login,$pass) {
    //Находим совпадение в базе данных
    $result = mysqli_query($db,"SELECT * FROM newauthor WHERE name='$login' AND password='$pass'");
    if($result) {
        if(mysqli_num_rows($result) == 1) {//Проверяем, одно ли совпадение
            $user = mysqli_fetch_array($result); //Получаем данные пользователя и заносим их в сессию
//            $_SESSION['login'] = $login;
//            $_SESSION['password'] = $pass;
            $_SESSION['id'] = $user['id'];
            return true; //Возвращаем true, потому что авторизация успешна
        } else {
            unset($_SESSION); //Удаляем все данные из сессии и возвращаем false, если совпадений нет или их больше 1
            return false;
        }
    } else {
        return false; //Возвращаем ложь, если произошла ошибка
    }
}

if(isset($_POST['act'])) {$act = $_POST['act'];}
if(isset($_POST['var1'])) {$var1 = $_POST['var1'];}
if(isset($_POST['var2'])) {$var2 = $_POST['var2'];}
//if($_POST['load']){
//    $echo = load($link);
//}
switch($_POST['act']) {//В зависимости от значения act вызываем разные функции
	case 'load':
		$echo = load($link); //Загружаем сообщения
        if(isset($var1) && isset($var2)) {//Авторизуемся
            if(!empty($_SESSION['id'])) {
                echo "авторизация успешна";
                echo "<input type='hidden' class='auth_test' value='success'>";

            }else{
                echo "Авторизуйтесь чтобы добавить отзыв";
            }
        }

	break;

	case 'send':
		if(isset($var1)) {
            if(empty($_SESSION['id'])){
                echo "Вы неавторизованны";
                return false;
            }else {
                $echo = send($link, $var1); //Отправляем сообщение
            }
        }
	break;

	case 'auth':
		if(isset($var1) && isset($var2)) {//Авторизуемся


            auth($link,$var1,$var2);
        }
	break;
}

echo @$echo;//Выводим результат работы кода
