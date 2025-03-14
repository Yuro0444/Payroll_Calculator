<?php
// Set response headers to allow API calls from frontend
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); // Allow all origins (for testing)
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "payroll_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed"]));
}

// Handle API request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);
    
    // Validate input
    if (!isset($data["salary"], $data["name"])) {
        echo json_encode(["error" => "Invalid request"]);
        exit;
    }

    $salary = floatval($data["salary"]);
    $name = $conn->real_escape_string($data["name"]);

    // SSS computation
    function calculateSSS($salary) {
        return ($salary < 4250) ? 180 : (($salary <= 29750) ? 180 + ceil(($salary - 4250) / 500.0) * 22.50 : 1350);
    }

    // PhilHealth computation
    function calculatePhilHealth($salary) {
        return max(250, min(2500, ($salary * 0.05) / 2));
    }

    // Pag-IBIG computation
    function calculatePagIbig($salary) {
        return $salary > 10000 ? 200 : ($salary <= 1500 ? $salary * 0.01 : $salary * 0.02);
    }

    // Retrieve tax rate from database
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

    // Calculate deductions
    $sss = calculateSSS($salary);
    $philHealth = calculatePhilHealth($salary);
    $pagIbig = calculatePagIbig($salary);
    $taxableIncome = $salary - ($sss + $philHealth + $pagIbig);

    // Get tax rate dynamically
    list($rate, $base_tax, $min_income) = getTaxRate($conn, $taxableIncome);
    $taxDue = $rate * ($taxableIncome - $min_income) + $base_tax;
    
    $netIncome = $salary - ($sss + $philHealth + $pagIbig + $taxDue);

    // Store data in database
    $stmt = $conn->prepare("INSERT INTO employees (name, salary, sss, philhealth, pagibig, taxable_income, tax_due, net_salary) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sddddddd", $name, $salary, $sss, $philHealth, $pagIbig, $taxableIncome, $taxDue, $netIncome);
    $stmt->execute();
    $stmt->close();

    // Return response in JSON format
    echo json_encode([
        "name" => $name,
        "salary" => $salary,
        "sss" => $sss,
        "philhealth" => $philHealth,
        "pagibig" => $pagIbig,
        "taxable_income" => $taxableIncome,
        "tax_due" => $taxDue,
        "net_income" => $netIncome
    ]);
}

// Close database connection
$conn->close();
?>
