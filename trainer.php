<?php
    include 'basesqlexecutors.php';
    
    $mid = $_GET['mid'];
    if (!$mid) {
        header("Location: interface.php");
    }
    
    // -----------------------
    // Personal section
    $result = executePlainSQL("select name, email, phone_number from gymuser where membership_id = $mid");
    $row = oci_fetch_array($result);
    $username = $row[0];
    $email = $row[1];
    $phone = $row[2];
    
    // Errors
    $personalerror = "";
    $usererror = "";
    $nameerror = "";
    $emailerror = "";
    $phoneerror = "";
    
    $u_name = $u_email = $u_phone = "";
    
    // Form handling
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Personal section
        if (array_key_exists('personal', $_POST)) {
            $anyerrors = false;
            $newname = htmlspecialchars($_POST['name'], ENT_QUOTES);
            if (strlen($newname)> 40) {
                $nameerror = "Name too long";
                $anyerrors = true;
            }
            $username = $newname;
            
            $newemail = htmlspecialchars($_POST['email']);
            if (!filter_var($newemail, FILTER_VALIDATE_EMAIL)) {
                $emailerror = "Enter a valid email";
                $anyerrors = true;
            }
            $email = $newemail;
            
            $newphone = $_POST['phone'];
            if (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $newphone)) {
                $phoneerror = "Phone number must be in the format xxx-xxx-xxxx";
                $anyerrors = true;
            }
            $phone = $newphone;
            
            if (!$anyerrors) {
                $sql = "update gymuser set name='" . $newname . "', email='" . $newemail. "', phone_number='"  . $newphone . "' where membership_id=".$mid;
                $parse = OCI_Parse($db_conn, $sql);
                $r = oci_execute($parse);
                if(!$r) {
                    $personalerror = "Error updating info";
                }
            }
        }
        else if (array_key_exists('newuser', $_POST)) {
            $anyerrors = false;
            $u_name = htmlspecialchars($_POST['name'], ENT_QUOTES);
            if (strlen($u_name)> 40) {
                $nameerror = "Name too long";
                $anyerrors = true;
            }
            $u_email = htmlspecialchars($_POST['email']);
            if (!filter_var($u_email, FILTER_VALIDATE_EMAIL)) {
                $emailerror = "Enter a valid email";
                $anyerrors = true;
            }
            $u_phone = htmlspecialchars($_POST['phone']);
            if (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $u_phone)) {
                $phoneerror = "Phone number must be in the format xxx-xxx-xxxx";
                $anyerrors = true;
            }
            $sql = "select membership_id from gymuser order by membership_id desc";
            $state = OCI_Parse($db_conn, $sql);
            $r = oci_execute($state);
            if (!$r) {
                $anyerrors = true;
            }
            if (!$anyerrors) {
                $row = oci_fetch_array($state);
                $newid = $row[0] + 1;
                
                $sql = "insert into gymuser values(".$newid.", '".$u_email."', '".$u_name."', '".$u_phone."')";
                $state = OCI_Parse($db_conn, $sql);
                $r = oci_execute($state);
                if (!$r) {
                    $usererror = "Failed to create new user";
                } else {
                    $usertype = $_POST['utype'];
                    if ($usertype == "athlete") {
                        $sql = "insert into athlete values($newid)";
                        $state = OCI_Parse($db_conn, $sql);
                        $r = oci_execute($state);
                        if (!$r) {
                            $usererror = "Failed to create new user";
                        }
                    } else if ($usertype == "trainer") {
                        $sql = "insert into trainer values($newid)";
                        $state = OCI_Parse($db_conn, $sql);
                        $r = oci_execute($state);
                        if (!$r) {
                            $usererror = "Failed to create new user";
                        }
                    } else if ($usertype == "admin") {
                        $sql = "insert into gymadmin values($newid)";
                        $state = OCI_Parse($db_conn, $sql);
                        $r = oci_execute($state);
                        if (!$r) {
                            $usererror = "Failed to create new user";
                        }
                    }
                }
            }
        }
    }
    
    
    
    ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title> Trainer Home </title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <script src="toggletab.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
        rel="stylesheet">
    <link rel="stylesheet" href="toggletab.css">
    <link rel="stylesheet" href="forms.css">
    <style>
    .header {
        width: 100%;
        height: 50px;
        margin: 0 auto;
        margin-top: 10px;
        border-bottom: 3px solid #ff4545;
        text-align: center;
    }

    h2 {
        color: #ff4545;
        margin: 0 auto;
    }

