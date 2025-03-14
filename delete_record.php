<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "payroll_db";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["id"])) {
    $id = intval($_POST["id"]);
    
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "Record deleted successfully!";
    } else {
        echo "Error deleting record!";
    }
    
    $stmt->close();
}

$conn->close();
?>

async function calculateSalary(event) {
            if (event.key === "Enter") {
                let salary = document.getElementById("salary").value;
                let name = document.getElementById("name").value;

                if (salary !== "" && name !== "") {
                    try {
                        let response = await fetch("process_salary.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ name: name, salary: parseFloat(salary) })
                        });

                        let result = await response.json();

                        if (response.ok) {
                            document.getElementById("result").innerHTML = `
                                <p><strong>Name:</strong> ${result.name}</p>
                                <p><strong>Gross Salary:</strong> ${result.salary.toFixed(2)}</p>
                                <p><strong>SSS Deduction:</strong> ${result.sss.toFixed(2)}</p>
                                <p><strong>PhilHealth Deduction:</strong> ${result.philhealth.toFixed(2)}</p>
                                <p><strong>Pag-IBIG Deduction:</strong> ${result.pagibig.toFixed(2)}</p>
                                <p><strong>Taxable Income:</strong> ${result.taxable_income.toFixed(2)}</p>
                                <p><strong>Tax Due:</strong> ${result.tax_due.toFixed(2)}</p>
                                <p><strong>Net Salary:</strong> ${result.net_income.toFixed(2)}</p>
                            `;
                            loadRecords();
                        } else {
                            document.getElementById("result").innerHTML = `<p style="color:red;">${result.error}</p>`;
                        }
                    } catch (error) {
                        console.error("Error:", error);
                    }
                }
            }
        }
