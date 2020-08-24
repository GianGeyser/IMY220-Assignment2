<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	// create and add a image to gallery
	function addImage($filename){
        echo "<div class='col-3' style='background-image: url(" . $filename . ")'></div>";
    }

    //database connection
	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false

    if(isset($_POST["submit"]) && $email){
        $target_dir = "gallery/";
        $uploadFile = $_FILES["picToUpload"];
        $numUploads = count($uploadFile["name"]);

        for ($i = 0; $i < $numUploads; $i++){
            $target_file = $target_dir . basename($uploadFile["name"][$i]);
            $imageType = $uploadFile["type"][$i];
            $imageSize = $uploadFile["size"][$i];
            $maxSize = 1048576;



            if(($imageType == "image/jpg" || $imageType == "image/jpeg") && $imageSize < $maxSize){
                if(move_uploaded_file($uploadFile["tmp_name"][$i], $target_file)){
                    $query = "SELECT user_id FROM tbusers WHERE email = '$email'";
                    $userId = mysqli_query($mysqli, $query);
                    if($userId) {
                        $userId = mysqli_fetch_assoc($userId);
                        $userId = $userId["user_id"];
                    }
                    $query = "INSERT INTO tbgallery (user_id, filename) VALUES ('$userId','$target_file');";
                    $res = mysqli_query($mysqli, $query);
                }
            }
        }
    }



?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Gian Geyser">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='login.php' method='post' enctype='multipart/form-data'>
                                <div class='form-group'>
									<input type='hidden' name='loginEmail' id='loginEmail' value='$email' />
								</div>
								<div class='form-group'>
									<input type='hidden' name='loginPass' id='loginPass' value='$pass'/>
								</div>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' multiple='multiple' id='picToUpload' /><br/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
								</div>
						  	</form>";
                    echo "<h2>Image Gallery</h2>";
                    echo    "<div class='row imageGallery'>";


                    $query = "SELECT user_id FROM tbusers WHERE email = '$email'";
                    $userId = mysqli_query($mysqli, $query);
                    if($userId) {
                        $userId = mysqli_fetch_assoc($userId);
                        $userId = $userId["user_id"];
                        $query = "SELECT filename FROM tbgallery WHERE user_id = '$userId'";
                        $res = mysqli_query($mysqli, $query);

                        while ($result = mysqli_fetch_assoc($res)) {
                            addImage($result["filename"]);
                        }
                    }

                    echo "</div>";

				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</body>
</html>