.newclassbutton {
    font-size: 25px;
    font-weight: bold;
    border: none;
    background-color: transparent;
    color: #a1a1a1;
}

.newclassbutton:hover {
    color: #0eff00;
    background-color: transparent;
    cursor: pointer;
}

.editclassbutton {
    border: none;
    background-color: transparent;
    color: #a1a1a1;
}

.editclassbutton:hover {
    color: #4489ff;
    background-color: transparent;
    cursor: pointer;
}

ul {
    text-align: left;
}

li {
    list-style-type: none;
    padding: 7px;
}

.classlist {
    color: #ff4545;
}

.classlist:nth-child(odd) {
    background-color: #f1f1f1;
}

.classlist:nth-child(even) {
    background-color: #ffe9e9;
}
</style>
</head>
<body>
    <div class="header">
        <h2> Trainer </h2>
        <p style="margin-top:5px;"><i>Welcome, <?php echo $username ?></i></p>
    </div>
    <div id="nav">
        <div class="tab selected" id="personal"><p>Personal Info</p></div>
        <div class="tab" id="classes"><p>Manage Classes</p></div>
        <div class="tab" id="routines"><p>Manage Exercises</p></div>
    </div>
    <div class="view home" id="personal">
        <div class="innerview">
            <form method="post">
                <h3> Personal </h3>
                <span style="color:red;"><?php echo $personalerror ?></span><br>
                <label>Name</label><input name="name" type=text value="<?php echo $username ?>"><span><?php echo $nameerror ?></span><br><br>
                <label>Email</label><input name="email" type=text value=<?php echo $email ?>><span><?php echo $emailerror ?></span><br><br>
                <label>Phone number</label><input name="phone" type=text value=<?php echo $phone ?>><span><?php echo $phoneerror ?></span><br><br>
                <button type=submit name="personal">Update</button>
            </form>
        </div>
    </div>
<div class="view" id="classes">
<div class="innerview">
<?php
    $sql = "select name, gym_name, gym_location from GymClass where trainer_membership_id = $mid";
    $parse = OCI_Parse($db_conn, $sql);
    $r = oci_execute($parse);
    if (!$r) {
        echo "<span style='color:red';> Could not get class info";
    }
    else {
        echo "<h3 style='display: inline-block';> Classes </h3><form method=get action='newclass.php' style='display:inline-block;'><input type=hidden name='mid' value=$mid><button type='submit' class='newclassbutton'>+</button></form><hr><ul style='width: 85%; margin: 0 auto;'>";
        while (($row = oci_fetch_array($parse, OCI_BOTH)) != false) {
            $classname  = $row[0];
            $gymname = $row[1];
            $gymloc = $row[2];
            echo "<li class='classlist'><p style='display:inline-block;'>$classname, $gymname,$gymloc</p><form method=get action='editclass.php' style='display:inline-block;'><input type=hidden name='classname' value='$classname'><input type=hidden name='gymname' value='$gymname'><input type=hidden name='gymloc' value='$gymloc'><input type=hidden name='mid' value=$mid><button type='submit' class='editclassbutton' ><i class='material-icons'>create</i></button></form><hr><ul><li><p style='color:101010; display: inline-block;'>Classes</p><form method=get action='newclass.php' style='display:inline-block;'><input type=hidden name='mid' value=$mid><input type=hidden name='classname' value='$classname'><input type=hidden name='gynname' value='$gymname'><input type=hidden name='gymloc' value='$gymloc'><button type='submit' class='newclassbutton'>+</button></form></li>";
            $sql = "select distinct gc.class_id, gc.name, gc.cost, t.name from gymclass gc, gymuser t where gc.gym_name = '$gymname' and gc.gym_location = '$gymloc' and gc.trainer_membership_id = t.membership_id order by gc.name";
            $parseclass = OCI_Parse($db_conn, $sql);
            oci_execute($parseclass);
            
            while (($classrow = oci_fetch_array($parseclass, OCI_BOTH)) != false) {
                $cid = $classrow[0];
                $classname = $classrow[1];
                $cost = $classrow[2];
                $tname = $classrow[3];
                echo "<li class='classlist'><p style='display:inline-block;'>$classname with $tname, $$cost</p><form method=get action='editclass.php' style='display:inline-block;'><input type=hidden name='classid' value=$cid><input type=hidden name='mid' value=$mid><button type='submit' class='editgymbutton'><i class='material-icons'>create</i></button></form><br></li>";
            }
            
            echo "</ul></li>";
        }
        echo "</ul>";
    }
    ?>
