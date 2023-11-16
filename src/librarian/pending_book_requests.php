<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	// require "header_librarian.php";
?>

<html>
<head>
    <title>Pending Book Requests</title>
    <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,700">
    <link rel="stylesheet" type="text/css" href="css/header_librarian_style.css" />
    <link rel="stylesheet" type="text/css" href="../css/global_styles.css">
    <link rel="stylesheet" type="text/css" href="../css/custom_checkbox_style.css">

    <!-- Add this script to handle dropdown item clicks and redirect -->
    <script>
        function redirectToPage(page) {
            window.location.href = page;
        }
    </script>
</head>

<body>
    <header>
        <div class ="cd-logo">
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
                    <a href="#" onclick="redirectToPage('pending_registrations.php')">Pending registrations</a>
                    <a href="#" onclick="redirectToPage('pending_book_requests.php')">Pending book requests</a>
                    <a href="#" onclick="redirectToPage('insert_book.php')">Add a new book</a>
                    <a href="#" onclick="redirectToPage('update_copies.php')">Update copies of a book</a>
                    <a href="#" onclick="redirectToPage('update_balance.php')">Update balance of a member</a>
                    <a href="#" onclick="redirectToPage('due_handler.php')">Reminders for today</a>
					<a href="../logout.php">Logout</a>
                </div>
            </button>
        </div>
    </header>


	<head>
		<title>Pending Book Requests</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css">
		<link rel="stylesheet" type="text/css" href="../css/custom_checkbox_style.css">
		<link rel="stylesheet" type="text/css" href="css/pending_book_requests_style.css">
	</head>
	
	<body>
		<a id="back-btn" href="./home.php">
			<input type="button" value="Back" />
		</a>
		<?php
			$query = $con->prepare("SELECT * FROM pending_book_requests;");
			$query->execute();
			$result = $query->get_result();;
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>No requests pending</h2>";
			else
			{
				echo "<form class='cd-form' method='POST' action='#'>";
				echo "<legend>Pending book requests</legend>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding=10 cellspacing=10>
						<tr>
							<th></th>
							<th>Username<hr></th>
							<th>Book<hr></th>
							<th>Time<hr></th>
						</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					echo "<tr>";
					echo "<td>
							<label class='control control--checkbox'>
								<input type='checkbox' name='cb_".$i."' value='".$row[0]."' />
								<div class='control__indicator'></div>
							</label>
						</td>";
					for($j=1; $j<4; $j++)
						echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "<br /><br /><div style='float: right;'>";
				echo "<input type='submit' value='Reject selected' name='l_reject' />&nbsp;&nbsp;&nbsp;&nbsp;";
				echo "<input type='submit' value='Grant selected' name='l_grant'/>";
				echo "</div>";
				echo "</form>";
			}
			
			//$header = 'From: <noreply@library.com>' . "\r\n";
			
			if(isset($_POST['l_grant'])) {
				$requests = 0;
				for($i=0; $i<$rows; $i++) {
					if(isset($_POST['cb_'.$i])) {
						$request_id =  $_POST['cb_'.$i];
			
						// Query to fetch member and ISBN
						$query = $con->prepare("SELECT member, book_isbn FROM pending_book_requests WHERE request_id = ?;");
						$query->bind_param("d", $request_id);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						$query->close(); // Close the result set
			
						// Query to insert into book_issue_log
						$query = $con->prepare("INSERT INTO book_issue_log(member, book_isbn) VALUES(?, ?);");
						$query->bind_param("ss", $member, $isbn);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn\'t issue book"));
						$query->close(); // Close the result set
						$requests++;
			
						// Query to fetch email
						$query = $con->prepare("SELECT email FROM member WHERE username = ?;");
						$query->bind_param("s", $member);
						$query->execute();
						//$to = mysqli_fetch_array($query->get_result())[0];
						//$subject = "Book successfully issued";
						$query->close(); // Close the result set
			
						// Query to fetch book title
						$query = $con->prepare("SELECT title FROM book WHERE isbn = ?;");
						$query->bind_param("s", $isbn);
						$query->execute();
						$title = mysqli_fetch_array($query->get_result())[0];
						$query->close(); // Close the result set
			
						// Query to fetch due date
						$query = $con->prepare("SELECT due_date FROM book_issue_log WHERE member = ? AND book_isbn = ?;");
						$query->bind_param("ss", $member, $isbn);
						$query->execute();
						$due_date = mysqli_fetch_array($query->get_result())[0];
						$query->close(); // Close the result set
			
						//$message = "The book '".$title."' with ISBN ".$isbn." has been issued to your account. The due date to return the book is ".$due_date.".";
						//mail($to, $subject, $message, $header);
					}
				}
			
				if($requests > 0)
					echo success("Successfully granted ".$requests." requests");
				else
					echo error_without_field("No request selected");

				header("refresh:1; url=pending_book_requests.php");
			}			

			if(isset($_POST['l_reject'])) {
				$requests = 0;
				for($i=0; $i<$rows; $i++) {
					if(isset($_POST['cb_'.$i])) {
						$requests++;
						$request_id =  $_POST['cb_'.$i];
			
						// Query to fetch member and ISBN
						$query = $con->prepare("SELECT member, book_isbn FROM pending_book_requests WHERE request_id = ?;");
						$query->bind_param("d", $request_id);
						$query->execute();
						$resultRow = mysqli_fetch_array($query->get_result());
						$member = $resultRow[0];
						$isbn = $resultRow[1];
						$query->close(); // Close the result set
			
						// Query to fetch email
						$query = $con->prepare("SELECT email FROM member WHERE username = ?;");
						$query->bind_param("s", $member);
						$query->execute();
						$to = mysqli_fetch_array($query->get_result())[0];
						$query->close(); // Close the result set
			
						// Query to fetch book title
						$query = $con->prepare("SELECT title FROM book WHERE isbn = ?;");
						$query->bind_param("s", $isbn);
						$query->execute();
						$title = mysqli_fetch_array($query->get_result())[0];
						$query->close(); // Close the result set
			
						// Query to delete the pending book request
						$query = $con->prepare("DELETE FROM pending_book_requests WHERE request_id = ?");
						$query->bind_param("d", $request_id);
						if(!$query->execute())
							die(error_without_field("ERROR: Couldn't delete values"));
						$query->close(); // Close the result set
			
						// Now you can send the email if needed
						// mail($to, $subject, $message, $header);
					}
				}
			
				if($requests > 0)
					echo success("Successfully deleted ".$requests." requests");
				else
					echo error_without_field("No request selected");

				header("refresh:1; url=pending_book_requests.php");
			}			