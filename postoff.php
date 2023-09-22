<?php

include './components/connect.php';

session_start();



 if(isset($_POST['submit']))
{
    $postid = $_POST['postid'];
    $select_post = $conn->prepare("SELECT postid FROM post_offices WHERE postid =?");
    $select_post->execute([$postid]);
    $orderid = $_POST['orderid'];
    $select_order = $conn->prepare("SELECT id FROM orders WHERE id =?");
    $select_order->execute([$orderid]);
    if($select_post->rowCount()>0 && $select_order->rowCount()>0)
    {
        
        $insert_track = $conn->prepare("INSERT INTO `tracking`(postid,orderid) VALUES (?,?)");
        $insert_track->execute([$postid,$orderid]);
    }
    else
    echo '<p class="empty">INVALID INPUT</p>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Post Office</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
<section class="form-container">

    <form action="" method="post">
        <h3>POST OFFICE</h3>
        <div class="inputBox">
            
            <input type="number" name="postid" placeholder="enter the post office number" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" required>
        </div>
        <div class="inputBox">
            
            <input type="number" name="orderid" placeholder="enter the consignment number" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" required>
        </div>
            <input type="submit" value="Submit" class="btn" name="submit">
    </form>
</section>
   
</body>
</html>











<script src="../js/admin_script.js"></script>
   
</body>
</html>