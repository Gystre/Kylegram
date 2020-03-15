<?php
session_start();

if(!isset($_SESSION['name'])){
    header("Location: /");
}

echo "heyyyy " . $_SESSION['name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kylegram</title>
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        <input type="submit" name="logout" value="Logout">
    </form>

    <button><a href="/upload">Upload something!</a></button>

    <?php 
        $link = mysqli_connect("localhost", "root", "", "kylegram");

        if(isset($_POST["logout"])){
            $name = $_SESSION['name'];
            $link->query("update accounts set loggedin = 0, ip = '' where (name = '$name')");
            session_destroy();
            $_SESSION = array();
            header("Location: /");
        }

        $result = $link->query("select * from images order by created desc") or die($link->error);
        while($data = $result->fetch_assoc()){
            echo "<h3>{$data['author']} on {$data['created']} </h2>";
            echo "<img src='{$data['imgdir']}' width='50%' height='50%'>";
            echo "<p>{$data['description']}</p>";
        }
    ?>
</body>
</html>