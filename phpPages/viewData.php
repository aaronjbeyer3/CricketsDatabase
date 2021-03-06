<!DOCTYPE html>
<html>
<head>
    <style>
        .error {
            color: #FF0000;
        }
        td {
            border: 1px solid black;  
            padding: 5px;        
        }
    </style>

    <h1>Crickets Database: View Data</h1>
    <table>
        <tr>
            <td><input type="button" onclick="location.href='index.html';" value="Home" /></td>
            <td><input type="button" onclick="location.href='uploadData.php';" value="Upload Data" /></td>
            <td><input type="button" onclick="location.href='downloadData.php';" value="Download Data" /></td>        
            <td><input type="button" onclick="location.href='viewData.php';" value="View Data" /></td>
            <td><input type="button" onclick="location.href='deleteData.php';" value="Delete Data" /></td>         
        <tr>
    </table>
</head>
<body>
<!--Taken from https://www.jqueryscript.net/table/jQuery-Plugin-To-Convert-HTML-Table-To-CSV-tabletoCSV.html -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="./jquery.tabletoCSV.js" type="text/javascript" charset="utf-8"></script>

<h2>Choose Table:</h2>

<?php
//define variables
$nameErr = "";
$name = "";
$valid = 0;
$noResultsMessage = false;
$fileCaption = "Crickets";

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

  // Set File Caption 
  if ($name != "All")
  {
      if ($name == "TestInstance")
        $fileCaption = "Crickets-Table(TestInstance)-Data";
      else if ($name == "Cricket")
        $fileCaption = "Crickets-Table(Cricket)-Data";
      else if ($name == "Test")
        $fileCaption = "Crickets-Table(Test)-Data";
      else if ($name == "Project")
        $fileCaption = "Crickets-Table(Project)-Data";
      else if ($name == "Observer")
        $fileCaption = "Crickets-Table(Observer)-Data";
      else if ($name == "CricketTestInstance")
        $fileCaption = "Crickets-Table(CricketTestInstance)-Data";
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

<h2>Data From <?php echo $name;?> Table:</h2>

<button id="export" data-export="export">Export As CSV</button><br><br>

<table style="border: 1px solid black" id="export_table">
<?php
// Only query the database if the table name is valid
if($valid == 1)
{
	// Connection string
	$conn = oci_connect('aabeyer', 'Piggies3!', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');

	$query = "SELECT * FROM " . $name . " ORDER BY ID";
	$stid = oci_parse($conn,$query);
	oci_execute($stid,OCI_DEFAULT);

	// If there are no records, say so.
	if(oci_fetch_all($stid, $res) < 1)
	{
        $noResultsMessage = true;
	}

	// Execute the query a second time because I used the fetch_all function above
	oci_execute($stid,OCI_DEFAULT);

    //---Start of data table---
    $out = '';

    //add column headers to table
    $fieldIndex = 1;
    $out .= "<tr>";
    while ($row = oci_field_name($stid,$fieldIndex))
    {
        $out .= "<td>". oci_field_name($stid,$fieldIndex) ."</td>"; 
        $fieldIndex++;
    }
    $out .= "</tr>";

	//iterate through each row
	while ($row = oci_fetch_array($stid,OCI_RETURN_NULLS + OCI_ASSOC))
	{
        $out .= "<tr>";
		//iterate through each item in the row and echo it
		foreach ($row as $item)
		{
            //echo $item.' ';
            $out .= "<td>".$item."</td>"; 
        }
        $out .= "</tr>";
	}
	oci_free_statement($stid);
    oci_close($conn);

    echo ($out);  
}
?>
</table><br/>

<?php
if($valid == 1)
    if($noResultsMessage)
        echo "No results for '" . $name . "' table."
?>

<script>
    $(function(){
        $("#export").click(function(){
            $("#export_table").tableToCSV("<?php echo $fileCaption ?>");
        });
    });
</script>


</body>
</html>
