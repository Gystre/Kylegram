<?php
    session_start(); 
    $link = mysqli_connect("localhost", "root", "", "kylegram");

    $comment = $_POST['comment'];
    if(isset($_SESSION['name']) && strlen($comment) >= 1 && strlen($comment) <= 255){
        $tablename = "comments" . $_POST['image'];
        $res = $link->query("select 1 from information_schema.tables where table_schema='kylegram' and table_name='$tablename' limit 1;");
        $exists = mysqli_num_rows($res);
        if($exists != 1){
            $link->query("create table `" . $tablename . "` (`author` varchar(15) not null, `comment` varchar(255) not null, `created` timestamp)");
        }

        $stmt = mysqli_prepare($link, "insert into `" . $tablename . "` (author, comment) values (?, ?)");
        $stmt->bind_param("ss", $_SESSION['name'], $comment);
        $stmt->execute();
        $stmt->close();

        echo "success";
    }else{ 
        echo "failure";
    }
?>