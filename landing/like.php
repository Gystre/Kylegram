<?php
    session_start(); 
    $link = mysqli_connect("localhost", "root", "", "kylegram");

    if(isset($_SESSION['name'])){
        $tablename = "likes" . $_POST['image'];
        //check if table exists for likes
        $res = $link->query("select 1 from information_schema.tables where table_schema='kylegram' and table_name='$tablename' limit 1");
        $exists = mysqli_num_rows($res);
        if($exists != 1){
            $link->query("create table `" . $tablename . "` (`name` varchar(15) not null, `created` timestamp, unique key `name`(`name`))");
        }

        //check if user has already liked the picture
        $likedResults = $link->query("select name from `" . $tablename ."` where name='{$_SESSION['name']}'");
        $userExists = mysqli_num_rows($likedResults);
        if($userExists != 1){
            $stmt = mysqli_prepare($link, "insert into `" . $tablename . "` (name) values(?)");
            $stmt->bind_param("s", $_SESSION['name']);
            $stmt->execute();
            $stmt->close();
    
            $link->query("update images set likes=likes+1 where encodedname='{$_POST['image']}'");

            echo "success";
        }else{
            echo "failure1";
        }
    }else{
        echo "failure2";
    }
?>