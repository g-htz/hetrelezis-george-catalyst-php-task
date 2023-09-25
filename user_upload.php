<?php
    $file = fopen('users.csv', 'r');
    $rows = [];
    while (($row = fgetcsv($file)) !== false) {
        $rows[] = $row;
    }
    fclose($file);

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "mysql";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    foreach ($rows as $row) {
        $name = $conn->real_escape_string($row[0]);
        $surname = $conn->real_escape_string($row[1]);
        $email = $conn->real_escape_string($row[2]);
        
        $sql = $conn->prepare("INSERT INTO users (name, surname, email) VALUES (?, ?, ?)");
        $sql->bind_param("sss", $name, $surname, $email);

        if ($sql->execute()) {
            echo "Record inserted successfully" . $row[0] . $row[1] . $row[2];
        } else {
            echo "Error: " . $sql->error;
        }

        $sql->close();
    }
    $conn->close();
?>