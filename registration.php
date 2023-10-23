<?php

session_start();

include_once('init.php');

$userOccupied = false;
$errors = [];
$fields = ['username' => '', 'name' => '', 'phone' => '', 'email' => ''];

$token = $_SESSION['token'] ?? $_COOKIE['token'] ?? null;

if($token !== null) {
  $session = getSession($token);

  if($session !== null) {
    $user = getUserById($session['id_user']);
    
    if($user !== null) {
      header("Location: account.php");
      exit();
    }
  } else {
    unset($_SESSION['token']);
    setcookie('token', '', time() - 1, '/');
  }
}


if($_SERVER['REQUEST_METHOD'] == 'POST') {
  $requiredFields = ['username', 'name', 'phone', 'email', 'pwd', 'pwd_rpt'];

  $fields = extractFields($_POST, $requiredFields);
  $errors = validateFields($fields);

  if(empty($errors)) {
    $userExists = checkUser($fields)['count'] > 0 ? true : false;

    if(!$userExists) {
      unset($fields['pwd_rpt']);
      $fields['pwd'] = password_hash($fields['pwd'], PASSWORD_BCRYPT);

      addUser($fields);
      $lastUserId = dbInstance()->lastInsertId();

      generateSession($lastUserId);

      header("Location: account.php");
      exit();

    } else {
      $userOccupied = true;
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
      <h1>Registration</h1>
      <div class="header-right">
        <a href="index.php" class="btn btn-primary">Sign in</a>
      </div>
    </div>
  </header>
  <hr>
  <main>
    <div class="container">
      <form action="" method="POST">
        <div class="col-md-4 form-item">
          <label for="username">Username</label>
          <input type="text" class="form-control" name="username" id="username" value="<?=$fields['username']?>" required>
        </div>
        <div class="col-md-4 form-item">
          <label for="name">Name</label>
          <input type="text" class="form-control" name="name" id="name" value="<?=$fields['name']?>" required>
        </div>
        <div class="col-md-4 form-item">
          <label for="phone">Phone</label>
          <input type="tel" class="form-control" name="phone" id="phone" value="<?=$fields['phone']?>" required>
        </div>
        <div class="col-md-4 form-item">
          <label for="email">Email</label>
          <input type="email" class="form-control" name="email" id="email" value="<?=$fields['email']?>" required>
        </div>
        <div class="col-md-4 form-item">
          <label for="pwd">Password</label>
          <input type="password" class="form-control" name="pwd" id="pwd" required>
        </div>
        <div class="col-md-4 form-item">
          <label for="pwd_rpt">Repeat Password</label>
          <input type="password" class="form-control" name="pwd_rpt" id="pwd_rpt" required>
        </div>
        <div class="col-md-4">
          <?foreach($errors as $err):?>
            <p class="alert alert-danger"><?=$err?></p>
          <?endforeach;?>
          <?if($userOccupied):?>
            <p class="alert alert-warning">This user already exists!</p>
          <?endif;?>
        </div>
        <button type="submit" class="btn btn-success form-btn">Submit</button>
      </form>
    </div>
  </main>
</body>
</html>



