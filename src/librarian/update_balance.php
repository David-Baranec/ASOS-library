<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
?>

<html>
	<head>
		<title>Update balance</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
		<link rel="stylesheet" href="css/update_balance_style.css">
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
                    <a href="#" onclick="redirectToPage('update_copies.php')">Update copies of a book</a>
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
					<input class="m-user" type='text' name='m_user' id="m_user" placeholder="Member username" required />
				</div>
				
				<div class="icon">
					<input class="m-balance" type="number" name="m_balance" placeholder="Balance to add" required />
				</div>
				
				<input type="submit" name="m_add" value="Add Balance" />
		</form>
	</body>
	
	<?php
		if(isset($_POST['m_add']))
		{
			$query = $con->prepare("SELECT username FROM member WHERE username = ?;");
			$query->bind_param("s", $_POST['m_user']);
			$query->execute();
			if(mysqli_num_rows($query->get_result()) != 1)
				echo error_with_field("Invalid username", "m_user");
			else
			{
				$query = $con->prepare("UPDATE member SET balance = balance + ? WHERE username = ?;");
				$query->bind_param("ds", $_POST['m_balance'], $_POST['m_user']);
				if(!$query->execute())
					die(error_without_field("ERROR: Couldn\'t add balance"));
				echo success("Balance successfully updated");
			}
		}
	?>
</html>