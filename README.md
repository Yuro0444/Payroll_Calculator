# Payroll_Calculator

A simple payroll calculation system built with PHP, MySQL, and JavaScript.

## Getting Started

1. Install XAMPP and start Apache & MySQL.
2. Open your browser and go to http://localhost/phpmyadmin/.
3. Click on the SQL tab and run the following script to create the database and tables:

'''
CREATE DATABASE IF NOT EXISTS payroll_db;
USE payroll_db;

-- Create tax_brackets table
CREATE TABLE IF NOT EXISTS tax_brackets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    min_income DECIMAL(15,2) NOT NULL,
    max_income DECIMAL(15,2) NOT NULL,
    base_tax DECIMAL(15,2) NOT NULL,
    rate DECIMAL(5,2) NOT NULL
);

-- Insert tax brackets data
INSERT INTO tax_brackets (min_income, max_income, base_tax, rate) VALUES
(-1.00, 20833.00, 0.00, 0.00),
(20833.00, 33333.00, 0.00, 0.15),
(33333.00, 66667.00, 1875.00, 0.20),
(66667.00, 166667.00, 8541.80, 0.25),
(166667.00, 666667.00, 33541.80, 0.30),
(666667.00, 99999999.99, 183541.80, 0.35);

-- Create employees table
CREATE TABLE IF NOT EXISTS employees (
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
);
'''

## Cloning the Repository

1. Create a folder named payroll_calculator inside your XAMPP folder (xampp/htdocs).
2. Open PowerShell or the terminal in VS Code.
3. Navigate to the xampp/htdocs folder:

'''
cd "path/to/payroll_calculator"
git clone https://github.com/Yuro0444/Payroll_Calculator.git
'''

4. (optional) or simply download the zip file and extract

## Run the Program

1. Enter http://localhost/payroll_calculator/index.php to your browser
