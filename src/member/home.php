<?php
    require "../db_connect.php";
    require "../message_display.php";
    require "verify_member.php";
    require "header_member.php";
?>

<html>
<head>
    <title>Welcome</title>
    <link rel="stylesheet" type="text/css" href="../css/global_styles.css">
    <link rel="stylesheet" type="text/css" href="css/home_style.css">
    <link rel="stylesheet" type="text/css" href="../css/custom_checkbox_style.css">
</head>
<body>
    <?php

		$query2 = $con->prepare("SELECT book_isbn FROM `book_issue_log` where member like ?");
		$query2->bind_param("s", $_SESSION['username']);
		$query2->execute();
		$result2 = $query2->get_result();
		if(mysqli_num_rows($result2)>0){
			echo "<h2 align='center'> We have something to recommend</h2>";

			echo "<br /><br /><form class='cd-form' method='POST' action='#'>";
			echo "<label for='search'>Search:</label>";
			echo "<input type='text' name='search' id='search' placeholder='Enter title or author'>";
			echo "<input type='submit' value='Search'>";
			echo "</form>";

			$query3 = $con->prepare("SELECT category FROM `book` where isbn=?");
			$book= mysqli_fetch_row($result2);
			$query3->bind_param("s", $book[0]);
			$query3->execute();
			$result3 = $query3->get_result();
			if(mysqli_num_rows($result3)>0)
				{
					$query4 = $con->prepare("SELECT * FROM `book` where category like ? and copies>0");
					$book_category= mysqli_fetch_row($result3);
					$query4->bind_param("s", $book_category[0]);
					$query4->execute();
					$result4 = $query4->get_result();
					$rows= mysqli_num_rows($result4);

					if (isset($_POST['search'])) {
						$searchTerm = '%' . $_POST['search'] . '%';
						$query4 = $con->prepare("SELECT * FROM `book` WHERE category LIKE ? AND copies > 0 AND (title LIKE ? OR author LIKE ?)");
						$query4->bind_param("sss", $book_category[0], $searchTerm, $searchTerm);
					} else {
						$query4 = $con->prepare("SELECT * FROM `book` WHERE category LIKE ? AND copies > 0");
						$query4->bind_param("s", $book_category[0]);
					}
					
					$query4->execute();
					$result4 = $query4->get_result();
					$rows = mysqli_num_rows($result4);
					
					if($rows){
						echo "<form class='cd-form' method='POST' action='#'>";
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
							$row = mysqli_fetch_array($result4);
							
							// Check if the book has not been requested by the user
							$bookId = $row[0];
							$userId = $_SESSION['username']; // Assuming you have a user session
							$requestCheckQuery = $con->prepare("SELECT * FROM pending_book_requests WHERE member = ? AND book_isbn = ?;");
							$requestCheckQuery->bind_param("ii", $userId, $bookId);
							$requestCheckQuery->execute();
							$requestCheckResult = $requestCheckQuery->get_result();
							$bookNotRequested = mysqli_num_rows($requestCheckResult) == 0;

							$requestCheckQuery = $con->prepare("SELECT * FROM book_issue_log WHERE member = ? AND book_isbn = ?;");
							$requestCheckQuery->bind_param("ii", $userId, $bookId);
							$requestCheckQuery->execute();
							$requestCheckResult = $requestCheckQuery->get_result();
							$bookNotIssued = mysqli_num_rows($requestCheckResult) == 0;

							if ($bookNotRequested && $bookNotIssued){
							echo "<tr>
									<td>
										<label class='control control--checkbox'>
											<input type='checkbox' name='cb_book[]' value=".$row[0]." />
											<div class='control__indicator'></div>
										</label>
									</td>";
							for($j=0; $j<6; $j++)
								if($j == 4)
									echo "<td>$".$row[$j]."</td>";
								else
									echo "<td>".$row[$j]."</td>";
							echo "</tr>";
							}
						}
						echo "</table>";
						echo "<br /><br /><input type='submit' name='m_request' value='Request book' />";
						echo "</form>";
					}
				}
		}

		else {
        $query = $con->prepare("SELECT * FROM book  where copies>0 ORDER BY title");
        $query->execute();
        $result = $query->get_result();
        
        if(!$result)
            die("ERROR: Couldn't fetch books");
        
        $rows = mysqli_num_rows($result);
        
        if($rows == 0)
            echo "<h2 align='center'>No books available</h2>";
        else
        {

			if (isset($_POST['search'])) {
				$searchTerm = '%' . $_POST['search'] . '%';
				$query = $con->prepare("SELECT * FROM book WHERE copies > 0 AND (title LIKE ? OR author LIKE ?) ORDER BY title");
				$query->bind_param("ss", $searchTerm, $searchTerm);
			} else {
				$query = $con->prepare("SELECT * FROM book WHERE copies > 0 ORDER BY title");
			}
			
			$query->execute();
			$result = $query->get_result();
			$rows = mysqli_num_rows($result);

            echo "<form class='cd-form' method='POST' action='#'>";
            echo "<legend>Available books</legend>";
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

				$bookId = $row[0];
				$userId = $_SESSION['username']; // Assuming you have a user session
				$requestCheckQuery = $con->prepare("SELECT * FROM pending_book_requests WHERE member = ? AND book_isbn = ?;");
				$requestCheckQuery->bind_param("ii", $userId, $bookId);
				$requestCheckQuery->execute();
				$requestCheckResult = $requestCheckQuery->get_result();
				$bookNotRequested = mysqli_num_rows($requestCheckResult) == 0;

				$requestCheckQuery = $con->prepare("SELECT * FROM book_issue_log WHERE member = ? AND book_isbn = ?;");
				$requestCheckQuery->bind_param("ii", $userId, $bookId);
				$requestCheckQuery->execute();
				$requestCheckResult = $requestCheckQuery->get_result();
				$bookNotIssued = mysqli_num_rows($requestCheckResult) == 0;

				if ($bookNotRequested && $bookNotIssued){
                echo "<tr>
                        <td>
                            <label class='control control--checkbox'>
                                <input type='checkbox' name='cb_book[]' value=".$row[0]." />
                                <div class='control__indicator'></div>
                            </label>
                        </td>";
                for($j=0; $j<6; $j++)
                    if($j == 4)
                        echo "<td>$".$row[$j]."</td>";
                    else
                        echo "<td>".$row[$j]."</td>";
                echo "</tr>";
				}
            }
            echo "</table>";
            echo "<br /><br /><input type='submit' name='m_request' value='Request selected books' />";
            echo "</form>";
        }
	}

		if(isset($_POST['m_request']))
		{
			if(empty($_POST['cb_book']))
				echo error_without_field("Please select at least one book to request");
			else
			{
				$selectedBooks = $_POST['cb_book'];

				// Loop through the selected books and process the request for each one
				foreach ($selectedBooks as $selectedBook) {
					$query = $con->prepare("SELECT copies FROM book WHERE isbn = ?;");
					$query->bind_param("s", $selectedBook);
					$query->execute();
					$copies = mysqli_fetch_array($query->get_result())[0];

					if($copies == 0)
						echo error_without_field("No copies of the selected book ($selectedBook) are available");
					else
					{
						$query = $con->prepare("SELECT request_id FROM pending_book_requests WHERE member = ? AND book_isbn = ?;");
						$query->bind_param("ss", $_SESSION['username'], $selectedBook);
						$query->execute();

						if(mysqli_num_rows($query->get_result()) > 0)
							echo error_without_field("You have already requested the book ($selectedBook)");
						else
						{
							$query = $con->prepare("SELECT book_isbn FROM book_issue_log WHERE member = ? AND book_isbn = ?;");
							$query->bind_param("ss", $_SESSION['username'], $selectedBook);
							$query->execute();
							$result = $query->get_result();

							if(mysqli_num_rows($result) > 0)
								echo error_without_field("You have already issued a copy of the book ($selectedBook)");
							else
							{
								$query = $con->prepare("SELECT balance FROM member WHERE username = ?;");
								$query->bind_param("s", $_SESSION['username']);
								$query->execute();
								$memberBalance = mysqli_fetch_array($query->get_result())[0];

								$query = $con->prepare("SELECT price FROM book WHERE isbn = ?;");
								$query->bind_param("s", $selectedBook);
								$query->execute();
								$bookPrice = mysqli_fetch_array($query->get_result())[0];

								if($memberBalance < $bookPrice)
									echo error_without_field("You do not have sufficient balance to request the book ($selectedBook)");
								else
								{
									$query = $con->prepare("INSERT INTO pending_book_requests(member, book_isbn) VALUES(?, ?);");
									$query->bind_param("ss", $_SESSION['username'], $selectedBook);
									
									if(!$query->execute())
										echo error_without_field("ERROR: Couldn't request book");
									else {
										$bookNames = implode(', ', $selectedBooks);
										echo success("Book ($bookNames) successfully requested. Wait for Admin to approve!");
									}
								}
							}
						}
					}
				}
			}
		}
		
		?>

</body>
</html>
