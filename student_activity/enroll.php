<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('location: index.php');
  exit();
}
require 'db_connect.php';

$conn->begin_transaction();
$sql = "select seats from activities where id={$_GET['id']}";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
if($row && $row['seats'] > 0) {
  try {
    $sql = "insert into enrollments (stu_id, act_id) 
            values (?, ?)";
    $stm = $conn->prepare($sql);
    $stm->bind_param('si', $_SESSION['user']['id'], $_GET['id']);
    $stm->execute();
    $sql = "update activities set seats=seats-1 where id=?";
    $stm = $conn->prepare($sql);
    $stm->bind_param('i', $_GET['id']);
    $stm->execute();
    $conn->commit();
    $resp = ['status'=>'ok'];
  } catch (Exception) {
    $conn->rollback();
    $resp = ['status'=>'error', 'message'=>'Operation error.'];
  }
}
else {
  $resp = ['status'=>'error', 'message'=>'Please check activity again.'];
}
echo json_encode($resp);