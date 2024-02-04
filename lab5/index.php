<?php
$servername = "db403-mysql";
$database = "northwind";
$username = "root";
$password = "P@ssw0rd";

try {
  $conn = new mysqli($servername, $username, $password, $database);
}
catch (Exception $e) {
  // echo $e->getMessage();
  die("Connection failed");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connect to MYSQL</title>
</head>
<body>
<?php
$sql = "SELECT * FROM categories";
try {
  $result = $conn->query($sql);
  echo "<table>";
  while($row = $result->fetch_assoc()) {
      echo "<tr>";
      echo "<td>{$row['CategoryID']}</td>";
      echo "<td>{$row['CategoryName']}</td>";
      echo "</tr>";
  }
  echo "</table>";
}
catch (Exception $e) {
  // echo $e->getMessage();
  echo 'Something went wrong.';
}
?>
<ol>
  <li>
    <p>แสดงจำนวนวันเฉลี่ยที่ใช้ในการขนส่งสินค้านับจากวันที่มีการสั่งสินค้าถึงวันที่ส่งสินค้า (เศษของวันนับเป็น 1 วัน) ของแต่ละประเทศ เรียงลำดับจากมากไปหาน้อย</p>
    <table>
      <tr><th>ShipCountry</th><th>Average shipped days</th></tr>
<?php
$sql = 'SELECT ShipCountry, CEILING(AVG(DATEDIFF(ShippedDate, OrderDate)))
`Average shipped days`
FROM orders
GROUP BY ShipCountry
ORDER BY `Average shipped days` DESC, ShipCountry';
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo "<td>{$row['ShipCountry']}</td>";
  echo "<td>{$row['Average shipped days']}</td>";
  echo '</tr>';
}
?>
    </table>
  </li>
  <li>
    <p>แสดงจำนวนรายการสั่งซื้อของแต่ละเดือน ในปี 1995</p>
    <table>
    <tr><th>Month</th><th>No. of orders</th></tr>
<?php
$sql = 'SELECT MONTH(OrderDate) Month, COUNT(*) `No. of orders`
FROM orders
WHERE YEAR(OrderDate) = 1995
GROUP BY Month';
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo "<td>{$row['Month']}</td>";
  echo "<td>{$row['No. of orders']}</td>";
  echo '</tr>';
}
?>
    </table>
  </li>
  <li>
    <p>ค้นหาว่าประเทศใดมีลูกค้ามากที่สุด</p>
    <table>
    <tr><th>Country</th><th>No. of customers</th></tr>
<?php
$sql = 'SELECT Country, COUNT(*) `No. of customers`
FROM customers
GROUP BY Country
ORDER BY 2 DESC
LIMIT 1';
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo "<td>{$row['Country']}</td>";
  echo "<td>{$row['No. of customers']}</td>";
  echo '</tr>';
}
?>
    </table>
  </li>
  <li>
    <p>ยอดสั่งซื้อสินค้าของแต่ละหมวด แยกตามประเทศของลูกค้า</p>
    <table>
    <tr>
      <th>CategoryName</th>
      <th>Country</th>
      <th>#Orders</th>
    </tr>
<?php
$sql = 'SELECT CategoryName, Country, COUNT(*) `#Orders`
FROM categories G 
	JOIN products P ON G.CategoryID = P.CategoryID
    JOIN `order details` D ON P.ProductID = D.ProductID
    JOIN orders O ON D.OrderID = O.OrderID
    JOIN customers C ON O.CustomerID = C.CustomerID
GROUP BY CategoryName, Country;';
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo "<td>{$row['CategoryName']}</td>";
  echo "<td>{$row['Country']}</td>";
  echo "<td>{$row['#Orders']}</td>";
  echo '</tr>';
}
$conn->close();
?>
    </table>
  </li>
</ol>
</body>
</html>