<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "test";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Specify the path to the CSV file
$filePath = '../data/data.csv';

// Read the first line of the CSV file to detect the delimiter
$firstLine = fgets(fopen($filePath, 'r'));
$commaCount = substr_count($firstLine, ',');
$pipeCount = substr_count($firstLine, '|');

// Determine the delimiter based on the count of commas and pipes
if ($commaCount > $pipeCount) {
    $delimiter = ',';
} else {
    $delimiter = '|';
}

// Read data from the CSV file using the detected delimiter
$data = array_map(function ($line) use ($delimiter) {
    return str_getcsv($line, $delimiter);
}, file($filePath));

// Assuming the CSV file has headers and the columns are in the same order: firstname, lastname, email
$csvHeaders = array_shift($data);

// Check if there is data in the CSV file
if (!empty($data)) {
    foreach ($data as $rowData) {
        // Check for duplicate entry with the same email
        $checkDuplicateSql = "SELECT * FROM sttpesd WHERE ped_cdr = '{$rowData[26]}'";
        $result = $conn->query($checkDuplicateSql);

        if ($result->num_rows == 0) {
            // No duplicate entry, proceed with the insert
            $sql = "INSERT INTO sttpesd (
                `ped_pcode`, 
                `ped_fcode`, 
                `ped_ecode`, 
                `ped_date`, 
                `ped_sampleno`, 
                `ped_trkpltno`, 
                `ped_trailerno`, 
                `ped_grswt`, 
                `ped_tarewt`, 
                `ped_brxrdg`, 
                `ped_polrdg`, 
                `ped_spgr`, 
                `ped_trkid`, 
                `ped_grscane`, 
                `ped_netcane`, 
                `ped_tonbrx`, 
                `ped_tonpol`, 
                `ped_sugar`, 
                `ped_variety`, 
                `ped_mcode`, 
                `ped_notrailer`, 
                `ped_minimil`, 
                `ped_perpol`, 
                `ped_purit`, 
                `ped_pertras`, 
                `ped_dtmerge`, 
                `ped_cdr`, 
                `ped_pstc`, 
                `ped_user`, 
                `ped_batch`, 
                `ped_delete`, 
                `ped_wkno`, 
                `ped_pstc2`, 
                `ped_fldno`, 
                `ped_fiber`, 
                `ped_ffm`, 
                `ped_trs`, 
                `ped_sugar_trs`, 
                `ped_pstcnew`, 
                `ped_sugnew`, 
                `ped_pstcorig`, 
                `ped_sugorig`, 
                `ped_comp`,
                `end`
            ) VALUES (
                '{$rowData[0]}', '{$rowData[1]}', '{$rowData[2]}', '{$rowData[3]}', '{$rowData[4]}',
                '{$rowData[5]}', '{$rowData[6]}', '{$rowData[7]}', '{$rowData[8]}', '{$rowData[9]}',
                '{$rowData[10]}', '{$rowData[11]}', '{$rowData[12]}', '{$rowData[13]}', '{$rowData[14]}',
                '{$rowData[15]}', '{$rowData[16]}', '{$rowData[17]}', '{$rowData[18]}', '{$rowData[19]}',
                '{$rowData[20]}', '{$rowData[21]}', '{$rowData[22]}', '{$rowData[23]}', '{$rowData[24]}',
                '{$rowData[25]}', '{$rowData[26]}', '{$rowData[27]}', '{$rowData[28]}', '{$rowData[29]}',
                '{$rowData[30]}', '{$rowData[31]}', '{$rowData[32]}', '{$rowData[33]}', '{$rowData[34]}',
                '{$rowData[35]}', '{$rowData[36]}', '{$rowData[37]}', '{$rowData[38]}', '{$rowData[39]}',
                '{$rowData[40]}', '{$rowData[41]}', '{$rowData[42]}', '{$rowData[43]}'
            )";

            if ($conn->query($sql) === TRUE) {
                $response = array(
                    'status' => 'success',
                    'message' => "Data inserted into the database at " . date("Y-m-d H:i:s"),
                    'countdown' => 60, // Countdown value in seconds
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => "Error: " . $sql . "<br>" . $conn->error,
                    'countdown' => 60, // Countdown value in seconds
                );
            }
        } else {
            // Duplicate entry, notify and continue
            $response = array(
                'status' => 'duplicate',
                'message' => "Duplicate entry found for email: {$rowData[2]}",
                'countdown' => 60, // Countdown value in seconds
            );
        }
    }
} else {
    // Error reading data from the CSV file
    $response = array(
        'status' => 'error',
        'message' => "Error reading data from the CSV file.",
        'countdown' => 60, // Countdown value in seconds
    );
}

// Close the database connection
$conn->close();

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
