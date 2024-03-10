<?php
session_start();
if (!isset($_SESSION['user'])) {
  header('location: index.php');
  exit();
}
require 'db_connect.php';
if (isset($_POST['submit'])) {
  $image = $_SESSION['user']['pic'];
  if ($_FILES['pic']['name'] != '') {
    $target_file = "{$_SESSION['user']['id']}."
    .pathinfo($_FILES["pic"]["name"], PATHINFO_EXTENSION);
    if (move_uploaded_file(
      $_FILES["pic"]["tmp_name"], 
      "images/student/$target_file"
     )) {
      $image = $target_file;
    }
  }  
  $email = $_POST['email'];
  $fullname = $_POST['fullname'];
  $dep_id = $_POST['dep_id'];

  $sql = "update students set fullname=?, email=?, dep_id=?, pic='$image'
          where id='{$_SESSION['user']['id']}'";
  $stm = $conn->prepare($sql);
  $stm->bind_param('sss', $fullname, $email, $dep_id);
  $stm->execute();
  $_SESSION['user']['fullname'] = $fullname;
  $_SESSION['user']['pic'] = $image;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />  <title>Student Activity</title>
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
  <style>
    html,
    body {
      height: 100%;
    }

    .form-signin {
      max-width: 330px;
      padding: 1rem;
    }

    .form-signin .form-floating:focus-within {
      z-index: 2;
    }

    .form-signin input, .form-signin select {
      margin-bottom: -1px;
      border-radius: 0;
    }

    .form-signin input[name='id'] {
      border-top-right-radius: 6px;
      border-top-left-radius: 6px;
    }

    #floatingDepartment {
      margin-bottom: 10px;
      border-bottom-left-radius: 6px;
      border-bottom-right-radius: 6px;
    }
  </style>
  <script>
    function changeFaculty() {
      let id = document.getElementById('floatingFaculty').value;
      console.log(id);
      fetch('departments.php?id='+id)
      .then(resp=>resp.json())
      .then(data=>{
        let departments = document.getElementById('floatingDepartment');
        let options = '';
        for (let dep of data) {
          options += `<option value="${dep.id}">${dep.name}</option>`;
        }
        departments.innerHTML = options;
      });
    }

    function preview() {
      const pic_label = document.getElementById('pic-label');
      pic_label.innerHTML = '<img class="rounded-circle" style="height:150px;">';
      const img = pic_label.querySelector('img');
      const profileImage = document.getElementById('pic');
      img.src = URL.createObjectURL(profileImage.files[0]);
      img.onload = () => {
        URL.revokeObjectURL(img.src);
      };
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
          <?php
if($_SESSION['user']['pic'] == null) {
?>
          <span class="material-symbols-outlined" 
          style="color:blue;font-size:24pt;">face</span>
<?php
}
else {
 echo "<img src='images/student/{$_SESSION['user']['pic']}' 
       width='32' height='32' class='rounded-circle'>";
}
?>
          </a>
          <ul class="dropdown-menu text-small">
            <li>
              <b class="dropdown-item">
                <?php echo $_SESSION['user']['fullname'];?>
              </b>
            </li>
            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="signout.php">Sign out</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>
  <div class="container">
<?php
$sql = "select S.id, fullname, email, pic, dep_id, fac_id  
        from students S 
        join departments D on S.dep_id=D.id 
        join faculties F on D.fac_id=F.id
        where S.id='{$_SESSION['user']['id']}'";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
?> 
  <div class="d-flex align-items-center py-4 bg-body-tertiary">
    <main class="form-signin w-100 m-auto">
      <form method="post" enctype="multipart/form-data">
        <img class="mb-4" src="images/logo.png" alt="" width="72" height="57">
        <span class="h3 text-success">Student Activity</span>
        <h1 class="h3 mb-3 fw-normal">Profile</h1>
<?php
if (isset($error)) {
  echo "<div class='alert alert-danger'>$error</div>";
}
?>   
        <div class="mb-3">
          <label id="pic-label" for="pic">
<?php
if ($row['pic'] == null) {
?>
            <span class="material-symbols-outlined" 
            style="color:gray;font-size:150pt;">face</span>
<?php
}
else {
  echo "<img class='rounded-circle' style='height:150px;' 
         src='images/student/{$row['pic']}'>";
}
?>
          </label>
          <input id="pic" name="pic" type="file" class="d-none" 
          accept="image/*" onchange="preview()">
        </div>
        <div class="form-floating">
          <input type="text" class="form-control" 
          id="floatingID" placeholder="Student ID" readonly 
          value="<?php echo $row['id'];?>">
          <label for="floatingID">Student ID</label>
        </div>
        <div class="form-floating">
          <input name="fullname" type="text" class="form-control" 
          id="floatingName" placeholder="Fullname" required
          value="<?php echo $row['fullname'];?>">
          <label for="floatingName">Fullname</label>
        </div>
        <div class="form-floating">
          <input name="email" type="email" class="form-control" 
          id="floatingEmail" placeholder="Email address" required
          value="<?php echo $row['email'];?>">
          <label for="floatingEmail">Email address</label>
        </div>
        <div class="form-floating">
          <select class="form-select" 
          id="floatingFaculty" placeholder="Faculty" onchange="changeFaculty()">
<?php
$sql = 'select * from faculties order by id';
$result = $conn->query($sql);
while ($fact = $result->fetch_assoc()) {
  echo "<option value='{$fact['id']}'"
        .($fact['id']==$row['fac_id']? ' selected':'')
        .">{$fact['name']}</option>";
}
?>
          </select>
          <label for="floatingFaculty">Faculty</label>
        </div>
        <div class="form-floating">
          <select name="dep_id" class="form-select" 
          id="floatingDepartment" placeholder="Department">
<?php
$sql = "select id, name from departments where fac_id={$row['fac_id']}";
$result = $conn->query($sql);
while ($dep = $result->fetch_assoc()) {
  echo "<option value='{$dep['id']}'"
        .($dep['id']==$row['dep_id'] ? ' selected' : '')
        .">{$dep['name']}</option>";
}
$conn->close();
?>
          </select>
          <label for="floatingDepartment">Department</label>
        </div>
    
        <button name="submit" class="btn btn-primary w-100 py-2" type="submit">Save</button>
      </form>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</div>

  </div>
</body>
</html>