<!DOCTYPE html>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>

<h1>Crickets Database: View Data</h1>
<p>(Select the table you want to view data from)</p><br>

<?php
//define variables
$nameErr = "";
$name = "";
$valid = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["tname"]))
    $nameErr = "Table is required";
  else
  {
    $name = $_POST["tname"];

    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
      $nameErr = "Only letters and white space allowed";
    }
    else
    	$valid = 1;
  }
}
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
  Table Name: 
  <select name="tname">
    <option value="">Select Table</option>
    <option value="TestInstance">TestInstance Table</option>
    <option value="Cricket">Cricket Table</option>
    <option value="Test">Test Table</option>
    <option value="Project">Project Table</option>
    <option value="Observer">Observer Table</option>
    <option value="CricketTestInstance">CricketTestInstance Table</option>
  </select>
  <span class="error"><?php echo $nameErr;?></span><br><br>
  <input type="submit">
</form><br><br>

<h1>Data From <?php echo $name;?> Table:</h1>

<?php
// Only query the database if the table name is valid
if($valid == 1)
{
	// Connection string
	$conn = oci_connect('aabeyer', 'Piggies3!', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');

	$query = "SELECT * FROM " . $name;
	$stid = oci_parse($conn,$query);
	oci_execute($stid,OCI_DEFAULT);

	// If there are no records, say so.
	if(oci_fetch_all($stid, $res) < 1)
	{
		echo "No results for '" . $name ."'";
		echo '<br/>';
	}

	// Execute the query a second time because I used the fetch_all function above
	oci_execute($stid,OCI_DEFAULT);

    //---Start of data table---
    echo "<table style='border: 1px solid black'>";
    
	//iterate through each row
	while ($row = oci_fetch_array($stid,OCI_RETURN_NULLS + OCI_ASSOC))
	{
        echo "<tr style='border: 1px solid black'>";
		//iterate through each item in the row and echo it
		foreach ($row as $item)
		{
            //echo $item.' ';
            echo "<td style='border: 1px solid black'>&nbsp&nbsp".$item."&nbsp&nbsp</td>"; 
        }
        echo "</tr>";
	}
	oci_free_statement($stid);
    oci_close($conn);
    
    //---End of data table---
    echo "</table>";
}
?>

</body>
</html>
