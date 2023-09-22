<?php

include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(isset($_POST['submit']))
{
   $fname = $_POST['firstname'];
   $lname = $_POST['lastname'];
   $comname = $_POST['companyname'];
   $IEC=$_POST['IECcode'];
   $AD=$_POST['ADcode'];
   $GSTIN=$_POST['GSTIN'];
   $LUT=$_POST['LUTcode'];
   $city=$_POST['city'];
   $state=$_POST['state'];
   $pincode=$_POST['pincode'];
   $address=$_POST['address'];
   $insert_admin = $conn->prepare("INSERT INTO `business_profile`(adminid,fname,lname,companyname,address,city,state,pincode,ieccode,adcode,gstin,lutcode) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
   $insert_admin->execute([$admin_id,$fname,$lname, $comname,$address,$city,$state,$pincode,$IEC,$AD,$GSTIN,$LUT]);
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

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
   
<?php include '../components/admin_header.php'; ?>
<section class="form-container">

   <form action="" method="post">
      <h3>BUSINESS PROFILE</h3>
      <input type="text" name="firstname" required placeholder="enter your first name" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="lastname" required placeholder="enter your last name" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="companyname" required placeholder="enter your company name" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="IECcode" required placeholder="enter your IEC code" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="ADcode" required placeholder="enter your AD code" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="GSTIN" required placeholder="enter your GSTIN" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="LUTcode" required placeholder="enter your LUT code" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="city" required placeholder="enter your city" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="state" required placeholder="enter your state" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="pincode" required placeholder="enter your pincode" maxlength="20"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="text" name="address" required placeholder="enter your address" maxlength="100"  class="box" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="Submit" class="btn" name="submit">
   </form>

</section>
   
</body>
</html>