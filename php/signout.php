<?php
//signout.php
include 'config.php';
echo '<h2>Sign out</h2>';

session_destroy();
header('location: index.php');
