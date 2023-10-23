<?php

function addSession(int $idUser, string $token) : bool {
  $params = ['uid' => $idUser, 'token' => $token];
  $sql = "INSERT INTO sessions(id_user, token) VALUES(:uid, :token)";
  dbQuery($sql, $params);
  return true;
}

function getSession(string $token) : ?array {
  $sql = "SELECT * FROM sessions WHERE token = :token";
  $query = dbQuery($sql, ['token' => $token]);
  $session = $query->fetch();
  return is_array($session) ? $session : null;
}

function deleteSession(string $token) : bool{
  $sql = "DELETE FROM sessions WHERE token = :token";
  dbQuery($sql, ['token' => $token]);
  return true;
}