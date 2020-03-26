<?php
    session_start(); 
    $link = mysqli_connect("localhost", "root", "", "kylegram");

    $res = mysqli_prepare($link, "select author, encodedname from images where encodedname = ?");
    $res->bind_param("s", $_POST['image']);
    $res->execute();
    $res->bind_result($author, $encodedname);
    $res->fetch();
    $res->close();

    //ghetto failsafe to prevent people from deleting other people's posts, why? YOU NEVER KNOWwwww :looking:
    if($_SESSION['name'] == $author || $_SESSION['op'] == 1){
        $deleteStmt = mysqli_prepare($link, "delete from images where encodedname = ?");
        $deleteStmt->bind_param("s", $encodedname);
        $deleteStmt->execute();
        $deleteStmt->close();
        
        $tablename = "comments" . $_POST['image'];
        $tableStmt = mysqli_prepare($link, "drop table `" . $tablename . "`");
        $tableStmt->execute();
        $tableStmt->close();

        echo "success";
    }else{
        echo "failure";
    }
?>