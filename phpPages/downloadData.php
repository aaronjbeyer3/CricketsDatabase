<!DOCTYPE html>
<html>
<head>
<style>
.error {color: #FF0000;}
</style>
</head>
<body>
<!--Taken from https://www.jqueryscript.net/table/jQuery-Plugin-To-Convert-HTML-Table-To-CSV-tabletoCSV.html -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="./jquery.tabletoCSV.js" type="text/javascript" charset="utf-8"></script>
<script>
    $(function(){
        $("#export").click(function(){
            $("#export_table").tableToCSV();
        });
    });
</script>

<h1>Crickets Database: Download Data</h1>
<p>(currently all data in the database joined together)</p><br>

<?php
//define variables
$nameErr = "";
$name = "";
$cValue = "";
$cValueErr = "";
$valid = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["tname"]))
    $nameErr = "Criteria is required";
  else
  {
    $name = $_POST["tname"];
    $valid++;
  }
  if (empty($_POST["cName"]))
    $cValueErr = "Value is required";
  else
  {
    $cValue = $_POST["cName"];

    // check if name only contains letters and whitespace
    if (!preg_match("/^[a-zA-Z0-9 ]*$/",$cValue)) {
        $cValueErr = "Only letters and white space allowed";
    }
    else
        $valid++;
  }
}
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
  Criteria: 
  <select name="tname">
    <option value="">Select Table</option>
    <option value="t.tname">Test Name</option>
    <option value="c.ID">Cricket ID</option>
    <option value="o.oName">Observer</option>
    <option value="ti.arena">Arena</option>
  </select>
  <span class="error"><?php echo $nameErr;?></span> = 
  <input type="text" name="cName">
  <span class="error"><?php echo $cValueErr;?></span><br><br>
  <input type="submit">
</form><br><br>

<h1>Results:</h1>

<table style='border: 1px solid black' id = "export_table">
<?php
if($valid == 2)
{
    // Connection string
    $conn = oci_connect('aabeyer', 'Piggies3!', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');

    $criteria = "";
    if($name == "c.ID" OR $name == "ti.arena")
        $criteria = "WHERE " . $name . " = " . $cValue;
    else
        $criteria = "WHERE " . $name . " = '" . $cValue . "'";

    $query = "SELECT c.name AS Id, t.tName AS Test, c.mother AS Mom, " .
    "c.father AS Dad, ti.recordingTime AS testTime, ti.mass, ti.rep, " .
    "ti.arena, ti.temp, ti.recordingDate AS testDate, o.oName AS Observer, ti.status " .
    "FROM TestInstance ti " .
    "LEFT JOIN CricketTestInstance cti ON ti.ID = cti.testInstanceID " .
    "INNER JOIN Cricket c ON cti.cricketID = c.ID " .
    "LEFT JOIN Test t ON ti.testID = t.ID " .
    "INNER JOIN Project p ON t.projectID = p.ID " .
    "LEFT JOIN Observer o ON ti.observerID = o.ID " . $criteria;
    $stid = oci_parse($conn,$query);
    oci_execute($stid,OCI_DEFAULT);

    // If there are no records, say so.
    if(oci_fetch_all($stid, $res) < 1)
    {
        echo "<tr style='border: 1px solid black'><td style='border: 1px solid black; padding: 5px'>". "No results for '" . $name ."'</td></tr>";
    }

    // Execute the query a second time because I used the fetch_all function above
    oci_execute($stid,OCI_DEFAULT);

    //---Start of data table---
    $out = '';

    //add column headers to table
    $fieldIndex = 1;
    $out .= "<tr style='border: 1px solid black'>";
    while ($row = oci_field_name($stid,$fieldIndex))
    {
        $out .= "<td style='border: 1px solid black; padding: 5px'>". oci_field_name($stid,$fieldIndex) ."</td>"; 
        $fieldIndex++;
    }
    $out .= "</tr>";

    //iterate through each row
    while ($row = oci_fetch_array($stid,OCI_RETURN_NULLS + OCI_ASSOC))
    {
        $out .= "<tr style='border: 1px solid black'>";
        //iterate through each item in the row and echo it
        foreach ($row as $item)
        {
            //echo $item.' ';
            $out .= "<td style='border: 1px solid black; padding: 5px'>".$item."</td>"; 
        }
        $out .= "</tr>";
    }
    oci_free_statement($stid);
    oci_close($conn);

    echo ($out); 
} 
?>
</table><br/>

<button id="export" data-export="export">Export As CSV</button>

</body>
</html>
