<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kylegram</title>
</head>
<body>
    <h1>Welcome to Kylegram!</h1>
    <form method="post"> 
        <input type="text" placeholder="User Name" value="<?php echo htmlspecialchars(isset($_POST['name'])?$_POST['name']:''); ?>" name="name"> <span style="color: red;">* (4-15 characters)</span>
        <br>
        <input type="password" name="password" placeholder="Password"> <span style="color: red;">* (5-24 characters)</span>
        <br>
        <input type="submit" name="login" value="Login">
        <input type="submit" name="register" value="Register">
    </form>

    <?php
        $link = mysqli_connect("localhost", "root", "", "kylegram");

        if(isset($_POST['register'])){
            if(empty($_POST['name']) || empty($_POST['password'])){
                echo "pls enter ur username and password";
            }else if(strlen($_POST['name']) < 4 || strlen($_POST['name']) > 15){
                echo "choose a username between 4 and 15 characters pls";
            }else if(strlen($_POST['password']) < 5 || strlen($_POST['password']) > 24){
                echo "choose a password between 5 and 24 characters pls";
            }else{
                $name = $_POST['name'];
                $dupes = $link->query("select * from accounts where (name = '$name')");
                if(mysqli_num_rows($dupes) == 0){
                    $hash = password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 13]);
                    $link->query("insert into accounts (name, password) values ('$name', '$hash')");
                    echo "thanks for registering " . $name . "!";
                }else{
                    echo "there is already someone registered with that name";
                }
            }
        }

        if(isset($_POST['login'])){
            if(empty($_POST['name']) || empty($_POST['password'])){
                echo "pls enter ur username and password";
            }else{
                $name = $_POST['name'];
                $password = $_POST['password'];
                $res = $link->query("select * from accounts where (name = '$name')");
                $exists = mysqli_num_rows($res);
                if($exists == 1){
                    $row = $res->fetch_assoc();
                    if(password_verify($password, $row['password'])){
                        $_SESSION['name'] = $name;
                        $ip = $_SERVER["REMOTE_ADDR"];
                        $link->query("update accounts set loggedin = 1, ip = '$ip' where (name = '$name')");
                        header("Location: landing");
                        die();
                    }else{
                        echo "ur username and/or password is incorrect";
                    }
                }else if($exists == 0){
                    echo "ur username and/or password is incorrect";
                }
            }
        }
    ?>
</body>
</html>