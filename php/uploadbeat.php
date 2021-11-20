<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fsp";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_POST['submit']))
{

    $beatname = $_POST['beat_name'];
    $beatgenre = $_POST['beat_genre'];
    $beattype = $_POST['beat_type'];
    $beatprice = $_POST['beat_price'];
    $sql = "INSERT INTO beats (beatname, beatgenre, beattype, price)
            VALUES ('$beatname', '$beatgenre', '$beattype', '$beatprice')";
    if ($conn->query($sql) === TRUE) {
        echo "Uploaded beat successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
<!DOCTYPE>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="stylesheet" href="../css/styles.css">
    <title>FSP - Upload Beat</title>
</head>
<body>
<div class="wrapper">
    <form method="POST" action="" enctype="multipart/form-data">
        <h3>Beat File:</h3> <label class="beatfile"><input type="file" name="beatfile" required /></label><br>

        <h3>Beat Name:</h3> <input type="text" placeholder="Beat name" name="beat_name" required/><br>

        <h3>Beat Genre:</h3>
        <select class="selection" id="beat_genre" name="beat_genre" required>
            <option value="Trap">Trap</option>
            <option value="Drill">Drill</option>
            <option value="Reggaeton">Reggaeton</option>
            <option value="Boombap">Boombap</option>
            <option value="Hyperpop">Hyperpop</option>
            <option value="House">House</option>
            <option value="EDM">EDM</option>
            <option value="Other">Other</option>
        </select><br>

        <h3>Free or Premium:</h3>
        <input class="radio" type="radio" name="beat_type" value="free" id="free" checked onchange="hideContent()"> —
        <input class="radio" type="radio" name="beat_type" value="premium" id="premium" onchange="showContent()">

        <div class="pricediv free" id="beatprice">
            <h3 class="h7">Beat Price:</h3>
            <input value="0" type="text" name="beat_price" placeholder="€19,99" id="beat_price"><br>
        </div>

        <input type="submit" name="submit" class="button2" value="Upload your beat" />
    </form>
</div>
<script>
    const beatprice = document.getElementById('beatprice');
    const premium = document.getElementById('premium');
    const free = document.getElementById('free');
    const beatPriceText = document.getElementById('beat_price');



    function hideContent() {
            beatprice.className = free.value;
            beatPriceText.value = "0";
    }
    function showContent() {
            beatprice.className = premium.value;
            beatPriceText.value = "€19,99";
    }
</script>
</body>
</html>
