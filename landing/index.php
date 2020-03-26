<?php
session_start();

if(!isset($_SESSION['name'])){
    header("Location: /");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="https://gystre.github.io/assets/favicon.ico" type="image/ico" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Kylegram</title>

    <script>
    function postDelete(filename){
        $.post("delete.php", {image: filename}, function(resp){
            if(resp == "success"){
                $("#" + filename).remove();
                alert("File deleted successfully!");
            }else if(resp == "failure"){
                alert("File failed to delete (something went wrong)");
            }
        });
    }

    function postComment(filename){
        var textContent = $("#" + filename + "comment").val();
        if(textContent.length < 1){
            alert("ur comment is 2 short!");
        }else if(textContent.length > 255){
            alert("ur comment is 2 long");
        }else{
            $.post("comment.php", {image: filename, comment: textContent}, function(resp){
                console.log(resp);
                if(resp == "success"){
                    alert("Comment added successfully");
                }else if(resp == "failure"){
                    alert("Make sure you are logged in before adding a comment or ur comment was too long or 2 short");
                }
            });
        }
    }

    function postLike(filename){
        $.post("like.php", {image: filename}, function(resp){
            if(resp == "success"){

            }else if(resp == "failure1"){
                alert("You already liked this post!")
            }else if(resp == "failure2"){
                alert("You are logged out, so you can't like anything huh durr")
            }
        })
    }
    </script>
</head>
<body>
    <?php echo "heyyyy " . $_SESSION['name']; ?>
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
            echo "<div id='{$data['encodedname']}'>";
            echo "<h3>{$data['author']} on {$data['created']} </h3>";

            //if this image is a video, display as such otherwise just make it an img
            $ext = substr($data['imgdir'], strpos($data['imgdir'], ".") + 1);
            if($ext == "mp4" || $ext == "mov"){
                echo "<video width='75%' height='75%' controls>";
                echo "<source src='{$data['imgdir']}' type='video/mp4'>";  
                echo "</video>";
            }else{
                echo "<img src='{$data['imgdir']}'>";
            }

            echo "<br>{$data['likes']} likes";
            echo "<button style='margin-left: 10px' onclick='postLike(\"{$data['encodedname']}\")'>Like</button>";

            //check if user owns the image or has elevated privleges
            if($_SESSION['op'] == 1 || $_SESSION['name'] == $data['author']){ 
                echo "<div style='margin-left: 10px; display: inline;'></div>";
                echo "<button class='delete' onclick='postDelete(\"{$data['encodedname']}\")'>Delete</button>";
            }
            
            echo "<p>{$data['description']}</p>";

            //check if the comment table exists for this image and if so grab it
            $tablename = "comments" . $data['encodedname'];
            $res = $link->query("select 1 from information_schema.tables where table_schema='kylegram' and table_name='$tablename' limit 1;");
            $exists = mysqli_num_rows($res);
            if($exists == 1){
                $commentQuery = $link->query("select * from `" . $tablename . "` order by created desc");
                if(mysqli_num_rows($commentQuery) >= 1){
                    while($commentData = $commentQuery->fetch_assoc()){
                        echo "<b>" . $commentData['author'] . " on ". $commentData['created'] . ": " . "</b>" . $commentData['comment'] . "<br>";
                    }
                }
            }  

            //The comment text area and comment button
            $textname = $data['encodedname'] . "comment";
            echo "<textarea id='{$textname}' class='commentArea' placeholder='Comment something! (max 255 chars)' style='width: 40%'></textarea>";
            echo "<br><button onclick='postComment(\"{$data['encodedname']}\")'>Comment</button>";
            echo "</div>";
        }
    ?>
</body>
</html>