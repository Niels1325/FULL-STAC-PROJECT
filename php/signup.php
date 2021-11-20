<?php
//signup.php
include "config.php";
echo '</div></div> <link rel="stylesheet" href="../css/styles.css" type="text/css">';
echo '<div class="wrapper"><h1>NeLLo <br> Records</h1>';

if($_SERVER['REQUEST_METHOD'] != 'POST')
{
    /*the form hasn't been posted yet, display it
      note that the action="" will cause the form to post to the same page it is on */
    echo '<form method="post" action="">
        <h3>Username:</h3> <input type="text" name="user_name" /><br>
        <h3>Password:</h3> <input type="password" name="user_pass"><br>
        <h3>Confirm Password:</h3> <input type="password" name="user_pass_check"><br>
        <input type="submit" class="button" value="Sign up" />
     </form></div>';
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
            echo '<h3>Successfully registered. You can now <a href="signin.php">sign in</a> and start uploading or buy beats!</h3>';
        }
    }
}

?>
