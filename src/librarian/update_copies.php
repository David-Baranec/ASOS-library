<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
?>

<html>
	<head>
		<title>Update copies</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
		<link rel="stylesheet" href="css/update_copies_style.css">
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
                    <a href="#" onclick="redirectToPage('insert_book.php')">Add a new book</a>
                    <a href="#" onclick="redirectToPage('update_balance.php')">Update balance of a member</a>
                    <a href="#" onclick="redirectToPage('due_handler.php')">Reminders for today</a>
					<a href="../logout.php">Logout</a>
                </div>
            </button>
        </div>
    </header>

	<body>
		<form class="cd-form" method="POST" action="#">
			<legend>Enter the details</legend>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				
				<div class="icon">
					<input class="b-isbn" type='text' name='b_isbn' id="b_isbn" placeholder="Book ISBN" required />
				</div>
					
				<div class="icon">
					<input class="b-copies" type="number" name="b_copies" placeholder="Copies to add" required />
				</div>
						
				<input type="submit" name="b_add" value="Add Copies" />
		</form>
	</body>
	
	<?php
		if(isset($_POST['b_add']))
		{
			$query = $con->prepare("SELECT isbn FROM book WHERE isbn = ?;");
			$query->bind_param("s", $_POST['b_isbn']);
			$query->execute();
			if(mysqli_num_rows($query->get_result()) != 1)
				echo error_with_field("Invalid ISBN", "b_isbn");
			else
			{
				$query = $con->prepare("UPDATE book SET copies = copies + ? WHERE isbn = ?;");
				$query->bind_param("ds", $_POST['b_copies'], $_POST['b_isbn']);
				if(!$query->execute())
					die(error_without_field("ERROR: Couldn\'t add copies"));
				echo success("Copies successfully updated");
			}
		}
	?>
</html>