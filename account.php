<?php

session_start();

include_once('init.php');

$token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;
$user = [];
$errors = [];
$uid = null;
$userUpdated = false;

if($token !== null) {
  $session = getSession($token);

  if($session !== null) {
    $user = getUserById($session['id_user']);
    $uid = $user['id_user'];

    if($user === null) {
      unsetAuth();
      redirect('index.php');
    }
  } else {
    unsetAuth();
    redirect("index.php");
  }
} else {
  redirect("index.php");
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $requiredFields = ['username', 'name', 'phone', 'email', 'pwd', 'pwd_rpt'];
  $user = extractFields($_POST, $requiredFields);
  $errors = validateFields($user);

  if(empty($errors)) {
    $verifyUser = checkUser($user);

    if($verifyUser['id_user'] !== $uid) {
      $errors[] = "User already exists!";
    } else {
      unset($user['pwd_rpt']);
      $user['pwd'] = password_hash($user['pwd'], PASSWORD_BCRYPT);
      $user['id_user'] = $uid;
  
      updateUser($user);
      $userUpdated = true;
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
  <title>Document</title>
  <link rel="stylesheet" href="assets/bootstrap.min.css">
  <link rel="stylesheet" href="assets/style.css">
</head>
<body>
  <header class="header">
    <div class="container header_container">
      <h1>Account Information</h1>
      <div class="header-right">
        <p class="text-primary">Hello, <?=$user['name']?></p>
        <a href="logout.php" class="btn btn-outline-danger">Log Out</a>
      </div>
    </div>
  </header>
  <hr>
  <main>
    <div class="container">
      <?if($userUpdated):?>
        <div class="col-md-4 alert alert-success">
          User succesfully updated
        </div>
      <?endif;?>
      <form method="POST">
        <div class="col-md-4 form-item">
          <label for="username">Username</label>
          <input type="text" name="username" class="form-control" id="username" value="<?=$user['username']?>" required>
        </div>
        <div class="col-md-4 form-item">
          <label for="name">Name</label>
          <input type="text" name="name" class="form-control" id="name" value="<?=$user['name']?>" required>
        </div>
        <div class="col-md-4 form-item">
          <label for="username">Email</label>
          <input type="email" name="email" class="form-control" id="email" value="<?=$user['email']?>" required>
        </div>
        <div class="col-md-4 form-item">
          <label for="phone">Phone</label>
          <input type="phone" name="phone" class="form-control" id="phone" value="<?=$user['phone']?>" required>
        </div>
        <div class="col-md-4 form-item">
          <label for="pwd">Password</label>
          <input type="password" name="pwd" class="form-control" id="pwd" required>
        </div>
        <div class="col-md-4 form-item">
          <label for="pwd_rpt">Repeat Password</label>
          <input type="password" name="pwd_rpt" class="form-control" id="pwd_rpt" required>
        </div>
        <div class="col-md-4">
          <?foreach($errors as $err):?>
            <p class="alert alert-danger"><?=$err?></p>
          <?endforeach;?>
        </div>
        <button type="submit" class="btn btn-success">Submit</button>
      </form>
    </div>
  </main>
</body>
</html>