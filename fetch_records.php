<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "payroll_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM employees";
$result = $conn->query($sql);

echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Net Salary</th><th>Actions</th></tr>";
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['name']}</td>
            <td>₱" . number_format($row['net_salary'], 2) . "</td>
            <td>
                <button onclick='deleteRecord({$row['id']})'>Delete</button>
            </td>
          </tr>";
}
echo "</table>";

$conn->close();
?>