</div>
</div>
<div class="view" id="routines">
<div class="innerview">
<?php
    $sql = "select routine_name, intensity, sets, reps from Routine where membership_id = $mid";
    $parse = OCI_Parse($db_conn, $sql);
    $r = oci_execute($parse);
    if (!$r) {
        echo "<span style='color:red';> Could not get routine info";
    }
    else {
        echo "<h3 style='display: inline-block';> Routines </h3><form method=get action='newroutine.php' style='display:inline-block;'><input type=hidden name='mid' value=$mid><button type='submit' class='newroutinebutton'>+</button></form><hr><ul style='width: 85%; margin: 0 auto;'>";
        while (($row = oci_fetch_array($parse, OCI_BOTH)) != false) {
            $gymname  = $row[0];
            $gymloc = $row[1];
            echo "<li class='gymlist'><p style='display:inline-block;'>$gymname, $gymloc</p><form method=get action='editgym.php' style='display:inline-block;'><input type=hidden name='gymname' value='$gymname'><input type=hidden name='gymloc' value='$gymloc'><input type=hidden name='mid' value=$mid><button type='submit' class='editgymbutton' ><i class='material-icons'>create</i></button></form><hr><ul><li><p style='color:101010; display: inline-block;'>Classes</p><form method=get action='newclass.php' style='display:inline-block;'><input type=hidden name='mid' value=$mid><input type=hidden name='gymname' value='$gymname'><input type=hidden name='gymloc' value='$gymloc'><button type='submit' class='newgymbutton'>+</button></form></li>";
            $sql = "select distinct gc.class_id, gc.name, gc.cost, t.name from gymclass gc, gymuser t where gc.gym_name = '$gymname' and gc.gym_location = '$gymloc' and gc.trainer_membership_id = t.membership_id order by gc.name";
            $parseclass = OCI_Parse($db_conn, $sql);
            oci_execute($parseclass);
            
            while (($classrow = oci_fetch_array($parseclass, OCI_BOTH)) != false) {
                $cid = $classrow[0];
                $classname = $classrow[1];
                $cost = $classrow[2];
                $tname = $classrow[3];
                echo "<li class='classlist'><p style='display:inline-block;'>$classname with $tname, $$cost</p><form method=get action='editclass.php' style='display:inline-block;'><input type=hidden name='classid' value=$cid><input type=hidden name='mid' value=$mid><button type='submit' class='editgymbutton'><i class='material-icons'>create</i></button></form><br></li>";
            }
            
            echo "</ul></li>";
        }
        echo "</ul>";
    }
    ?>
</div>
</div>
</body>
</html>