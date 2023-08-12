<?php
require_once 'config.php';

//sesija je kao mala memorija koja ce da sacuva podatke

if($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT admin_id, password FROM admins WHERE username = ?";
    
    $run = $conn->prepare($sql);
    $run->bind_param("s", $username);
    $run->execute();

    $results = $run->get_result();

    $conn->close();
    
    if($results->num_rows == 1) {

    $admin = $results->fetch_assoc();
    //fetch_assoc je da pretvori iz baze podataka u asocijativni niz
    if(password_verify($password, $admin['password']))
    //password_verify sluzi da bi uporedili string i hash password
    {
        $_SESSION['admin_id'] = $admin['admin_id'];
        header('location: admin_dashboard.php');
    } else {
        $_SESSION['error'] = "Netacan password";
        $conn->close();
        header('location: index.php');
        exit;
    }

    } else {
        $_SESSION['error'] = "Netacan username";
        $conn->close();
        header('location: index.php');
        exit;
    }

}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>

    <?php
    if(isset($_SESSION['error'])) {
        echo $_SESSION['error'];
        unset($_SESSION['error']); // mora da se unsetuje da ne bi ponovo izbacivap
    }
    ?>

    <form action="" method="POST">
        Username: <input type="text" name="username"><br>
        Password: <input type="password" name="password"><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>