<?php
require 'db_connect.php';
$sql = "select id, name from departments where fac_id=?";
$stm = $conn->prepare($sql);
$stm->bind_param('i', $_GET['id']);
$stm->execute();
$result = $stm->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
echo json_encode($rows);