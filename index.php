<?php
	include 'basesqlexecutors.php';
   
	if($_SERVER["REQUEST_METHOD"] == "POST") {
	  $membership = $_POST["memberid"];
      
      $sql = "select Count(*) from gymuser where membership_id = $membership";
      $result = executePlainSQL($sql);
      $row = oci_fetch_array($result);
      $count = $row[0];
      // If result matched, table row must be 1 row
      if($count == 1) {
         
         $sqladmin = "select Count(*) from gymadmin where membership_id = $membership";
         $sqlathlete = "select Count(*) from athlete where membership_id = $membership";
         $sqltrainer = "select Count(*) from trainer where membership_id = $membership";
         
         $resultadmin = executePlainSQL($sqladmin);
		 $rowadmin = oci_fetch_array($resultadmin);
		 $countadmin = $rowadmin[0];
		 
		 if ($countadmin == 1) {
			 header("Location: admin.php?mid=$membership");
		 }

		 $resultathlete = executePlainSQL($sqlathlete);
		 $rowathlete = oci_fetch_array($resultathlete);
		 $countathlete = $rowathlete[0];
		 
		 if ($countathlete == 1) {
			 header("Location: athlete.php?mid=$membership");

			 header("Location: athlete.php?mid=$membership");

		 }

		 $resulttrainer = executePlainSQL($sqltrainer);
		 $rowtrainer = oci_fetch_array($resulttrainer);
		 $counttrainer = $rowtrainer[0];
		 
		 if ($counttrainer == 1) {
			 header("Location: trainer.php?mid=$membership");
		 }
		 
		 $error = "Membership ID invalid";
      }else {
         $error = "Membership ID invalid";
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

</head>
   <head>
      <title>Choose interface</title>

      <style type = "text/css">
.header {
width: 100%;
height: 50px;
margin: 0 auto;
    margin-top: 10px;
    padding-bottom: 10px;
    border-bottom: 3px solid #ff4545;
    text-align: center;
}
h2 {
color: #ff4545;
margin: 0 auto;
padding-top: 15px;
}
         body {
            font-family:Arial, Helvetica, sans-serif;
            font-size:14px;
         }
         
         label {
            font-weight:bold;
            width:100px;
            font-size:14px;
         }
         
         .box {
            border:#666666 solid 1px;
         }
      </style>
      
   </head>
   
   <body bgcolor = "#FFFFFF">
<div class="header">
<h2> Welcome, Please Log In </h2>
</div>
      <div align = "center">
				
            <div style = "margin:30px"><br><br><br>
               <form action = "" method = "post">
                  <label>Membership ID: </label><input type = "text" name = "memberid" class = "box"/><br /><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>
               
               <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
					
            </div>
			
      </div>

   </body>
</html>
