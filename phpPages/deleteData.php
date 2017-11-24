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

    <h1>Crickets Database: Delete Data</h1>
    <table>
        <tr>
            <td><input type="button" onclick="location.href='index.html';" value="Home" /></td>
            <td><input type="button" onclick="location.href='viewData.php';" value="View Data" /></td>
            <td><input type="button" onclick="location.href='deleteData.php';" value="Delete Data" /></td> 
            <td><input type="button" onclick="location.href='uploadData.php';" value="Upload Data" /></td>
            <td><input type="button" onclick="location.href='downloadData.php';" value="Download Data" /></td>       
        <tr>
    </table>
</head>
<body>

<h2>Select Download Option</h2>

<?php
// Define variables
$name = "";
$nameErr = "";
$valid = false;

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
  if(empty($_POST["tname"]))
  {
      $nameErr = "No option selected";
  }
  else
  {
      $name = $_POST["tname"];
      $valid = true;
  }
}
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
  Delete Option: 
  <select name="tname">
    <option value="">Select Delete Option</option>
    <option value="test">Delete Test Data Only</option>
    <option value="all">Delete All Data (including table data)</option>
  </select>
  <span class="error"><?php echo $nameErr;?></span><br><br>
  <input type="submit">
</form><br><br>

<?php
if($valid)
{
    // Connection string
    $conn = oci_connect('aabeyer', 'Piggies3!', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');

    if ($name == "test")
    {
        //Only Delete from TestInstance table
        $query = "DELETE FROM TestInstance";

        $stid = oci_parse($conn,$query);
        oci_execute($stid,OCI_COMMIT_ON_SUCCESS);
        echo "Test Data Deleted";
    }   
    else //$name == "all"
    {
        //Delete all data in the databse
        $query = "DELETE FROM Project"; 
        $stid = oci_parse($conn,$query);
        oci_execute($stid,OCI_COMMIT_ON_SUCCESS);

        $query = "DELETE FROM Observer"; 
        $stid = oci_parse($conn,$query);
        oci_execute($stid,OCI_COMMIT_ON_SUCCESS);

        $query = "DELETE FROM Cricket"; 
        $stid = oci_parse($conn,$query);
        oci_execute($stid,OCI_COMMIT_ON_SUCCESS);

        echo "All Data Deleted";
    }
    
    oci_free_statement($stid);
    oci_close($conn);
} 
?>

</body>
</html>
