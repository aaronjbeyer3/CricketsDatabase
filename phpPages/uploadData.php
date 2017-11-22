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

    <h1>Crickets Database: Upload Data</h1>
    <table>
        <tr>
            <td><input type="button" onclick="location.href='index.html';" value="Home" /></td>
            <td><input type="button" onclick="location.href='viewData.php';" value="View Data" /></td>
            <td><input type="button" onclick="location.href='uploadData.php';" value="Upload Data" /></td>
            <td><input type="button" onclick="location.href='downloadData.php';" value="Download Data" /></td>        
        <tr>
    </table>
</head>
<body>
<!--Taken from https://www.jqueryscript.net/table/jQuery-Plugin-To-Convert-HTML-Table-To-CSV-tabletoCSV.html -->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.0.3/jquery.min.js" type="text/javascript" charset="utf-8"></script>
<script src="./jquery.tabletoCSV.js" type="text/javascript" charset="utf-8"></script>

<h2>Test</h2>

<?php
if(!empty($_POST))
{
    if ($_FILES["file"]["error"] > 0)
    {
        echo "Error: " . $_FILES["file"]["error"] . "<br />";
    }
    else
    {
        // Set connection string
        $conn = oci_connect('aabeyer', 'Piggies3!', '(DESCRIPTION=(ADDRESS_LIST=(ADDRESS=(PROTOCOL=TCP)(Host=db2.ndsu.edu)(Port=1521)))(CONNECT_DATA=(SID=cs)))');

        // Declare variables
        $f = file($_FILES["file"]["tmp_name"]);
        $firstLine = true;
        $testCounter = 0;

        // Variables for inserts
        $cricketID = "";
        $projectID = "";
        $testID = "";
        $observerID = "";

        //loop through each line
        foreach($f as $key => $value)
        { 
            if($firstLine)
            {
                // cut the whitespaces from the beginning and end
                $value = trim($value);
                $array = explode(",", $value);
                
                // trim the values for the database
                array_walk($array, trim);
                $query = "Fields that will be inserted into the databse: $array[2], $array[3], $array[4], $array[5], $array[6], $array[7], $array[8], $array[9], $array[10]";
                echo "$query<br /><br />";

                $firstLine = false;
            }
            else // Loop through each record
            {
                if($testCounter < 1)
                {
                    // cut the whitespaces from the beginning and end
                    $value = trim($value);
                    $array = explode(",", $value);
                    array_walk($array, trim); // trim the values for the database

                    //WRITE QUERIES
                    echo "============================================ NEW RECORD ============================================<br />";

                    
                    // Cricket Table ===============================================================================================
                    echo "Cricket Table:<br />";

                    // Determine CricketID
                    $query = "SELECT * FROM Cricket WHERE name = '$array[2]'";
                    $stid = oci_parse($conn,$query);

                    oci_execute($stid,OCI_DEFAULT);
                    //$row = oci_fetch_array($stid,OCI_RETURN_NULLS + OCI_ASSOC);
                    oci_fetch($stid);
                    $cricketID = oci_result($stid, 'ID');

                    //if the Cricket is not already in the database
                    if($cricketID == "")
                    {
                        $query = "SELECT MAX(ID) AS ID FROM Cricket";
                        echo "$query<br />";
                        $stid = oci_parse($conn,$query);
                        oci_execute($stid,OCI_DEFAULT);
                        oci_fetch($stid);
                        $cricketID = "" . ((int)(oci_result($stid, 'ID')) + 1);

                        // Add new Cricket to Database
                        $query = "INSERT INTO Cricket VALUES" .
                                " ($cricketID, '$array[2]'," .                          
                                " CAST(SUBSTR('$array[2]', INSTR('$array[2]', '_', 1, 2) + 1, INSTR('$array[2]', '_', 1, 3) - INSTR('$array[2]', '_', 1, 2) - 1) AS INTEGER)," .
                                " CAST(SUBSTR('$array[2]', INSTR('$array[2]', '_', 1, 3) + 1, INSTR('$array[2]', '_', 1, 4) - INSTR('$array[2]', '_', 1, 3) - 1) AS INTEGER))";
                        echo "$query<br />";
                        $stid = oci_parse($conn,$query);
                        oci_execute($stid,OCI_COMMIT_ON_SUCCESS);
                        echo "query executed<br />";
                    }
                    else
                        echo "Cricket already exists<br />";                    
                    echo "Cricket ID from database: $cricketID<br />";


                    // Observer Table ===============================================================================================
                    echo "<br />Observer Table:<br />";
                    $query = "INSERT INTO Observer values (GET-ID-FROM('$array[9]'), '$array[9]')";
                    echo "$query<br />";

                    // Project Table ===============================================================================================
                    echo "<br />Project Table:<br />";
                    $query = "INSERT INTO Project values (GET-ID-FROM('InputtedProject'), 'InputtedProject')";
                    echo "$query<br />";

                    // Test Table ===============================================================================================
                    echo "<br />Test Table:<br />";
                    $query = "INSERT INTO Test values (GET-ID-FROM('InputtedTest'), 'InputtedTest')";
                    echo "$query<br />";

                    // TestInstance Table ===============================================================================================
                    echo "<br />TestInstance Table:<br />";
                    $query = "INSERT INTO TestInstance values (GET-ID-FROM(MaxValueInTheTable + 1), TestIDValueFromAbove, '$array[3]', '$array[4]', '$array[5]', '$array[6]', '$array[7]', TO_DATE('$array[8]', 'MM/DD/YYYY'), ObserverIDValueFromAbove, '$array[10]')";
                    echo "$query<br />";

                    // CricketTestInstanceTable ===============================================================================================
                    echo "<br />CricketTestInstance Table:<br />";
                    $query = "INSERT INTO CricketTestInstance values (GET-ID-FROM(MaxValueInTheTable + 1), CricketIDFromAbove, TestInstanceIDFromAbove)";
                    echo "$query<br />";

                    $testCounter++;
                }
            }
        }
    }    
}
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
<label for="file">Filename:</label>
<input type="file" name="file" id="file" /> 
<input type="submit" name="submit" value="Submit" />
</form>

</body>
</html>
