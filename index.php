<!doctype html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HTML+CSS+PHP+SQL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-gH2yIJqKdNHPEq0n4Mqa/HGKIhSkIHeL5AyhkYV8i59U5AR6csBvApHHNl/vI1Bx" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="body2">
    <?php

    session_start();
    if (!isset($_SESSION['counter'])) {
        $_SESSION['counter'] = -1;
    }
    ?>


    <nav class="navbar bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="/img/test.jpg" alt="тут картинка" width="100px" class="d-inline-block align-text-top">
                Тестирование по общим вопросам
            </a>
        </div>
    </nav>

    <?php
    $start_page = '<form action="" method="post">
        <div class="input-group mb-3">
            <span class="input-group-text" id="basic-addon1">Введите ваше имя</span>
            <input type="text" class="form-control" placeholder="Имя пользователя" aria-label="Имя пользователя" aria-describedby="basic-addon1" name="name" required>
        </div>

        <div  class="d-grid gap-2 mb" >
         <button type="submit" class="btn btn-primary" name="start_test">Начать тестирование</button>
        </div>

    </form>';


    if ($_SESSION['count_right'] == 1) {
        $question_text = "вопрос";
    } elseif (($_SESSION['count_right'] == 2) or ($_SESSION['count_right'] == 3) or ($_SESSION['count_right'] == 4)) {
        $question_text = "вопроса";
    } else {
        $question_text = "вопросов";
    };


    $end_page = get_card("smyle.jpg", $_SESSION["user_name"], "А ВОТ И ВСЕ! Поздравляю!", " Вы ответили на " . $_SESSION['count_right'] . "  " . $question_text . " из 10", "", "", "", "", "");

    if ($_SESSION['counter'] == -1) echo $start_page;

    if ($_SESSION['counter'] == 10) echo $end_page;
    ?>

    <?php
    if (($_SESSION['counter'] >= 0) && ($_SESSION['counter'] < 10)) {

        $host = "localhost";
        $user = "root";
        $password = "";
        $database = "quest";
        $port = 3306;
        $connect = new mysqli($host, $user, $password, $database, $port);
        $connect->set_charset("utf8mb4");


        if ($connect->connect_error) {
            die("Ошибка: " . $connect->connect_error);
        } else {
            $sql = "SELECT * FROM questions WHERE id=" . $_SESSION['counter'];
            if ($result = $connect->query($sql)) {


                $row = mysqli_fetch_array($result);

                $id = $row["id"];
                $quest_number = $row["quest_number"];
                $_SESSION['quest_number'] = $quest_number;
                $quest_text = $row["quest_text"];
                $answer_1 = $row["answer_1"];
                $answer_2 = $row["answer_2"];
                $answer_3 = $row["answer_3"];
                $answer_4 = $row["answer_4"];
                $_SESSION['correct_answer'] = $row["correct_answer"];
                $picture = $row["picture"];
                $correct_answer_text = $row["correct_answer_text"];



                if ($_SESSION['quest_page']) {
                    $quest_page = get_card($picture, $_SESSION["user_name"], $quest_number, $quest_text, $answer_1, $answer_2, $answer_3, $answer_4, "");
                    echo $quest_page;
                } else {
                    if ($_SESSION['right']) {
                        $right_page = get_card($picture, $_SESSION["user_name"], '<samp style="color: green;">ВЕРНО</samp>', $correct_answer_text, "", "", "", "", "Следующий вопрос");
                        echo $right_page;
                    } else {
                        $wrong_page = get_card($picture, $_SESSION["user_name"], '<samp style="color: red;">НЕ ВЕРНО</samp>', $correct_answer_text, "", "", "", "", "Следующий вопрос");
                        echo $wrong_page;
                    }
                }
            };
        };

        $connect->close();
    }

    ?>

    <?php

    function get_card($picture, $user_name, $quest_number, $quest_text, $answer_1, $answer_2, $answer_3, $answer_4, $botton_next)

    {
        $card = '<form action="" method="post">
    <div class="card">
    <img src="img/' . $picture . '" class="card-img-top" alt="картинка quest"   width="90" ;>
    <div class="card-body">
        <h5 class="card-title">' . $user_name . '! ' . $quest_number . '</h5>
        <p class="card-text">' . $quest_text . '</p>';

        if ($answer_1 <> "") {
            $answer_1 = ' <div class="d-grid gap-2 mb">
                          <button type="submit" class="btn btn-primary" name="answer_1">' . $answer_1 . '</button></div>';
        };

        if ($answer_2 <> "") {
            $answer_2 = '<div class="d-grid gap-2 mb">
                         <button type="submit" class="btn btn-primary" name="answer_2">' . $answer_2 . '</button></div>';
        };

        if ($answer_3 <> "") {
            $answer_3 = '<div class="d-grid gap-2 mb">
            <button type="submit" class="btn btn-primary" name="answer_3">' . $answer_3 . '</button></div>';
        };

        if ($answer_4 <> "") {
            $answer_4 = '<div class="d-grid gap-2 mb">
            <button type="submit" class="btn btn-primary" name="answer_4">' . $answer_4 . '</button></div>';
        };

        if ($botton_next <> "") {
            $botton_next = '<div class="d-grid gap-2 mb">
            <button type="submit" class="btn btn-primary" name="botton_next">' . $botton_next . '</button></div>';
        };

        return  $card . $answer_1 . $answer_2 . $answer_3 . $answer_4 . $botton_next . '</div></div> </form>';
    }
    ?>

    <?php //Обработка ответов

    if (isset($_POST["answer_1"])) {
        echo $_SESSION['correct_answer'];
        if ($_SESSION['correct_answer'] == 1) {
            right();
        } else {
            wrong();
        };
    };

    if (isset($_POST["answer_2"])) {
        if ($_SESSION['correct_answer'] == 2) {
            right();
        } else {
            wrong();
        };
    };

    if (isset($_POST["answer_3"])) {
        if ($_SESSION['correct_answer'] == 3) {
            right();
        } else {
            wrong();
        };
    };

    if (isset($_POST["answer_4"])) {
        if ($_SESSION['correct_answer'] == 4) {
            right();
        } else {
            wrong();
        };
    };

    function right()
    {
        add_db("Верно");
        $_SESSION['quest_page'] = false;
        $_SESSION['right'] = true;
        header("Refresh:0");
        $_SESSION['count_right']++;
    }

    function wrong()
    {
        add_db("Не верно");
        $_SESSION['quest_page'] = false;
        $_SESSION['right'] = false;
        header("Refresh:0");
    }

    function add_db($answer)
    {

        $host = "localhost";
        $user = "root";
        $password = "";
        $database = "quest";
        $port = 3306;
        $connect = new mysqli($host, $user, $password, $database, $port);
        $connect->set_charset("utf8mb4");

        $name = $_SESSION['user_name'];
        $quest_number = $_SESSION['quest_number'];


        if ($connect->connect_error) {
            die("Ошибка: " . $connect->connect_error);
        } else {
            $sql = "INSERT INTO user_responses (name, quest,answer) VALUES ('$name',' $quest_number','$answer')";;
            ($connect->query($sql));
        };

        $connect->close();
    }

    ?>

    <?php

    if (isset($_POST["start_test"])) { //Обработка кнопки начать тест
        $_SESSION['user_name'] = $_POST["name"];
        $_SESSION['counter']++;
        $_SESSION['quest_page'] = true;
        $_SESSION['count_right'] = 0;
        header("Refresh:0");
    }
    ?>

    <form action="" method="post">
        <div class="d-grid gap-2 mb">
            <button type="submit" class="btn btn-primary" name="stop">Остановить тестирование</button>
        </div>
    </form>

    <?php
    if (isset($_POST["stop"])) { //Обработка кнопки остановить тестирование
        $_SESSION['counter'] = -1;
        $_SESSION['user_name'] = '';
        header("Refresh:0");
    }

    if (isset($_POST["botton_next"])) { //Обработка кнопки следующий вопрос
        $_SESSION['counter']++;
        $_SESSION['quest_page'] = true;
        header("Refresh:0");
    }
    ?>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-A3rJD856KowSb7dwlZdYEkO39Gagi7vIsF0jrRAoQmDKKtQBHUuLZ9AsSv4jD4Xa" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.5/dist/umd/popper.min.js" integrity="sha384-Xe+8cL9oJa6tN/veChSP7q+mnSPaj5Bcu9mPX5F5xIGE0DVittaqT5lorf0EI7Vk" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.min.js" integrity="sha384-ODmDIVzN+pFdexxHEHFBQH3/9/vQ9uori45z4JjnFsRydbmQbmL5t1tQ0culUzyK" crossorigin="anonymous"></script>
</body>

</html>