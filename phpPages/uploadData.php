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
    // Validate fields
    $pNameErr = "";
    $pName = "";
    $tNameErr = "";
    $tName = "";
    $fieldsValid = true;
    $fileErr = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate Project field 
        if (empty($_POST["pname"]))
        {
            $pNameErr = "Project is required";
            $fieldsValid = false;
        }
        else
            $pName = $_POST["pname"];

        // Validate Test field
        if (empty($_POST["tname"]))
        {
            $tNameErr = "Test is required";
            $fieldsValid = false;
        }
        else
            $tName = $_POST["tname"];
    }

    // Execute insert queries
    if($fieldsValid)
    {
        if ($_FILES["file"]["error"] > 0)
        {
            //echo "Error: " . $_FILES["file"]["error"] . "<br />";
            $fileErr = "File is INVALID";
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
            $testInstanceID = "";
            $cricketTestInstanceID = "";
            $project_ID = "";
            $test_ID = "";

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
                    if($testCounter < 5000)
                    {
                        // cut the whitespaces from the beginning and end
                        $value = trim($value);
                        $array = explode(",", $value);
                        array_walk($array, trim); // trim the values for the database

                        //WRITE QUERIES
                        echo "============================================ NEW RECORD ============================================<br />";

                        
                        // Cricket Table ===============================================================================================
                        echo "Cricket Table:<br />";

                        // Determine cricketID
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

                        // Determine observerID
                        $query = "SELECT * FROM Observer WHERE oName = '$array[9]'";

                        $stid = oci_parse($conn,$query);
                        oci_execute($stid,OCI_DEFAULT);
                        //$row = oci_fetch_array($stid,OCI_RETURN_NULLS + OCI_ASSOC);
                        oci_fetch($stid);
                        $observerID = oci_result($stid, 'ID');

                        //if the Observer is not already in the database
                        if($observerID == "")
                        {
                            $query = "SELECT MAX(ID) AS ID FROM Observer";
                            echo "$query<br />";
                            $stid = oci_parse($conn,$query);
                            oci_execute($stid,OCI_DEFAULT);
                            oci_fetch($stid);
                            $observerID = "" . ((int)(oci_result($stid, 'ID')) + 1);

                            // Add new Observer to Database
                            $query = "INSERT INTO Observer VALUES" .
                                    " ($observerID, '$array[9]')";
                            echo "$query<br />";
                            $stid = oci_parse($conn,$query);
                            oci_execute($stid,OCI_COMMIT_ON_SUCCESS);
                            echo "query executed<br />";
                        }
                        else
                            echo "Observer already exists<br />";                    
                        echo "Observer ID from database: $observerID<br />";
                        
                        // Project Table ===============================================================================================
                        echo "<br />Project Table:<br />";
                        
                        // Determine project_ID
                        $query = "SELECT * FROM Project WHERE projName = '$pName'";

                        $stid = oci_parse($conn,$query);
                        oci_execute($stid,OCI_DEFAULT);
                        //$row = oci_fetch_array($stid,OCI_RETURN_NULLS + OCI_ASSOC);
                        oci_fetch($stid);
                        $project_ID = oci_result($stid, 'ID');

                        //if the Project is not already in the database
                        if($project_ID == "")
                        {
                            $query = "SELECT MAX(ID) AS ID FROM Project";
                            echo "$query<br />";
                            $stid = oci_parse($conn,$query);
                            oci_execute($stid,OCI_DEFAULT);
                            oci_fetch($stid);
                            $project_ID = "" . ((int)(oci_result($stid, 'ID')) + 1);

                            // Add new Project to Database
                            $query = "INSERT INTO Project VALUES" .
                                    " ($project_ID, '$pName')";
                            echo "$query<br />";
                            $stid = oci_parse($conn,$query);
                            oci_execute($stid,OCI_COMMIT_ON_SUCCESS);
                            echo "query executed<br />";
                        }
                        else
                            echo "Project already exists<br />";                    
                        echo "Project ID from database: $project_ID<br />";

                        // Test Table ===============================================================================================
                        echo "<br />Test Table:<br />";
                        
                        // Determine test_ID
                        $query = "SELECT * FROM Test WHERE tName = '$tName'";

                        $stid = oci_parse($conn,$query);
                        oci_execute($stid,OCI_DEFAULT);
                        //$row = oci_fetch_array($stid,OCI_RETURN_NULLS + OCI_ASSOC);
                        oci_fetch($stid);
                        $test_ID = oci_result($stid, 'ID');

                        //if the Test is not already in the database
                        if($test_ID == "")
                        {
                            $query = "SELECT MAX(ID) AS ID FROM Test";
                            echo "$query<br />";
                            $stid = oci_parse($conn,$query);
                            oci_execute($stid,OCI_DEFAULT);
                            oci_fetch($stid);
                            $test_ID = "" . ((int)(oci_result($stid, 'ID')) + 1);

                            // Add new Project to Database
                            $query = "INSERT INTO Test VALUES" .
                                    " ($test_ID, '$tName', $project_ID)";
                            echo "$query<br />";
                            $stid = oci_parse($conn,$query);
                            oci_execute($stid,OCI_COMMIT_ON_SUCCESS);
                            echo "query executed<br />";
                        }
                        else
                            echo "Test already exists<br />";                    
                        echo "Test ID from database: $test_ID<br />";

                        // TestInstance Table ===============================================================================================
                        echo "<br />TestInstance Table:<br />";

                        // Determine TestInstance ID
                        $query = "SELECT MAX(ID) AS ID FROM TestInstance";
                        echo "$query<br />";
                        $stid = oci_parse($conn,$query);
                        oci_execute($stid,OCI_DEFAULT);
                        oci_fetch($stid);
                        $testInstanceID = "" . ((int)(oci_result($stid, 'ID')) + 1);

                        // Insert new TestInstance into Database
                        $query = "INSERT INTO TestInstance values ($testInstanceID, $test_ID, '$array[3]', '$array[4]', $array[5], $array[6], $array[7], TO_DATE('$array[8]', 'MM/DD/YYYY'), $observerID, '$array[10]')";
                        echo "$query<br />";
                        $stid = oci_parse($conn,$query);
                        oci_execute($stid,OCI_COMMIT_ON_SUCCESS);
                        echo "query executed<br />";

                        // CricketTestInstanceTable ===============================================================================================
                        echo "<br />CricketTestInstance Table:<br />";

                        // Determine CricketTestInstance ID
                        $query = "SELECT MAX(ID) AS ID FROM CricketTestInstance";
                        echo "$query<br />";
                        $stid = oci_parse($conn,$query);
                        oci_execute($stid,OCI_DEFAULT);
                        oci_fetch($stid);
                        $cricketTestInstanceID = "" . ((int)(oci_result($stid, 'ID')) + 1);

                        // Insert new CricketTestInstance into Database
                        $query = "INSERT INTO CricketTestInstance values ($cricketTestInstanceID, $cricketID, $testInstanceID)";
                        echo "$query<br />";
                        $stid = oci_parse($conn,$query);
                        oci_execute($stid,OCI_COMMIT_ON_SUCCESS);
                        echo "query executed<br />";

                        $testCounter++;
                    }
                }
            }
        } 
    } // end of if($fieldsValid)   
}
?>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" enctype="multipart/form-data">
<span class="error"><?php echo $fileErr;?></span><br>
<label for="file">Filename:</label>
<input type="file" name="file" id="file" /><br><br>
Project: <select name="pname">
    <option value="">Select Project</option>
    <option value="AD">AD</option>
</select><span class="error"><?php echo $pNameErr;?></span><br>
Test: <select name="tname">
    <option value="">Select Test</option>
    <option value="AP">AP</option>
    <option value="OF">OF</option>
</select><span class="error"><?php echo $tNameErr;?></span><br><br>
<input type="submit" name="submit" value="Submit" />
</form>

</body>
</html>
