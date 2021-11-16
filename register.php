<?php
include_once "include/dbconnect.php";
include_once "include/functions-2.php";
?>
<?php
if (isset($_POST['submit'])) {
  //include'include/dbconnect.php';
  // Get the data from the form
  $email = $_POST["email"];
  $email = strip_tags(trim(mysqli_real_escape_string($conn, $email)));
  $password = $_POST["password"];
  $password = strip_tags(trim(mysqli_real_escape_string($conn, $password)));
    
  $hashemail = password_hash(strip_tags(trim(mysqli_real_escape_string($conn, $email))),PASSWORD_DEFAULT); 
  $passhash = password_hash(strip_tags(trim(mysqli_real_escape_string($conn, $password))), PASSWORD_DEFAULT);
  $sql = "INSERT INTO website_user (email,password) VALUES('$hashemail','$passhash')";
  $result = mysqli_query($conn, $sql);
  if($result){
      echo "Successful Registered";
  }else{
    echo "Not Register Some Error : ";
  }
//   $sql = "SELECT * FROM website_user WHERE email='$email'";
//   $result = mysqli_query($conn, $sql); //fire query to the mysql DB
//   $count = mysqli_num_rows($result);
//   $row = mysqli_fetch_assoc($result);
//   if ($count > 0) {
//     if ($email == $user['email']) {
//       if (password_verify($password, $row['password'])) {
//         $npassword = $_POST['npassword'];
//         $hashpassword = password_hash(strip_tags(trim(mysqli_real_escape_string($conn, $npassword))), PASSWORD_DEFAULT);
//         $cpassword = $_POST['cpassword'];
//         if (($npassword == $cpassword) && $npassword != '') //new password and confirm password are same/ then update the old password with newone
//         {
//           $sql =  "UPDATE website_user SET `password` = '$hashpassword' WHERE email='$email'";
//           $result = mysqli_query($conn, $sql); //fire query to the mysql DB
//           if ($result) {
//             echo '<div class="alert alert-success text-center"><strong> Password Updated Successfully...</strong></div>';
//             header("refresh:3;url=dashboard.php");
//             //header("Location: index.php");
//           }
//         } else {
//           echo '<div class="alert alert-success text-center"><strong> New Password & Confirm New Password are not Same...</strong></div>';
//           header("refresh:3;url=change-password.php");
//         }
//         //header("Location: change-password.php");
//       } else {
//         echo '<div class="alert alert-success text-center"><strong> Registered Old Password is Not Correct...</strong></div>';
//         header("refresh:3;url=change-password.php");
//       }
//     } else {
//       echo '<div class="alert alert-success text-center"><strong> Please Enter Only You Email and Password...</strong></div>';
//       header("refresh:3;url=change-password.php");
//     }
//   } else {
//     echo '<div class="alert alert-success text-center"><strong> Registered Email is Not Correct...</strong></div>';
//     header("refresh:3;url=change-password.php");
//     //header("Location: change-password.php");
//   }
}
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-F3w7mX95PdgyTmZZMECAngseQB83DfGTowi0iMjiWaeVhAn4FJkqJByhZMI3AhiU" crossorigin="anonymous">

    <title>Hello, world!</title>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center  align-items-center ">
            <div class="col-6 mt-5">
                <form action="" method="POST" role="form">
                    <div class="row mb-3">
                        <label for="inputEmail3" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="email" name="email" class="form-control" id="inputEmail3">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label for="inputPassword3" class="col-sm-2 col-form-label">Password</label>
                        <div class="col-sm-10">
                            <input type="text" name="password" class="form-control" id="inputPassword3">
                        </div>
                    </div>
                    <?php echo generateStrongPassword()?>
                    <button type="submit" name="submit" class="btn btn-primary">Sign in</button>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-/bQdsTh/da6pkI1MST/rWKFNjaCP5gBSY4sEBT38Q/9RBh9AH40zEOg7Hlq2THRZ" crossorigin="anonymous"></script>
</body>

</html>