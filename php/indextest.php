<?php
//signin.php
include 'config.php';
echo '</div></div> <link rel="stylesheet" href="../css/index.css" type="text/css">';
echo '<div class="wrapper"><h1>NeLLo <br>Records</h1>';

//first, check if the user is already signed in. If that is the case, there is no need to display this page
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
{
    echo '<h3>You are already signed in, click here to <a href="signout.php">sign out</a><h3/>';
}
else
    if($_SERVER['REQUEST_METHOD'] != 'POST')
    {
        /*the form hasn't been posted yet, display it
          note that the action="" will cause the form to post to the same page it is on */
        echo '
            <div class="form-wrapper">
    <div class="title-text">
        <div class="title login">Login</div>
        <div class="title signup">Signup</div>
    </div>
    <div class="form-container">
        <div class="slide-controls">
            <input type="radio" name="slide" id="login" checked>
            <input type="radio" name="slide" id="signup">
            <label for="login" class="slide login">Login</label>
            <label for="signup" class="slide signup">Signup</label>
            <div class="slider-tab"></div>
        </div>
        <div class="form-inner">
            <form method="post" action="#" class="login">
                <div class="field">
                    <input type="text" placeholder="Username" name="user_name" required>
                </div>
                <div class="field">
                    <input type="password" placeholder="Password" name="user_pass" required>
                </div>
                <div class="pass-link"><a href="#">Forgot password?</a></div>
                <div class="field btn">
                    <div class="btn-layer"></div>
                    <input type="submit" name="login_submit"  value="Login">
                </div>
                <div class="signup-link">Not a member? <a href="">Signup now</a></div>
            </form>';
    }
    else {
        if (!isset($_POST['user_name'], $_POST['user_pass'])) {
            // Could not get the data that should have been sent.
            exit('<h3>Please fill both the username and password fields!</h3>');
        }
// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
        if ($stmt = $conn->prepare('SELECT userID, password FROM users WHERE username = ?')) {
            // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
            $stmt->bind_param('s', $_POST['user_name']);
            $stmt->execute();
            // Store the result so we can check if the account exists in the database.
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($id, $password);
                $stmt->fetch();
                // Account exists, now we verify the password.
                // Note: remember to use password_hash in your registration file to store the hashed passwords.
                if (password_verify($_POST['user_pass'], $password)) {
                    $needs_refactor=mysqli_real_escape_string($conn, $_POST['user_name']);
                    $sql = "SELECT userID ,username FROM users WHERE username = '$needs_refactor' ";
                    $result = mysqli_query($conn,$sql);
                    while($row = mysqli_fetch_assoc($result))
                    {
                        $_SESSION['signed_in']=true;
                        $_SESSION['user_id'] = $row['userID'];
                        $_SESSION['user_name']  = $row['username'];
                        print_r($_SESSION);
                        header('location: home.php');
                    }
                } else {
                    // Incorrect password
                    echo '<h3>Incorrect username and/or password!</h3>';
                }
            } else {
                // Incorrect username
                echo '<h3>Incorrect username and/or password!</h3>';
            }

            $stmt->close();
        }
    }
    ?>
<?php

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
    /*the form hasn't been posted yet, display it
      note that the action="" will cause the form to post to the same page it is on */
    echo '<form method="post" action="#" class="signup">
                <div class="field">
                    <input type="text" placeholder="Username" name="user_name" required>
                </div>
                <div class="field">
                    <input type="password" placeholder="Password" name="user_pass" required>
                </div>
                <div class="field">
                    <input type="password" placeholder="Confirm password" name="user_pass_check" required>
                </div>
                <div class="field btn">
                    <div class="btn-layer"></div>
                    <input type="submit" name="signup_submit" value="Signup">
                </div>
            </form>
        </div>
    </div>
</div>
<script src="../javascript/loginsignup.js"></script>';
}
else
{
    /* so, the form has been posted, we'll process the data in three steps:
        1.  Check the data
        2.  Let the user refill the wrong fields (if necessary)
        3.  Save the data
    */
    $errors = array(); /* declare the array for later use */

    if(isset($_POST['user_name']))
    {
        //the user name exists
        if(!ctype_alnum($_POST['user_name']))
        {
            $errors[] = '<h3>The username can only contain letters and digits.</h3>';
        }
        if(strlen($_POST['user_name']) > 30)
        {
            $errors[] = '<h3>The username cannot be longer than 30 characters.</h3>';
        }
    }
    else
    {
        $errors[] = '<h3>The username field must not be empty.</h3>';
    }


    if(isset($_POST['user_pass']))
    {
        if($_POST['user_pass'] != $_POST['user_pass_check'])
        {
            $errors[] = '<h3>The two passwords did not match.</h3>';
        }
    }
    else
    {
        $errors[] = '<h3>The password field cannot be empty.</h3>';
    }

    if(!empty($errors)) /*check for an empty array, if there are errors, they're in this array (note the ! operator)*/
    {
        echo '<h3>Uh-oh.. a couple of fields are not filled in correctly..</h3>';
        echo '<ul>';
        foreach($errors as $key => $value) /* walk through the array so all the errors get displayed */
        {
            echo '<li>' . $value . '</li>'; /* this generates a nice error list */
        }
        echo '</ul>';
    }
    else
    {
        //the form has been posted without, so save it
        //notice the use of mysql_real_escape_string, keep everything safe!
        $hashed_pass= password_hash($_POST['user_pass'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users(username, password, date_created) VALUES('" . mysqli_real_escape_string($conn,$_POST['user_name']) . "', '" . $hashed_pass . "', NOW())";

        $result = mysqli_query($conn,$sql);
        if(!$result)
        {

            //something went wrong, display the error
            echo '<h3>Something went wrong while registering. Please try again later.</h3>';
            echo mysqli_error($conn); //debugging purposes, uncomment when needed
        }
        else
        {
            echo '<div class="succes_message">Successfully registered. You can now <a href="indextest.php">sign in</a> and start uploading or buy beats!</div>';
        }
    }
}

?>
