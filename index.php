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
      <title>Choose interface</title>
      
      <style type = "text/css">
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
	
      <div align = "center">
         <div style = "width:300px; border: solid 1px #333333; " align = "left">
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><b>Login</b></div>
				
            <div style = "margin:30px">
               <form action = "" method = "post">
                  <label>Membership ID: </label><input type = "text" name = "memberid" class = "box"/><br /><br />
                  <input type = "submit" value = " Submit "/><br />
               </form>
               
               <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
					
            </div>
				
         </div>
			
      </div>

   </body>
</html>
