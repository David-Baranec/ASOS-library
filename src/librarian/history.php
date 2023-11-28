<?php
	require "../db_connect.php";
	require "../message_display.php";
	require "verify_librarian.php";
	require "header_librarian.php";
?>

<html>
	<head>
		<title>History</title>
		<link rel="stylesheet" type="text/css" href="../css/global_styles.css" />
		<link rel="stylesheet" type="text/css" href="../css/form_styles.css" />
		<link rel="stylesheet" href="css/update_copies_style.css">
	</head>
	<body>
		
		<?php
		$query = $con->prepare("SELECT * FROM book_history_log  ");
        $query->execute();
        $result = $query->get_result();
        if(!$result)
            die("ERROR: Couldn't fetch books");
        $rows = mysqli_num_rows($result);
        if($rows == 0)
            echo "<h2 align='center'>No borrows yet</h2>";
        else
        {
            echo "<form class='cd-form' method='POST' action='#'>";
            echo "<legend>History</legend>";
            echo "<div class='error-message' id='error-message'>
                    <p id='error'></p>
                </div>";
            echo "<table width='100%' cellpadding=10 cellspacing=10>";
            echo "<tr>
                    <th>id </th>
                    <th>member<hr></th>
                    <th>book_isbn<hr></th>
                    <th>borrowing_date<hr></th>
                    <th>return_date<hr></th>
                </tr>";
            for($i=0; $i<$rows; $i++)
            {
                $row = mysqli_fetch_array($result);
                echo "<tr>";
                for($j=0; $j<5; $j++)
                    echo "<td>".$row[$j]."</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</form>";
        }
			?>
	</body>

</html>