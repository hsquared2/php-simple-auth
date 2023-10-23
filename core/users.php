<?php

function getUserByUsername(string $username) : ?array {
  $sql = "SELECT * FROM users WHERE username = :username";
  $query = dbQuery($sql, ['username' => $username]);
  $user = $query->fetch();
  return is_array($user) ? $user : null;
}

function getUserByEmail(string $email) : ?array{
  $sql = "SELECT * FROM users WHERE email = :email";
  $query = dbQuery($sql, ['email' => $email]);
  $user = $query->fetch();
  return is_array($user) ? $user : null;
}

function getUserByPhone(string $phone) : ?array {
  $sql = "SELECT * FROM users WHERE phone = :phone";
  $query = dbQuery($sql, ['phone' => $phone]);
  $user = $query->fetch();
  return is_array($user) ? $user : null;
}

function getUserById(int $id) : ?array{
  $sql = "SELECT id_user, username, name, email, phone FROM users WHERE id_user = :id";
  $query = dbQuery($sql, ['id' => $id]);
  $user = $query->fetch();
  return is_array($user) ? $user : null;
}

function addUser(array $fields) : bool {
  $sql = "INSERT INTO users (username, name, email, phone, password) VALUES (:username, :name, :email, :phone, :pwd)";
  dbQuery($sql, $fields);
  return true;
}

function checkUser(array $fields) : ?array {
  $params = ['username' => $fields['username'], 'email' => $fields['email'], 'phone' => $fields['phone']];
  $sql = "SELECT id_user, count(*) AS count FROM users WHERE username = :username OR email = :email OR phone = :phone";
  $query = dbQuery($sql, $params);
  $user = $query->fetch();
  return is_array($user) ? $user : null; 
}

function updateUser(array $fields) : bool {
  $sql = "UPDATE users SET username = :username, name = :name, email = :email, phone = :phone, password = :pwd WHERE id_user = :id_user";
  dbQuery($sql, $fields);
  return true;
}