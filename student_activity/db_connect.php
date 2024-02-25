<?php
try {
  $conn = new mysqli('DB403-mysql', 'root', 'P@ssw0rd', 'student_activity');
}
catch (Exception) {
  die('Connection error');
}