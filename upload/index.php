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
        <br>
        <label for="userfile">Select an image: </label>
        <input type="file" name="userfile" value="">
        <br>
        <input type="text" name="description" placeholder="Description (max 255 chars)">
        <br>
        Nothing over 8 megabytes pls, and no porn!!!!!
        <br>
        <input type="submit" name="upload" value="Upload">
    </form>

    <?php
        $link = mysqli_connect("localhost", "root", "", "kylegram");

        if(isset($_POST["logout"])){
            $name = $_SESSION['name'];
            $link->query("update accounts set loggedin = 0, ip = '' where (name = '$name')");
            session_destroy();
            $_SESSION = array();
            header("Location: /");
        }

        $phpFileUploadErrors = array(
            0 => "Success",
            1 => "The file exceeds max file size",
            2 => "The file exceeds the MAX_FILE_SIZE directive that was specified in the html form",
            3 => "The file was only partially uploaded",
            4 => "No file was uploaded",
            5 => "Missing a temporary folder",
            7 => "Failed to write file to disk (I probably ran out of disk space :P)",
            8 => "A PHP extension stopped the file from uploading",
        );
        
        $validExtensions = array("jpg", "png", "gif", "jpeg");
        if(isset($_FILES['userfile'])){
            $file = $_FILES['userfile'];
            if($file['error']){
                ?> <div class="alert alert-error">
                <?php echo $file['name'] . " - " . $phpFileUploadErrors[$file["error"]];
                ?> </div> <?php
            }else if(strlen($_POST['description']) >= 255){
                ?> <div class="alert alert-error">
                <?php echo "The description is longer than 255 chars"
                ?> </div> <?php
            }else{
                $fileInfo = explode(".", $file['name']); //[0] = name, [1] = extension
                $name = $fileInfo[0];
                $extension = end($fileInfo);

                if(!in_array($extension, $validExtensions)){
                    ?> <div class="alert alert-error">
                    <?php echo "{$file['name']} - Invalid file extension";
                    ?> </div> <?php
                }else{
                    $movDir = "../landing/images/" . uniqid() . "." . $extension;
                    move_uploaded_file($file['tmp_name'], $movDir);
                    $movDir = str_replace("../landing/", "", $movDir);
                    $link->query("insert into images (author, name, description, imgdir) values ('{$_SESSION['name']}', '$name', '{$_POST['description']}', '$movDir')");

                    ?> <div class="alert alert-success">
                    <?php echo $file['name'] . " - " . $phpFileUploadErrors[$file["error"]];
                    ?> </div> <?php

                    header("Location: /landing");
                }
                
            }
        }
    ?>
</body>
</html>