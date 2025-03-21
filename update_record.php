<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "payroll_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getTaxRate($conn, $taxableIncome) {
    $rate = 0;
    $base_tax = 0;
    $min_income = 0;
    $max_income = 0;
    $stmt = $conn->prepare("SELECT rate, base_tax, min_income FROM tax_brackets WHERE ? BETWEEN min_income AND max_income");
    $stmt->bind_param("d", $taxableIncome);
    $stmt->execute();
    $stmt->bind_result($rate, $base_tax, $min_income);
    $stmt->fetch();
    $stmt->close();
    return [$rate, $base_tax, $min_income];
}


// Read JSON data from request
$data = json_decode(file_get_contents("php://input"), true);

$id = intval($data['id']);
$name = $conn->real_escape_string($data['name']);
$hours_worked = floatval($data['hours_worked']);
$hourly_rate = floatval($data['hourly_rate']);

$rate = 0;
$base_tax = 0;
$min_income = 0;
// Calculate new salary
$gross_salary = $hours_worked * $hourly_rate;

$sss = ($gross_salary < 4250) ? 180 : (($gross_salary <= 29750) ? 180 + ceil(($gross_salary - 4250) / 500.0) * 22.50 : 1350);
$philhealth = max(250, min(2500, ($gross_salary * 0.05) / 2));
$pagibig = ($gross_salary > 10000) ? 200 : (($gross_salary <= 1500) ? $gross_salary * 0.01 : $gross_salary * 0.02);
$taxable_income = $gross_salary - ($sss + $philhealth + $pagibig);

// Fetch tax rate dynamically (assumed from database query)
list($rate, $base_tax, $min_income) = getTaxRate($conn, $taxable_income);

$tax_due = ($taxable_income > $min_income) ? ($rate * ($taxable_income - $min_income)) + $base_tax : 0;
$net_salary = $taxable_income - $tax_due;


// Update the record
$sql = "UPDATE employees SET 
        name = '$name', 
        net_salary = '$net_salary'
        WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully.";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>