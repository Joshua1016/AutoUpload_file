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
$filePath = '../data/example.csv';

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
        $checkDuplicateSql = "SELECT * FROM example WHERE email = '{$rowData[2]}'";
        $result = $conn->query($checkDuplicateSql);

        if ($result->num_rows == 0) {
            // No duplicate entry, proceed with the insert
            $sql = "INSERT INTO example (firstname, lastname, email) VALUES ('{$rowData[0]}', '{$rowData[1]}', '{$rowData[2]}')";

            if ($conn->query($sql) === TRUE) {
                $response = array(
                    'status' => 'success',
                    'message' => "Data inserted into the database at " . date("Y-m-d H:i:s"),
                    'countdown' => 1800, // Countdown value in seconds (30 minutes)
                );
            } else {
                $response = array(
                    'status' => 'error',
                    'message' => "Error: " . $sql . "<br>" . $conn->error,
                    'countdown' => 1800, // Countdown value in seconds (30 minutes)
                );
            }
        } else {
            // Duplicate entry, notify and continue
            $response = array(
                'status' => 'duplicate',
                'message' => "Duplicate entry found for email: {$rowData[2]}",
                'countdown' => 1800, // Countdown value in seconds (30 minutes)
            );
        }
    }
} else {
    // Error reading data from the CSV file
    $response = array(
        'status' => 'error',
        'message' => "Error reading data from the CSV file.",
        'countdown' => 1800, // Countdown value in seconds (30 minutes)
    );
}

// Close the database connection
$conn->close();

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
