<html>

	<head>
		<title> Athlete Home </title>
	</head>

	<body>
		<div>
			
			<?php
				include 'basesqlexecutors.php';
				$membershipID = $_GET['membershipID'];
				$memberInfo = executePlainSQL("select * from GymUser where membership_id=$membershipID");
				printInfo($memberInfo);
			?>
			
		</div>

		<div>
			<h2> Classes in which you are currently enrolled: </h2>
				<?php
					$enrolledClasses = executePlainSQL("select gc.name, gc.class_id, a.class_id, a.membership_id from GymClass gc, Attends a where gc.class_id=a.class_id and membership_id=$membershipID");
					printClasses($enrolledClasses);
				?>
			<hr />	
		</div>
		
		<div>
			<a href="viewClasses.php">Sign up for more classes</p>
		</div>
		

	</body>
</html>

<?php

function printInfo($result){
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<h1>Welcome " . $row["NAME"] . "!</h1>";
		echo "<p><b>ID: </b><i>" . $row["MEMBERSHIP_ID"] . "</i><br>";
		echo "<b>Email: </b><i>" . $row["EMAIL"] . "</i><br>";
		echo "<b>Phone Number: </b><i>" . $row["PHONE_NUMBER"] . "</i></p>";
		echo "<hr>";
	}
}

function printClasses($result){
	echo "<p><ul>";
	while ($row = OCI_Fetch_Array($result, OCI_BOTH)){
		echo "<li><a href='specificClass.php?classID=" .$row["CLASS_ID"] . "'>" . $row["NAME"] . "</li>";
	}
	echo "</ul></p>";
}

?>
