<?php
// Include config file
require_once "dbconn.php";
 
// Define variables and initialize with empty values
$title = $content = $date = $image = $cat_id = $added_by = "";
$title_err = $content_err = "";
 
# Fetch Roles .....
$categories = 'select * from category';
$op = $mysqli->prepare($categories);

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate title
    $input_title = trim($_POST["title"]);
    if(empty($input_title)){
        $title_err = "Please enter a title.";
    } elseif(!filter_var($input_title, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $title_err = "Please enter a valid title.";
    } else{
        $title = $input_title;
    }
    
    // Validate content
    $input_content = trim($_POST["content"]);
    if(empty($input_content)){
        $content_err = "Please enter an content.";     
    } else{
        $content = $input_content;
    }
    
      # Validate image 
      $input_imagen= trim($_FILES['image']['name']);
      if(empty($input_imagen)){
        $image_err = "Field Required";
    }else{
        
    $tmpPath    =  $_FILES['image']['tmp_name'];
    $imageName  =  $_FILES['image']['name'];
    $imageSize  =  $_FILES['image']['size'];
    $imageType  =  $_FILES['image']['type'];

    $exArray   = explode('.',$imageName);
    $extension = end($exArray);

    $FinalName = rand().time().'.'.$extension;

    $allowedExtension = ["png",'jpg'];

    }
    
    if(empty($title_err) && empty($content_err)){
        
        // sql insert statement
        $sql = "INSERT INTO blog (title, content , image) VALUES (?, ?, ?)";
        
        if($stmt = $mysqli->prepare($sql)){

            $stmt->bind_param("sss", $param_title, $param_content, $param_image);
            
            // Save parameters
            $param_title = $title;
            $param_content = $content;
            $param_image = $image;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        echo mysqli_error($mysqli);
        exit();
         
        // Close statement
        //$stmt->close();
    }
    
    // Close connection
    $mysqli->close();
}
?>
 
 <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Create Blog</h2>
                    <p>Please fill this form and submit to add blog record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>title</label>
                            <input type="text" name="title" class="form-control <?php echo (!empty($title_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $title; ?>">
                            <span class="invalid-feedback"><?php echo $title_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>contetn</label>
                            <textarea name="content" class="form-control <?php echo (!empty($content_err)) ? 'is-invalid' : ''; ?>"><?php echo $content; ?></textarea>
                            <span class="invalid-feedback"><?php echo $content_err;?></span>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword">Image</label>
                            <input type="file"   name="image" >
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword">Category</label>

                            <select class="form-control" name="cat_id">
                                <?php
                                while($data = mysqli_fetch_assoc($categories)){
                                ?>
                                <option value="<?php echo $data['id']; ?>"><?php echo $data['title']; ?></option>
                                <?php } ?> ?>
                            </select>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="index.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>