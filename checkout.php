<?php

include 'components/connect.php';

session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   $user_id = '';
   header('location:user_login.php');
};

if(isset($_POST['order'])){

   $name = $_POST['name'];
   $number = $_POST['number'];
   $email = $_POST['email'];
   $method = $_POST['method'];
   $address = 'flat no. '. $_POST['flat'] .', '. $_POST['street'] .', '. $_POST['city'] .', '. $_POST['state'] .', '. $_POST['country'] .' - '. $_POST['pin_code'];
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];
   $country=$_POST['country'];
   $_SESSION['country'] = $country;

   $tariff=$conn->prepare("SELECT tariff FROM tariff WHERE country=?");
   $tariff->execute([$country]);
   $tariffs=$tariff->fetch(PDO::FETCH_ASSOC);

   $check_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0)
   {
      while($row = $check_cart->fetch(PDO::FETCH_ASSOC))
      {

         $weight=$conn->prepare("SELECT weight FROM products WHERE pid =?");
         $weight->execute([$row['pid']]);
         $weights = $weight->fetch(PDO::FETCH_ASSOC);
         
         $pname=$conn->prepare("SELECT name FROM products WHERE pid =?");
         $pname->execute([$row['pid']]);
         $pnames=$pname->fetch(PDO::FETCH_ASSOC);
         $delivery=$weights['weight']*$tariffs['tariff'];
         $total_price=$row['price']*$row['quantity']+$weights['weight']*$tariffs['tariff'];
         $insert_order = $conn->prepare("INSERT INTO orders( user_id, pid, pname, name, number, email, method, address, quantity, price,delivery_charge, total_price) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
         $insert_order->execute([$user_id, $row['pid'], $pnames['name'], $name, $number, $email, $method, $address,$row['quantity'],$row['price'],$delivery, $total_price]);
         
      }
      $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'order placed successfully!';
      // Redirect to invoice.php
      header("Location: invoice.php");
      exit; // Make sure to exit to prevent further script execution
   }else{
      $message[] = 'your cart is empty';
   }

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'components/user_header.php'; ?>

<section class="checkout-orders">

   <form action="" method="POST">

   <h3>your orders</h3>

      <div class="display-orders">
      <?php
         $grand_total = 0;
         $cart_items[] = '';
         $select_cart = $conn->prepare("SELECT * FROM cart WHERE user_id = ?");
         $select_cart->execute([$user_id]);
         if($select_cart->rowCount() > 0){
            while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
               $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
               $total_products = implode($cart_items);
               $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
      ?>
         <p> <?= $fetch_cart['name']; ?> <span>(<?= 'Rs '.$fetch_cart['price'].'/- x '. $fetch_cart['quantity']; ?>)</span> </p>
      <?php
            }
         }else{
            echo '<p class="empty">your cart is empty!</p>';
         }
      ?>
         <input type="hidden" name="total_products" value="<?= $total_products; ?>">
         <input type="hidden" name="total_price" value="<?= $grand_total; ?>" value="">
         <div class="grand-total">Grand Total : <span>Rs <?= $grand_total; ?>/-</span></div>
      </div>

      <h3>place your orders</h3>

      <div class="flex">
         <div class="inputBox">
            <span>Name: </span>
            <input type="text" name="name" placeholder="Enter your name" class="box" maxlength="20" required>
         </div>
         <div class="inputBox">
            <span>Number: </span>
            <input type="number" name="number" placeholder="Enter your number" class="box" min="0" max="9999999999" onkeypress="if(this.value.length == 10) return false;" required>
         </div>
         <div class="inputBox">
            <span>Email: </span>
            <input type="email" name="email" placeholder="Enter your email" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Payment Method :</span>
            <select name="method" class="box" required>
               <option value="cash on delivery">Cash on Delivery</option>
               <option value="credit card">Credit Card</option>
               <option value="paytm">Paytm</option>
               <option value="paypal">Paypal</option>
            </select>
         </div>
         <div class="inputBox">
            <span>Address Line 1: </span>
            <input type="text" name="flat" placeholder="e.g. Flat Number" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Address Line 2: </span>
            <input type="text" name="street" placeholder="e.g. Street Name" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>City: </span>
            <input type="text" name="city" class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>State: </span>
            <input type="text" name="state"  class="box" maxlength="50" required>
         </div>
         <div class="inputBox">
            <span>Country: </span>
            <select name="country" class="box" required>
               <option value="Australia">Australia</option>
               <option value="Canada">Canada</option>
               <option value="China">China</option>
               <option value="France">France</option>
               <option value="Ireland">Ireland</option>
               <option value="Japan">Japan</option>
               <option value="New Zealand">New Zealand</option>
               <option value="UAE">United Arab Emirates (UAE)</option>
               <option value="UK">United Kingdom (UK)</option>
               <option value="USA">United States of America (USA)</option>
            </select>
         </div>

         <div class="inputBox">
            <span>Pin Code: </span>
            <input type="number" min="0" name="pin_code" min="0" max="999999" onkeypress="if(this.value.length == 6) return false;" class="box" required>
         </div>
      </div>

      <input type="submit" name="order" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>" value="place order">

   </form>


</section>


<?php include 'components/footer.php'; ?>

<script src="js/script.js"></script>

</body>
</html>