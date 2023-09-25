<?php
    $options = getopt(null, [
        'file:',        
        'create_table', 
        'dry_run',      
        'u:',           
        'p:',           
        'h',            
        'help'          
    ]);
    
    if (isset($options['help'])) {
        echo "Help Menu: php script.php \n 
        [--file <csv_file>]: Allows you to run the script with your .csv file. \n 
        [--create_table]: Lets you generate a new table. \n 
        [--dry_run]: Allows you to run the script without inserting into the DB. Database will not update but all other functions will execute. \n 
        [-u <username>]: Allows you to enter your username. \n 
        [-p <password>]: Allows you to enter your password. \n 
        [-h <host>]: Allows you to enter your respective host input. \n 
        [--help]: View Help menu again. \n";
        exit;
    }
    
    $file = $options['file'] ?? null;
    $create_table = isset($options['create_table']);
    $dry_run = isset($options['dry_run']);
    $username = $options['u'] ?? 'root';
    $password = $options['p'] ?? '';
    $host = $options['h'] ?? 'localhost';
    
    $conn = new mysqli($host, $username, $password);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    if ($create_table) {
        $sql = "CREATE DATABASE IF NOT EXISTS mysql";
        $conn->query($sql);
        $sql = "USE mysql";
        $conn->query($sql);
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            surname VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE
        )";
        $conn->query($sql);
        $conn->close();
        echo "Table 'users' created successfully.\n";
        exit;
    }
    
    if (!$file) {
        echo "Error: You must provide a CSV file using the --file option.\n";
        exit;
    }
    
    $file = fopen('users.csv', 'r');
    $rows = [];
    while (($row = fgetcsv($file)) !== false) {
        $rows[] = $row;
    }
    fclose($file);

    $host = "localhost";
    $user = "root";
    $password = "";
    $dbname = "mysql";

    $conn = new mysqli($host, $user, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    foreach ($rows as $row) {
        $name = $conn->real_escape_string(ucfirst(strtolower($row[0])));
        $surname = $conn->real_escape_string(ucfirst(strtolower($row[1])));
        $email = $conn->real_escape_string(strtolower($row[2]));
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Error: User for $email's entry was invalid \n";
            continue;
        }

        $sql = $conn->prepare("INSERT INTO users (name, surname, email) VALUES (?, ?, ?)");
        $sql->bind_param("sss", $name, $surname, $email);

        if ($sql->execute()) {
            echo "Record inserted successfully" . $name . $surname . $email."\n";
        } else {
            echo "Error: " . $sql->error;
        }

        $sql->close();
    }
    $conn->close();
?>