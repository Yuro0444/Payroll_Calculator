<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll System</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        async function calculateSalary(event) {
            if (event.key === "Enter") {
                
                let name = document.getElementById("name").value;
                let hoursWorked = parseFloat(document.getElementById("hoursWorked").value);
                let hourlyRate = parseFloat(document.getElementById("hourlyRate").value);

                if (!isNaN(hoursWorked) && !isNaN(hourlyRate) && name !== "") {
                    let salary = hoursWorked * hourlyRate;
                    document.getElementById("salary").value = salary.toFixed(2);
                    
                    try {
                        let response = await fetch("process_salary.php", {
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ name: name, salary: salary })
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


        async function loadRecords() {
            try {
                let response = await fetch("fetch_records.php");
                let data = await response.text();
                document.getElementById("records").innerHTML = data;
            } catch (error) {
                console.error("Error loading records:", error);
            }
        }

        function editRecord(id, name, salary) {
            document.getElementById("name").value = name;
            document.getElementById("salary").value = salary;
            document.getElementById("editId").value = id;
            document.getElementById("saveButton").style.display = "block";
        }

        async function updateSalary() {
            let id = document.getElementById("editId").value;
            let name = document.getElementById("name").value;
            let salary = document.getElementById("salary").value;

            if (id && name && salary) {
                try {
                    let response = await fetch("update_record.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ id: id, name: name, salary: parseFloat(salary) })
                    });

                    let result = await response.text();
                    alert(result);
                    loadRecords();
                    document.getElementById("saveButton").style.display = "none";
                } catch (error) {
                    console.error("Error updating record:", error);
                }
            }
        }

        async function deleteRecord(id) {
            if (confirm("Are you sure you want to delete this record?")) {
                try {
                    let response = await fetch("delete_record.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "id=" + id
                    });
                    let result = await response.text();
                    alert(result);
                    loadRecords();
                } catch (error) {
                    console.error("Error deleting record:", error);
                }
            }
        }

        window.onload = loadRecords;
    </script>
</head>
<body>
    <div class="container">
        <h2>Payroll System</h2>
        <label for="name">Employee Name:</label>
        <input type="text" id="name" placeholder="Enter name">
        <label for="hoursWorked">Hours Worked:</label>
        <input type="number" id="hoursWorked" placeholder="Enter hours worked" onkeypress="calculateSalary(event)">
        <label for="hourlyRate">Hourly Rate:</label>
        <input type="number" id="hourlyRate" placeholder="Enter hourly rate" onkeypress="calculateSalary(event)">
        <label for="salary">Gross Salary:</label>
        <input type="number" id="salary" placeholder="Calculated gross salary" readonly>
        <div id="result"></div>
        <h3>Employee Records</h3>
        <div id="records"></div>
    </div>
</body>
</html>