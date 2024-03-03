<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('location: index.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <title>Student Activity</title>
  <script>
    function enroll(id) {
      fetch('enroll.php?id='+id)
      .then(response=>response.json())
      .then(data=>{
        if (data['status'] == 'error') {
          alert(data['message']);
        }
        else {
          alert('ลงทะเบียนสำเร็จ');
        }
        location.reload();
      });
    }
  </script>
</head>
<body>
  <header class="p-3 mb-3 border-bottom">
    <div class="container">
      <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
        <a href="main.php" class="d-flex align-items-center mb-2 mb-lg-0 link-body-emphasis text-decoration-none">
          <img src="images/logo.png" class="bi me-2" width="40" height="32">
        </a>

        <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
          <li><a href="#" class="nav-link px-2 link-secondary">Activities</a></li>
          <li><a href="#" class="nav-link px-2 link-body-emphasis">Report</a></li>
        </ul>

        <form class="col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3" role="search">
          <input type="search" class="form-control" placeholder="Search..." aria-label="Search">
        </form>

        <div class="dropdown text-end">
          <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
          </a>
          <ul class="dropdown-menu text-small">
            <li>
              <b class="dropdown-item">
                <?php echo $_SESSION['user']['fullname'];?>
              </b>
            </li>
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="signout.php">Sign out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>
  <div class="container">
    <h1 class="display-6">Activities</h1>
    <table class="table table-striped table-hover">
      <tr>
        <th>กิจกรรม</th>
        <th>ภาคการศึกษา</th>
        <th>ประเภท</th>
        <th>เริ่ม</th>
        <th>สิ้นสุด</th>
        <th>ที่คงเหลือ</th>
        <th></th>
      </tr>
<?php
require 'db_connect.php';
// $sql = "select A.id, A.name act_name, semester, edu_year, start, end, seats, 
//         C.name cat_name
//         from activities A join categories C on A.cat_id=C.id
//         where seats > 0 and
//         not exists (
//           select act_id
//           from enrollments 
//           where stu_id = '{$_SESSION['user']['id']}' and act_id=A.id)";
$sql = "select A.id, A.name act_name, semester, edu_year, start, end, seats, 
        C.name cat_name
        from activities A join categories C on A.cat_id=C.id
        where seats > 0 and
        A.id not in (
          select act_id
          from enrollments 
          where stu_id = '{$_SESSION['user']['id']}')";
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
  echo '<tr>';
  echo "<td>{$row['act_name']}</td>";
  echo "<td>{$row['semester']}/{$row['edu_year']}</td>";
  echo "<td>{$row['cat_name']}</td>";
  echo "<td>{$row['start']}</td>";
  echo "<td>{$row['end']}</td>";
  echo "<td class='text-end'>{$row['seats']}</td>";
  echo "<td><button class='btn btn-sm btn-primary' 
        onclick='enroll({$row['id']})'>ลงทะเบียน</button></td>";
  echo '</tr>';
}
$conn->close();
?>
    </table>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>