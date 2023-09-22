<?php

include 'd:\xampp\htdocs\DNK\components\connect.php';

session_start();

if(isset($_SESSION['user_id'])){
    $user_id = $_SESSION['user_id'];
 }else{
    $user_id = '';
 };

 if(isset($_POST['submit']))
{

    $ID = $_POST['consignment_no'];
    $select_post = $conn->prepare("SELECT postname,postaddress FROM `post_offices` WHERE postid IN(SELECT  postid FROM `tracking` WHERE orderid=?)");
    $select_post->execute([$ID]);
    if($select_post->rowCount() > 0)
{
    ?>
    <table border="1">
      <tr>
      <th>Post name</th>
      <th>Post address</address></th>
    </tr>
    <?php
   while($row = $select_post->fetch(PDO::FETCH_ASSOC))
   {
      
?>
    <tr>
       <td><?php echo $row['postname']; ?></td>
       <td><?php echo $row['postaddress']; ?></td>
    </tr>
     </table>
<?php
    }

} else {

?>

<p>Invalid consignment No.</p>

<?php

}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Business Profile</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="css/style.css">

</head>
<body>
<section class="form-container">

    <form action="" method="post">
        <h3>TRACKING</h3>
        <div class="inputBox">
            <span>your number :</span>
            <input type="number" name="consignment_no" placeholder="enter your number" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" required>
        </div>
            <input type="submit" value="Submit" class="btn" name="submit">
    </form>
</section>
   
</body>
</html>