<!DOCTYPE html>

<!-- Comments here
	head contains information
	title is the title of the page or the tab
	body contains the actual info
	<h1> is title header
	<p> is new paragraph
	<hr> gives horizontal line
	<br> starts a new line
	<a href="[some website here]">[Click here!]</a> takes you to [some website] when [Click here!] link is pressed 
	<ul> <li> </li> </ul> creates a bullet pointed unordered list and each element wrapped in li tag
	<ol> used for numbered or ordered lists
	<img src="[location]" width="200" height="100" alt="My image" /> displays image from [location] with width and height and for 	text only browsers, image displayed as "My image"
	<div> </div> use to separate parts of page
	html entities to find useful html stuff like copywrite symbols
	can give a tag a unique ID by saying ex. <div id="[id]"> which allows us to identify header by id
	can give tag a class also by saying ex. <div class="[class]"> which allows us to identify element(s) by class
		use classes and ids for style sheet purposes
	<span class="red">[word</span]> allows word to be grouped under class red, can insert in the middle of a line
	<[tag] style="[property:value;]" changes specified property of tag to specified style
	check out formatting text, not important and can add later
	<q> for indented quotations
	<address> for contact information
-->

<html>

	<head>
		<title> Athlete Home </title>
	</head>

	<body>
		<div>
			<h1> Welcome athlete! </h1>
		</div>

		<div>
			<h2> Classes in which you are currently enrolled: </h2>
			<ul>
				<li><a href="specificClass.php">Class 1</a></li>
				<li><a href="specificClass.php">Class 2</a></li>
			</ul>
			
			<hr />	
		</div>
		
		<div>
			<a href="viewClasses.php">Sign up for more classes</p>
		</div>
		

	</body>
</html>
