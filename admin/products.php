<?php
include '../components/connect.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

$message = []; // Initialize an array to store messages

if (isset($_POST['add_product'])) {
    $pname = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_INT);
    $details = filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING);
    $weight = filter_input(INPUT_POST, 'weight', FILTER_VALIDATE_INT);

    //Check if any of the required fields are empty or not valid
    if (!$pname || $pname === false || !$price || $price === false || !$details || $details === false || !$weight || $weight === false) {
     $message[] = 'All fields are required and must be valid.';
    } else {
        $image_01 = $_FILES['image_01']['name'];
        $image_size_01 = $_FILES['image_01']['size'];
        $image_tmp_name_01 = $_FILES['image_01']['tmp_name'];
        $image_folder_01 = '../uploaded_img/' . $image_01;

        // Check if product name already exists
        $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
        $select_products->execute([$pname]);

        if ($select_products->rowCount() > 0) {
            $message[] = 'Product name already exists.';
        } else {
            // Insert the new product into the database
            $insert_products = $conn->prepare("INSERT INTO `products` (adminid, name, details, weight, price, image_01) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_products->execute([$admin_id, $pname, $details, $weight, $price, $image_01]);

            if ($insert_products) {
                if ($image_size_01 > 2000000) {
                    $message[] = 'Image size is too large (maximum 2MB each).';
                } else {
                    // Move uploaded images to the destination folder
                    move_uploaded_file($image_tmp_name_01, $image_folder_01);
                    $message[] = 'New product added successfully.';
                }
            }
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Fetch product data for deletion
    $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);

    if ($fetch_delete_image) {
        // Delete product images
        unlink('../uploaded_img/' . $fetch_delete_image['image_01']);

        // Delete product from various tables
        $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
        $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
        $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE pid = ?");

        $delete_product->execute([$delete_id]);
        $delete_cart->execute([$delete_id]);
        $delete_wishlist->execute([$delete_id]);

        header('location:products.php');
        exit(); // Exit to prevent further execution
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="add-products">

   <h1 class="heading">add product</h1>

   <form action="" method="post" enctype="multipart/form-data">
      <div class="flex">
         <div class="inputBox">
            <span>Product Name (required)</span>
            <input type="text" class="box" required maxlength="100" placeholder="Enter Product Name" name="name">
         </div>
         <div class="inputBox">
            <span>Product Price (required)</span>
            <input type="number" min="0" class="box" required max="9999999999" placeholder="Enter Product Price" onkeypress="if(this.value.length == 10) return false;" name="price">
         </div>
         <div class="inputBox">
            <span>Product Weight (required)</span>
            <input type="number" min="0" class="box" required max="9999" placeholder="Enter Product Weight" name="weight">
         </div>
        <div class="inputBox">
            <span>Image1 (required)</span>
            <input type="file" name="image_01" accept="image/jpg, image/jpeg, image/png, image/webp" class="box" required>
        </div>
        
         <div class="inputBox">
            <span>Product Details (required)</span>
            <textarea name="details" placeholder="Enter brief desciption of product" class="box" required maxlength="500" cols="30" rows="10"></textarea>
         </div>
      </div>
      
      <input type="submit" value="add product" class="btn" name="add_product">
   </form>

</section>

<section class="show-products">

   <h1 class="heading">products added</h1>

   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products`");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
   ?>
   <div class="box">
      <img src="../uploaded_img/<?= $fetch_products['image_01']; ?>" alt="">
      <div class="name"><?= $fetch_products['name']; ?></div>
      <div class="weight"><?= $fetch_products['weight']; ?></div>
      <div class="price">Rs <span><?= $fetch_products['price']; ?></span>/-</div>
      <div class="details"><span><?= $fetch_products['details']; ?></span></div>
      <div class="flex-btn">  
         <a href="update_product.php?update=<?= $fetch_products['pid']; ?>" class="option-btn">update</a>
         <a href="products.php?delete=<?= $fetch_products['pid']; ?>" class="delete-btn" onclick="return confirm('delete this product?');">delete</a>
      </div>
   </div>
   <?php
         }
      }else{
         echo '<p class="empty">no products added yet!</p>';
      }
   ?>
   
   </div>

</section>

<script src="../js/admin_script.js"></script>
   
</body>
</html>