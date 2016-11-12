<!DOCTYPE html>

<html>

	<head>
		<title>View Classes Page</title>
	</head>

	<body>
		<div>
			<h1>All classes displayed here</h1>
		</div>
		
		<div>
			<h2>Classes currently available</h2>

			<?php

			include 'basesqlexecutors.php';
			$classNames = executePlainSQL("select name, class_id from GymClass");
			printClassNames($classNames);

			?>
			
		</div>

	</body>

</html>

<?php

// Prints the names of all available classes as hyperlinks
function printClassNames($result) {
	echo "<ul>";

	while ($row = OCI_Fetch_Array($result, OCI_BOTH)) {
		echo "<li><a href='specificClass.php?classID=" .$row["CLASS_ID"] ."'>" . $row["NAME"] . "</a></li>";
	}
	echo "</ul>";
}

?>
