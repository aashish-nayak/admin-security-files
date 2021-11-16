<?php
// Turn off error reporting
error_reporting(0);
session_start();
if (isset($_SESSION['id'])) { //destroy session(login) if user come back to this page after successfully login
  session_unset();
  session_destroy();
}
include_once "include/dbconnect.php";
include_once "include/functions-2.php";
$ip = getIpAddr();
$ltime = time(); //login time
$try = getTry($ip, $conn);
$maxtry = 5; //number of login attempts
$blocktime = 30; //in minutes
$minutes = timediffmiuntes($ip, time(), $conn);
if ($minutes >= $blocktime) { // Number in minutes to block user for a period | reset the try and time stamp with 0 and current time stamp respectivly
  $sql = "UPDATE logintry SET ip='$ip', try=0, ltime='$ltime' where ip='$ip'";
  $result = mysqli_query($conn, $sql);
}
?>
<?php // Check if form was submitted:
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recaptcha_response'])) {
  //Site Key: 6Lc_LKsbAAAAABKKAbFlE9ey26CMomLijvrCsofw
  //Secret Key: 6Lc_LKsbAAAAAKYU85oWouEyyOooKgpmLrX0xES8

  // Build POST request:
  $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
  $recaptcha_secret = $secretkey;
  $recaptcha_response = $_POST['recaptcha_response'];

  // Make and decode POST request:
  $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
  $recaptcha = json_decode($recaptcha);

  // Take action based on the score returned:
  if ($recaptcha->score >= 0.5) { //score near to 1 for human
    // Verified - send email or send to page after successfully login
    if (isset($_POST['submit'])) {
      // Get the data from the form
      $email = $_POST["email"];
      $email = strip_tags(trim(mysqli_real_escape_string($conn, $email)));
      $password = $_POST["password"];
      $password = strip_tags(trim(mysqli_real_escape_string($conn, $password)));
      $sql = "SELECT * FROM website_user";
      $result = mysqli_query($conn, $sql);
      $count = mysqli_num_rows($result);
      if ($count > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          if (password_verify($email, $row['email'])) {
            if (password_verify($password, $row['password'])) {
              $_SESSION['id'] = $session_id;
              $_SESSION['user'] = $row['id'];
              set_timeout(); //for session expire after some time
              echo '<div class="alert alert-success text-center" role="alert"><strong> Login Successful! You are Redirecting to Dashboard...</strong></div>';
              header("refresh:3;url=dashboard.php");
            } else {
              echo '<div class="alert alert-danger text-center" role="alert"><strong> Registered Password is Incorrect...</strong></div>';
              header("refresh:3;url=index.php");
            }
          } else { //login failed
            echo '<div class="alert alert-danger text-center" role="alert"><strong> Registered Email is Incorrect...</strong></div>';
            $findip = findip($ip, $conn); //find ip from talbe $logtry
            if ($findip == 0) { //insert ip and login try if no old ip present and try
              $try = 1;
              $sql = "INSERT INTO logintry (ip,try,ltime) VALUES('$ip', '$try','$ltime')";
              $result = mysqli_query($conn, $sql);
            } else {
              $try = getTry($ip, $conn);
              $try++;
              $sql = "UPDATE logintry SET ip='$ip', try='$try', ltime='$ltime' where ip='$ip'";
              $result = mysqli_query($conn, $sql);
            }
            header("refresh:3;url=index.php");
          }
        }
      }
    }
  } else { //for spammer block login form
    // Not verified - show form error
    //May be spam block the try to 5(max)
    if (ipexist($ip, $conn)) {
      $try = getTry($ip, $conn);
      $try++;
      $sql = "UPDATE logintry SET try='$maxtry', ltime='$ltime' where ip='$ip'";
      $result = mysqli_query($conn, $sql);
    } else {
      $try = 1;
      $sql = "INSERT INTO logintry (ip,try,ltime) VALUES('$ip', '$maxtry','$ltime')";
      $result = mysqli_query($conn, $sql);
    }
  }
} ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - <?php echo ucwords($website); ?></title>
  <script src="https://www.google.com/recaptcha/api.js?render=<?php echo $sitekey; ?>"></script>
  <script>
    grecaptcha.ready(function() {
      grecaptcha.execute('<?php echo $sitekey; ?>', {
        action: 'submit'
      }).then(function(token) {
        var recaptchaResponse = document.getElementById('recaptchaResponse');
        recaptchaResponse.value = token;
      });
    });
  </script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background-color: #F3F8FB;
    }

    a {
      text-decoration: none;
    }

    .login-box {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      min-height: 100vh;
      padding: 100px 0;
    }

    .login-box form {
      margin: auto;
      width: 450px;
      max-width: 100%;
      background: #fff;
      border-radius: 3px;
    }

    .login-form-head {
      text-align: center;
      background: #8655FC;
      padding: 50px;
    }

    .login-form-head h4 {
      letter-spacing: 0;
      text-transform: uppercase;
      font-weight: 600;
      margin-bottom: 7px;
      color: #fff;
    }

    .login-form-head p {
      color: #fff;
      font-size: 14px;
      line-height: 22px;
    }

    .login-form-body {
      padding: 50px;
    }

    .login-form-body label {
      position: absolute;
      left: 0;
      top: 0;
      color: #b3b2b2;
      -webkit-transition: all 0.3s ease 0s;
      transition: all 0.3s ease 0s;
      font-size: 14px;
    }

    .form-floating>label {
      padding: 1rem 0;
    }

    .form-control:focus {
      box-shadow: none;
    }

    .form-floating>.form-control {
      padding: 1rem 4px 0 4px;
    }

    .form-floating>.form-control,
    .form-floating>.form-select {
      height: calc(2.8rem + 2px);
      line-height: 1.25;
    }

    .login-form-body i {
      position: absolute;
      right: 5px;
      top: 50%;
      color: #7e74ff;
      font-size: 16px;
    }

    .submit-btn-area button {
      width: 100%;
      height: 50px;
      border: none;
      background: #fff;
      color: #585b5f;
      border-radius: 40px;
      text-transform: uppercase;
      letter-spacing: 0;
      font-weight: 600;
      font-size: 12px;
      box-shadow: 0 0 22px rgb(0 0 0 / 7%);
      -webkit-transition: all 0.3s ease 0s;
      transition: all 0.3s ease 0s;
      background-color: #2c71da;
      color: white;
      font-weight: 800;
    }

    .submit-btn-area button:hover {
      background-color: white;
      border: 1px solid #2c71da;
      color: #2c71da;
    }

    @media (min-width: 240px) and (max-width: 479px) {

      .login-form-head,
      .login-form-body {
        padding: 40px 20px;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="login-box">
      <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" role="form">
        <div class="login-form-head">
          <h4>Login</h4>
          <p>Hello, Sign in and Start Managing your Website from Admin Panel</p>
        </div>
        <div class="login-form-body">
          <?php
          if ($try <= $maxtry) {
          ?>
            <div class="form-floating mb-3">
              <input type="email" class="form-control border-0 border-bottom" name="email" id="floatingInput" placeholder="name@example.com">
              <label for="floatingInput">Email address</label>
              <i class="far fa-envelope"></i>
            </div>
            <div class="form-floating">
              <input type="password" class="form-control border-0 border-bottom" id="floatingPassword" name="password" placeholder="Password">
              <label for="floatingPassword">Password</label>
              <i class="fas fa-lock"></i>
            </div>
            <div class="submit-btn-area mt-4">
              <button type="submit" value="submit" name="submit">Submit </button>
              <input type="hidden" name="recaptcha_response" id="recaptchaResponse">
            </div>
          <?php } else {
            echo 'Too Many Login Attemps or You are a Spammer! Your Login is Blocked for 30 minutes. Wait or Try Forgot Password...';
          } ?>
          <div class="form-footer text-center mt-5">
            <p class="text-muted"><a href="forgot-password.php">Forgot Password?</a></p>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>