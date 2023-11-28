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
		<a id="back-btn" href="./home.php">
			<input type="button" value="Back" />
		</a>
		<form class="cd-form" method="POST" action="#">
			<legend>Enter the details</legend>
			
				<div class="error-message" id="error-message">
					<p id="error"></p>
				</div>
				
				<div class="icon">
					<input class="b-isbn" type='text' name='b_isbn' id="b_isbn" placeholder="Book ISBN" required />
				</div>
					
				<div class="icon">

					<input class="b-copies" type="number" max=30 min=0 name="b_copies" placeholder="Available copies" required />

				</div>
						
				<input type="submit" name="b_add" value="Update Copies" />
		</form>
		<?php
			$query = $con->prepare("SELECT * FROM book ORDER BY title");
			$query->execute();
			$result = $query->get_result();
			if(!$result)
				die("ERROR: Couldn't fetch books");
			$rows = mysqli_num_rows($result);
			if($rows == 0)
				echo "<h2 align='center'>No books available</h2>";
			else
			{
				echo "<div style='margin: 30px'>";
				echo "<h2 align='center'>Available books</h2>";
				echo "<div class='error-message' id='error-message'>
						<p id='error'></p>
					</div>";
				echo "<table width='100%' cellpadding=10 cellspacing=10>";
				echo "<tr>
						<th></th>
						<th>ISBN<hr></th>
						<th>Title<hr></th>
						<th>Author<hr></th>
						<th>Category<hr></th>
						<th>Price<hr></th>
						<th>Copies available<hr></th>
					</tr>";
				for($i=0; $i<$rows; $i++)
				{
					$row = mysqli_fetch_array($result);
					echo "<tr>
							<td>
								<label class='control control--radio'>
									<input type='radio' name='rd_book' value=".$row[0]." />
								<div class='control__indicator'></div>
							</td>";
					for($j=0; $j<6; $j++)
						if($j == 4)
							echo "<td>$".$row[$j]."</td>";
						else
							echo "<td>".$row[$j]."</td>";
					echo "</tr>";
				}
				echo "</table>";
				echo "</div>";
			}
			?>
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
				$query = $con->prepare("UPDATE book SET copies = ? WHERE isbn = ?;");
				$query->bind_param("ds", $_POST['b_copies'], $_POST['b_isbn']);
				if(!$query->execute())
					die(error_without_field("ERROR: Couldn\'t add copies"));
				echo success("Copies successfully updated");
			}
		}
	?>
</html>