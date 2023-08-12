<?php
require_once 'config.php';
require_once 'register_member.php';

if(!isset($_SESSION['admin_id'])) {
    header('location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link
  rel="stylesheet"
  href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css"
  type="text/css"
/>
    <title>Admin Dashboard</title>
</head>
<body>

<?php if(isset($_SESSION['success_message'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php
    echo $_SESSION['success_message'];
    unset($_SESSION['success_message']);
    ?>
    <button class="btn-close" type="button" data-bs-dismiss="alert" aria-label="close"></button>
</div>
<?php endif;?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Members List</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Trainer</th>
                        <th>Photo</th>
                        <th>Training Plan</th>
                        <th>Access Card</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $sql = "SELECT members.*, training_plans.name AS training_plan_name,
                trainers.first_name AS trainer_first_name,
                trainers.last_name AS trainer_last_name
                FROM `members`
                LEFT JOIN `training_plans` ON members.training_plan_id = training_plans.plan_id
                LEFT JOIN `trainers` ON members.trainer_id = trainers.trainer_id;";
                $results = $conn->query($sql);
                $results= $results->fetch_all(MYSQLI_ASSOC);
                $select_members = $results;

                foreach($results as $result) :?>
                <tr>
                    <td><?php echo $result['first_name'];?></td>
                    <td><?php echo $result['last_name'];?></td>
                    <td><?php echo $result['email'];?></td>
                    <td><?php echo $result['phone_number'];?></td>
                    <td><?php
                    if($result['trainer_first_name']) {
                        echo "<b>" . $result['trainer_first_name'] . " " . $result['trainer_last_name'] . "</b>";
                    } else {
                        echo "Nije dodeljen trener";
                    }
                    ?></td>
                    <td><?php echo $result['photo_path'];?></td>
                    <td><?php 
                    if($result['training_plan_name']) {
                        echo $result['training_plan_name'];
                    } else {
                        echo "Nema plana";
                    }
                    ?>
                    </td>
                    <td><a target="blank" href="<?php echo $result['access_card_pdf_path'];?>">Access Card</a></td>
                    <td>
                        
                    <?php 
                    
                    $created_at = strtotime($result['created_at']);
                    // uzeo sam datum iz database i formatirao ga u timestamp kako bih mogao kasnije da ga obradim
                    $new_date = date("d/m/Y", $created_at);
                    //obradio sam time stamp u pravi realan date pomocu date ugradjene funkcije
                    echo $new_date;
                    ?>
                    </td>
                    <td>
                    <form action="delete_member.php" method="POST">
                    <input type="hidden" name="member_id" value = "<?php echo $result['member_id'];?>">
                    <button>DELETE</button>
                    </form>
                </td>
                </tr>
              <?php  endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <div class="container">
        <div class="row mb-5">
            <div class="col-md-6">
                <h2>Register Member</h2>
                <form action="register_member.php" method="post" enctype="multipart/form-data">
                    First Name: <input type="text" class="form-control" name="first_name"><br>
                    Last Name: <input type="text" class="form-control" name="last_name"><br>
                    Email: <input type="email" class="form-control" name="email"><br>
                    Phone Number: <input type="text" class="form-control" name="phone_number"><br>
                    Training Plan:
                    <select name="training_plan_id" class="form-control">
                        <option value="" disabled selected>Training Plan</option>
                        <?php
                        $sql = "SELECT * FROM trainers";
                        $run = $conn->query($sql);
                        $results = $run->fetch_all(MYSQLI_ASSOC);
                        $select_trainers = $results;
                        var_dump($results);

                        foreach($results as $result) {
                            echo "<option value='". $result['plan_id'] . "'>" . $result['name'] ."</option>";
                        }

                        $conn->close();

                        ?>
                    </select> <br>
                    <input type="hidden" name="photo_path" id="photoPathInput">
                    <div id="dropzone-upload" class="dropzone"></div>
                    <input type="submit" class="btn btn-primary mt-3" value = "Register Member">
                </form>
            </div>
            <div class="col-md-6">
                <h2>Register Trainer</h2>
                <form action="register_trainer.php" method="post">
                    First Name: <input type="text" class="form-control" name = "first_name">
                    Last Name: <input type="text"  class="form-control" name="last_name">
                    Email: <input type="email"  class="form-control" name="email">
                    Phone Number: <input type="text"  class="form-control" name="phone_number">
                <input type="submit" class="btn btn-primary" value="Register Trainer">
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <h2>Assign Trainer to Member</h2>
                <form action="assign_trainer.php" method="POST">
                <label for="">Select Member</label>    
                <select name="member" id="" class="form-select">
                        <?php
                        foreach($select_members as $member): ?>
                        <option value="<?php echo $member['member_id'];?>">
                            <?php echo $member['first_name'];?>
                    </option>
                    <?php endforeach; ?>
                    </select>
                    <label for="">Select Trainer</label>    
                <select name="member" id="" class="form-select">
                        <?php
                    foreach($select_trainers as $trainer): ?>
                    <option value="<?php echo $trainer['trainer_id'];?>">
                        <?php echo $trainer['first_name'];?>
                    </option>
                    <?php endforeach;?>
                    </select>
                <button type="submit" class="btn btn-primary">Assign Trainer</button>
                </form>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
    Dropzone.options.dropzoneUpload = {
        url: "upload_photo.php",
        paramName = "photo",
        maxFilesize: 20,
        acceptedFiles: "image/*",
        init: function () {
            this.on("success", function(file,response) {
                const jsonResponse = JSON.parse(response);
                if(jsonResponse.success) {
                    document.getElementById('photoPathInput').value = jsonResponse.photo_path;
                } else {
                    console.error(jsonResponse.error);
                }
            });
        }
    };
</script>

</body>
</html>