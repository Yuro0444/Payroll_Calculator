<?php
$servername = "localhost";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$dbname = "payroll_db";

try {
    // Connect to MySQL
    $conn = new PDO("mysql:host=$servername", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if not exists
    $conn->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $conn->exec("USE $dbname");
    
    // Create tax_brackets table
    $conn->exec("CREATE TABLE IF NOT EXISTS tax_brackets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        min_income DECIMAL(15,2) NOT NULL,
        max_income DECIMAL(15,2) NOT NULL,
        base_tax DECIMAL(15,2) NOT NULL,
        rate DECIMAL(5,2) NOT NULL
    )");
    
    // Insert tax brackets data if table is empty
    $stmt = $conn->query("SELECT COUNT(*) FROM tax_brackets");
    if ($stmt->fetchColumn() == 0) {
        $conn->exec("INSERT INTO tax_brackets (min_income, max_income, base_tax, rate) VALUES
            (-1.00, 20833.00, 0.00, 0.00),
            (20833.00, 33333.00, 0.00, 0.15),
            (33333.00, 66667.00, 1875.00, 0.20),
            (66667.00, 166667.00, 8541.80, 0.25),
            (166667.00, 666667.00, 33541.80, 0.30),
            (666667.00, 99999999.99, 183541.80, 0.35)");
    }
    
    // Create employees table
    $conn->exec("CREATE TABLE IF NOT EXISTS employees (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        position_id INT NOT NULL,
        department_id INT NOT NULL,
        salary DECIMAL(15,2) NOT NULL,
        location_id INT NOT NULL,
        sss DECIMAL(15,2) NOT NULL,
        philhealth DECIMAL(15,2) NOT NULL,
        pagibig DECIMAL(15,2) NOT NULL,
        taxable_income DECIMAL(15,2) NOT NULL,
        tax_due DECIMAL(15,2) NOT NULL,
        net_salary DECIMAL(15,2) NOT NULL
    )");
    
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>