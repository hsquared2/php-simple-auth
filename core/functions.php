<?php

function extractFields(array $target, array $fields) : array {
  $res = [];

  foreach($fields as $field) {
    $res[$field] = trim($target[$field]);
  }

  return $res;
}

function redirect(string $url) {
  header("Location: ".$url);
  exit();
}

function unsetAuth() {
  unset($_SESSION['token']);
  setcookie('token', '', time() - 1, '/');
}

function generateSession(int $idUser) {
  $token = substr(bin2hex(random_bytes(128)), 0, 64);
  $_SESSION['token'] = $token;
  setcookie('token', $token, time() + 3600 * 24, '/');
  addSession($idUser, $token);
}

function check_captcha($token) {
    $ch = curl_init();
    $args = http_build_query([
        "secret" => SMARTCAPTCHA_SERVER_KEY,
        "token" => $token,
        "ip" => $_SERVER['REMOTE_ADDR'], // Нужно передать IP-адрес пользователя.
                                         // Способ получения IP-адреса пользователя зависит от вашего прокси.
    ]);
    curl_setopt($ch, CURLOPT_URL, "https://smartcaptcha.yandexcloud.net/validate?$args");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

    $server_output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
        return true;
    }
    $resp = json_decode($server_output);
    return $resp->status === "ok";
}


function validateFields(array &$fields) : array {
  $errors = [];
  $tests = [
    'phone' => '/^\+?[1-9][0-9]{7,14}$/',
    'email' => '/^\S+@\S+\.\S+$/',
  ];

  foreach($fields as $key => $val) {
    $fields[$key] = htmlspecialchars($val);
  }

  if(!preg_match($tests['phone'], $fields['phone'])) {
    $errors[] = "Wrong phone format!";
  }

  if(!preg_match($tests['email'], $fields['email'])) {
    $errors[] = "Wrong email format!";
  }

  if($fields['pwd'] !== $fields['pwd_rpt']) {
    $errors[] = "Passwords do not match! Try again";
  }

  return $errors;
}