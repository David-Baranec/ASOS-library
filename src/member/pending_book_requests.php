<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_member.php";
	require "header_member.php";
?>

<html>
<head>
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
			$userId = $_SESSION['username']; 
			$query = $con->prepare("SELECT  * FROM pending_book_requests WHERE member = ?;");
			$query->bind_param("s", $userId);
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
				echo "<input type='submit' value='Cancel Requet' name='m_cancel'/>";
				echo "</div>";
				echo "</form>";
		}

		if(isset($_POST['m_cancel'])) {
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

					$query = $con->prepare("SELECT title FROM book WHERE isbn = ?;");
					$query->bind_param("s", $isbn);
					$query->execute();
					$title = mysqli_fetch_array($query->get_result())[0];

					
					$query = $con->prepare("DELETE FROM pending_book_requests WHERE request_id = ?");
					$query->bind_param("d", $request_id);
					if(!$query->execute())
						die(error_without_field("ERROR: Couldn\'t cancel values"));
				}
			}
			if($requests > 0){

				echo success("Successfully canceled ".$requests." requests");
				header("refresh:1; url=pending_book_requests.php");
			}
			else
				echo error_without_field("No request selected");

			header("refresh:1; url=pending_book_requests.php");
		}			
			
					