<?php

if (session_status() == PHP_SESSION_NONE) {
  session_start();
}


define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'library_db');


$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);


if ($conn === false) {
  die("ERROR: Could not connect to the database. " . mysqli_connect_error());
}


define('BASE_URL', 'http://localhost/library_project/');


function sanitize_input($conn, $data)
{
  return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}
