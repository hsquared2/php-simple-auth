<?php

session_start();
define('SMARTCAPTCHA_SERVER_KEY', 'ysc2_rfsjyfPp7tvv9QyZZZzDMp5G8yFQ4YzbE7DCTY966f6983ab');

include_once("init.php");

$token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;


if($token !== null) {
  $session = getSession($token);

  if($session !== null) {
    $user = getUserById($session['id_user']);

    if($user !== null) {
      redirect('account.php');
    }
  } else {
    unsetAuth();
    redirect("index.php");
  }
}

$errors = [];
$login = '';
$showCaptcha = false;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $login = trim($_POST['login']);
  $pwd = $_POST['pwd'];
  $captchaToken = $_POST['smart-token'];

  if($login === '' || $pwd === '' ) {
    $errors[] = "Fill in the fields!";
  }

  if(empty($errors)) {
    $user = getUserByEmail($login) ?? getUserByPhone($login) ?? null;

    if($user !== null) {
      if(password_verify($pwd, $user['password'])) {
        $uid = $user['id_user'];
        $showCaptcha = true;
        
        if(check_captcha($captchaToken)) {
          generateSession($uid);
          redirect('account.php');
        } else {
         $errors[] = "Captcha Error";
        }
      } else {
        $errors[] = "Incorrect password!";
      }
    } else {
      $errors[] = "User not found!";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign in</title>
  <link rel="stylesheet" href="assets/bootstrap.min.css">
  <link rel="stylesheet" href="assets/style.css">
  <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
</head>
<body>
  <header class="header">
    <div class="container header_container">
      <h1>Sign in</h1>
      <a href="registration.php" class="btn btn-primary">Sign up</a>
    </div>
  </header>
  <hr>
  <main>
    <div class="container">
      <form method="POST">
        <div class="col-md-4 form-item">
          <label for="login">Email / Phone</label>
          <input type="text" class="form-control" name="login" id="login" value="<?=$login?>">
        </div>
        <div class="col-md-4 form-item">
          <label for="password">Password</label>
          <input type="password" class="form-control" name="pwd" id="pwd">
        </div>
        <div class="col-md-4 form-item">
          <?foreach($errors as $err):?>
            <p class="alert alert-danger"><?=$err?></p>
          <?endforeach;?>
        </div>
        <div class="col-md-4">
          <div
          id="captcha-container"
          class="smart-captcha"
          data-sitekey="ysc1_rfsjyfPp7tvv9QyZZZzD92cxEcvkcya6JOe19wjr8520f6ac"
          >
            <input type="hidden" name="smart-token" value="<?=$captchaToken?>">
          </div>
        </div><br>
        <button type="submit" class="btn btn-success">Submit</button>
        <br><br>
        <div class="col-md-4 form-item">
          <p class="alert alert-warning">
            Don't have an account?
            Sign up
            <a href="registration.php">here</a>
          </p>
        </div>
      </form>
    </div>
  </main>
</body>
</html>