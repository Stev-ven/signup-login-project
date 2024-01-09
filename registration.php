<?php
session_start();
if (isset($_SESSION["user"])) {
   header("Location: index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="container">
        <?php
        if (isset($_POST["submit"])) {
           $fullName = $_POST["fullname"];
           $mobile = $_POST["mobile"];
           $email = $_POST["email"];
           $residence = $_POST["residence"];
           $password = $_POST["password"];
           $passwordRepeat = $_POST["confirm_password"];
        

           //check for strong password;
           $uppercase = preg_match('@[A-Z]@', $password);
           $lowercase = preg_match('@[a-z]@', $password);
           $number    = preg_match('@[0-9]@', $password);
           $specialChars = preg_match('@[^\w]@', $password);

           //condition for strong password;
           if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
              array_push($errors,"weak password");
           }else{
               echo "<div class='alert alert-success'>Strong password</div>";
           }
           
           //hash password;
           $passwordHash = password_hash($password, PASSWORD_DEFAULT);
           
           //validation
           $errors = array();
           
           if (empty($fullName) OR empty($email) OR empty($password) OR empty($passwordRepeat)) {
            array_push($errors,"All fields are required");
           }
           if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Email is not valid");
           }
           if ($password!==$passwordRepeat) {
            array_push($errors,"Password does not match");
           }
           require_once "database.php";
           
           //check if email already exists in database
           $sql = "SELECT * FROM users WHERE email = '$email'";
           $result = mysqli_query($conn, $sql);
           $rowCount = mysqli_num_rows($result);
           if ($rowCount>0) {
            array_push($errors,"Email already exists!");
           }
           if (count($errors)>0) {
            foreach ($errors as  $error) {
                echo "<div class='alert alert-danger'>something went wrong</div>";
            }
           }else{
            
                 //insert user data into database
                 $sql = "INSERT INTO users (full_name, mobile,email, residence, password) 
                         VALUES ( ?, ?, ?, ?, ?)";
                 $stmt = mysqli_stmt_init($conn);
                 $prepareStmt = mysqli_stmt_prepare($stmt,$sql);
                 if ($prepareStmt){
                                   mysqli_stmt_bind_param($stmt,"sssss",$fullName,  $mobile, $email, 
                                   $residence, $passwordHash);
                                   mysqli_stmt_execute($stmt);
                                   echo "<div class='alert alert-success'>You are registered successfully.</div>";
                    }else{
                         die("Something went wrong");
                         }
                }
          

        }
        ?>
        <form action="registration.php" method="post">
            <div class="form-group">
                <input type="text" class="form-control" name="fullname" placeholder="Full Name:">
            </div>
            <div class="form-group">
                <input type="mobile" class="form-control" name="mobile" placeholder="Mobile:">
            </div>
            <div class="form-group">
                <input type="emamil" class="form-control" name="email" placeholder="Email:">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="residence" placeholder="Residence:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password:">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password:">
            </div>
            <div class="form-btn">
                <input type="submit" class="btn btn-primary" value="Register" name="submit">
            </div>
        </form>
        <div>
        <div><p>Already Registered <a href="login.php">Login Here</a></p></div>
      </div>
    </div>
</body>
</html>