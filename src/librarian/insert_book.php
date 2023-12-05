<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
?>

<html>
	<head>
		<title>Add book</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
		<link rel="stylesheet" href="css/insert_book_style.css">
		<link rel="stylesheet" type="text/css" href="css/header_librarian_style.css" />
		<script>
        function redirectToPage(page) {
            window.location.href = page;
        }
    </script>
</head>

<body>
    <header>
        <div class="cd-logo">
            <a href="../librarian/home.php">
                <img src="img/ic_logo.svg" alt="Logo" />
                <p>LIBRARY</p>
            </a>
        </div>
        
        <div class="dropdown">
            <button class="dropbtn">
                <p id="librarian-name"><?php echo $_SESSION['username'] ?></p>
                <div id="allTheThings" class="dropdown-content">
					<a href="#" onclick="redirectToPage('home.php')">Home</a>
                    <a href="#" onclick="redirectToPage('pending_book_requests.php')">Pending book requests</a>
                    <a href="#" onclick="redirectToPage('pending_book_requests.php')">Pending book requests</a>
                    <a href="#" onclick="redirectToPage('update_copies.php')">Update copies of a book</a>
                    <a href="#" onclick="redirectToPage('update_balance.php')">Update balance of a member</a>
                    <a href="#" onclick="redirectToPage('due_handler.php')">Reminders for today</a>
					<a href="../logout.php">Logout</a>
                </div>
            </button>
        </div>
    </header>
	<body>
		<a id="back-btn" href="./home.php">
			<input type="button" value="Back" />
		</a>
		<form class="cd-form" method="POST" action="#" enctype="multipart/form-data" >
			<legend>Enter book details</legend>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				
				<div class="icon">
					<input class="b-isbn" id="b_isbn" type="number"  min= 1 max=9999999999999 name="b_isbn" placeholder="ISBN" required />
				</div>
				
				<div class="icon">
					<input class="b-title" type="text" name="b_title" placeholder="Title" required />
				</div>
				
				<div class="icon">
					<input class="b-author" type="text" name="b_author" placeholder="Author" required />
				</div>
				
				<div>
				<h4>Category</h4>
				
					<p class="cd-select icon">
						<select class="b-category" name="b_category">
							<option>Fiction</option>
							<option>Non-fiction</option>
							<option>Education</option>
							<option>Fairy Tale</option>
							<option>Fantasy</option>
							<option>Horror</option>
							<option>Romance</option>
							<option>Biography</option>
							<option>Mystery</option>
							<option>Cook Books</option>
						</select>
					</p>
				</div>
				
				
				<div class="icon">
					<input class="b-price" type="number" min=1 max=100 name="b_price" placeholder="Price" required />
				</div>
				
				<div class="icon">
					<input class="b-copies" type="number" min=1 max=100 name="b_copies" placeholder="Copies" required />
				</div>
				</div>
				<div>
				<label for="photo">Upload Photo:</label>
       			<input type="file" name="photo" id="photo" accept="image/*" required>
					</div>
				
				<br />
				<input class="b-isbn" type="submit" name="b_add" value="Add book" />
		</form>
	<body>
	
	<?php
		if(isset($_POST['b_add']))
		{
			$query = $con->prepare("SELECT isbn FROM book WHERE isbn = ?;");
			$query->bind_param("s", $_POST['b_isbn']);
			$query->execute();
			
			if(mysqli_num_rows($query->get_result()) != 0)
				echo error_with_field("A book with that ISBN already exists", "b_isbn");
			else
			{
				$query = $con->prepare("INSERT INTO book VALUES(?, ?, ?, ?, ?, ?);");
				$query->bind_param("ssssdd", $_POST['b_isbn'], $_POST['b_title'], $_POST['b_author'], $_POST['b_category'], $_POST['b_price'], $_POST['b_copies']);
				
				if(!$query->execute())
					die(error_without_field("ERROR: Couldn't add book"));
				echo success("Successfully added book");
				$targetDir = "../member/books/";
				$isbn = $_POST["b_isbn"];
				// Create the directory if it doesn't exist
				if (!file_exists($targetDir)) {
					mkdir($targetDir, 0777, true);
				}
			
				// Get the uploaded file name
				$fileName = basename($_FILES["photo"]["name"]);
				echo $fileName;
				$targetFilePath = $targetDir . $isbn . ".jpeg";

				// Check if file type is allowed (you can customize this based on your needs)
				$allowedTypes = array('jpeg');
				$fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
			
				if (in_array($fileType, $allowedTypes)) {
					// Upload file to the specified directory
					if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
						//echo "The file " . $fileName . " has been uploaded.";
					} else {
						echo "Sorry, there was an error uploading your file.";
					}
				} else {
					echo "Sorry, only JPG, JPEG files are allowed.";
				}
			}
		}
	?>
</html>