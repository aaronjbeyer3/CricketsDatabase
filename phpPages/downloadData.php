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

    <h1>Crickets Database: Download Data</h1>
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

<h2>Choose Filter (Optional)</h2>

<?php
// Define variables
$name = "All";
$cValue = "";
$cValueErr = "";
$valid = 1;
$ResultsType = "All Data (Default):";
$fileCaption = "Crickets-ALL-Data";

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  $name = $_POST["tname"];

  if (empty($_POST["cName"]) && $_POST["tname"] != "All")
  {
    $cValueErr = "Value is required";
    $valid = 0;
  }   
  else
  {
    $cValue = $_POST["cName"];

    // Check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z0-9 ]*$/",$cValue))
    {
        $cValueErr = "Only letters and white space allowed";
        $valid = 0;
    }
  }

  // Set Table Label and File Caption
  if ($name != "All")
  {
    $ResultsType = "Filtered Data:";

    // File Caption 
    if($name == "t.tname")
        $fileCaption = "Crickets-FILTERED(Test)-Data";
    else if ($name == "c.ID")
        $fileCaption = "Crickets-FILTERED(CricketID)-Data";
    else if ($name == "o.oName")
        $fileCaption = "Crickets-FILTERED(Observer)-Data";
    else if ($name == "ti.arena")
        $fileCaption = "Crickets-FILTERED(Arena)-Data";
    else if ($name == "p.projName")
        $fileCaption = "Crickets-FILTERED(Project)-Data";
  }   
}
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
  Criteria: 
  <select name="tname">
    <option value="All">All Data</option>
    <option value="t.tname">Test Name</option>
    <option value="c.ID">Cricket ID</option>
    <option value="p.projName">Project Name</option>
    <option value="o.oName">Observer Name</option>
    <option value="ti.arena">Arena Number</option>
  </select> =
  <input type="text" name="cName">
  <span class="error"><?php echo $cValueErr;?></span><br><br>
  <input type="submit">
</form><br><br>

<h2><?php echo $ResultsType ?></h2>

<button id="export" data-export="export">Export As CSV</button><br><br>

<table style='border: 1px solid black' id = "export_table">
<?php
if($valid == 1)
{
    // Connection string
    $conn = oci_connect('aabeyer', 'Piggies3!', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');

    $criteria = "";
    if ($name == "c.ID" OR $name == "ti.arena")
        $criteria = "WHERE " . $name . " = " . $cValue;
    else if ($name != "All")
        $criteria = "WHERE " . $name . " = '" . $cValue . "'";

    $query = "SELECT c.name AS Id, p.projName AS Project, t.tName AS Test, c.mother AS Mom," .
            " c.father AS Dad, ti.recordingTime AS testTime, ti.mass, ti.rep," .
            " ti.arena, ti.temp, ti.recordingDate AS testDate, o.oName AS Observer, ti.status" .
            " FROM TestInstance ti" .
            " LEFT JOIN CricketTestInstance cti ON ti.ID = cti.testInstanceID" .
            " INNER JOIN Cricket c ON cti.cricketID = c.ID" .
            " LEFT JOIN Test t ON ti.testID = t.ID" .
            " INNER JOIN Project p ON t.projectID = p.ID" .
            " LEFT JOIN Observer o ON ti.observerID = o.ID " . 
            $criteria .
            " ORDER BY testDate, testTime";

    $stid = oci_parse($conn,$query);
    oci_execute($stid,OCI_DEFAULT);

    // If there are no records, say so.
    if(oci_fetch_all($stid, $res) < 1)
    {
        echo "<tr><td>". "No results for '" . $name ."'</td></tr>";
    }

    // Execute the query a second time because I used the fetch_all function above
    oci_execute($stid,OCI_DEFAULT);

    //---Start of data table---
    $out = '';

    // Add column headers to table
    $fieldIndex = 1;
    $out .= "<tr>";
    while ($row = oci_field_name($stid,$fieldIndex))
    {
        $out .= "<td>". oci_field_name($stid,$fieldIndex) ."</td>"; 
        $fieldIndex++;
    }
    $out .= "</tr>";

    // Iterate through each entry in results
    while ($row = oci_fetch_array($stid,OCI_RETURN_NULLS + OCI_ASSOC))
    {
        // Start next row in table
        $out .= "<tr>";

        // Fill each column in the row
        foreach ($row as $item)
        {
            $out .= "<td>".$item."</td>"; 
        }

        // End row
        $out .= "</tr>";
    }
    oci_free_statement($stid);
    oci_close($conn);

    //Print out table to page
    echo ($out);
    //---End of data table---
} 
?>
</table><br/>
<!-- USED TO DEBUG QUERY <p><?php echo $query ?></p> -->

<script>
    $(function(){
        $("#export").click(function(){
            $("#export_table").tableToCSV("<?php echo $fileCaption ?>");
        });
    });
</script>

</body>
</html>
