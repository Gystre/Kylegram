<?php
session_start();

if(!isset($_SESSION['name'])){
    header("Location: /");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://gystre.github.io/assets/favicon.ico" type="image/ico" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <title>Kylegram</title>
</head>
<body>
    <?php echo "heyyyy " . $_SESSION['name']; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="submit" name="logout" value="Logout">
        <br>
        <label for="userfile">Select an image: </label>
        <input id="upload" name="userfile" type="file" value="" accept=".png, .jpeg, .jpg, .gif, .mp4, .mov">
        <br>
        <textarea name="description" placeholder="Description (max 255 chars)" style="width: 500px;"></textarea>
        <br>
        Nothing over 8 megabytes pls, and no porn!!!!!
        <span id="error" style="color: red;"></span>
        <br>
        <input id="submitfile" type="submit" name="upload" value="Upload">
    </form>

    <script>
        $("#upload").on("change", function(e){
            var file = e.currentTarget.files[0];
            var filesize = ((file.size/1024)/1024).toFixed(4); // MB
            if(filesize <= 8){
                $("#error").text("");
                $("#submitfile").prop('disabled', false);
            }else{
                $("#error").text("File is too big");
                $("#submitfile").prop('disabled', true);
            }
        });
    </script>

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
        
        $validExtensions = array("jpg", "png", "gif", "jpeg", "mp4", "mov");
        if(isset($_FILES['userfile'])){
            echo "uploading...";
            $file = $_FILES['userfile'];
            if($file['error']){
                echo $file['name'] . " - " . $phpFileUploadErrors[$file["error"]];
            }else if(strlen($_POST['description']) >= 255){
                echo "The description is longer than 255 chars";
            }else{
                $fileInfo = explode(".", $file['name']); //[0] = name, [1] = extension
                $name = $fileInfo[0];
                $extension = end($fileInfo);

                if(!in_array($extension, $validExtensions)){
                    echo "{$file['name']} - Invalid file extension";
                }else{
                    $encodedName = uniqid();
                    $movDir = "../landing/images/" . $encodedName . "." . $extension;
                    move_uploaded_file($file['tmp_name'], $movDir);
                    $movDir = str_replace("../landing/", "", $movDir);
                    $desc = htmlspecialchars($_POST['description']);
                    
                    //adding to image table and creating new table for image comments
                    $stmt = mysqli_prepare($link, "insert into images (author, name, encodedname, description, imgdir, created) values (?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssssss", $_SESSION['name'], $name, $encodedName, $desc, $movDir, date("Y-m-d H:i:s"));
                    $stmt->execute();
                    $stmt->close();

                    header("Location: /landing");
                }
            }
        }
    ?>
</body>
</html>