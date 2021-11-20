<?php
//signin.php
include 'config.php';
echo '</div></div> <link rel="stylesheet" href="../css/styles.css" type="text/css">';
echo '<div class="wrapper"><h1>NeLLo <br>Records</h1>';

//first, check if the user is already signed in. If that is the case, there is no need to display this page
// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if(isset($_SESSION['signed_in']) && $_SESSION['signed_in'] == true)
{
    echo 'You are already signed in, you can <a href="signout.php">sign out</a> if you want.';
}
else
    if($_SERVER['REQUEST_METHOD'] != 'POST')
    {
        /*the form hasn't been posted yet, display it
          note that the action="" will cause the form to post to the same page it is on */
        echo '
            <form method="post" action="">
            <h3>Username:</h3> <input type="text" name="user_name" />
            <h3>Password:</h3> <input type="password" name="user_pass"><br>
            <input type="submit" class="button" value="Sign in" />
         </form></div>';
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
                        header('location: freebeats.php');
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
