<?php
// Test script for jobsList endpoint
require '/home/bwalia/webimpetus-src/ci4/app/Config/Database.php';

$db = \Config\Database::connect();

// Test the exact query from the model
$builder = $db->table('project_jobs');
$builder->select('project_jobs.*,
    projects.name as project_name,
    users.name as assigned_user_name,
    employees.first_name as assigned_employee_first_name,
    employees.surname as assigned_employee_surname');
$builder->join('projects', 'projects.uuid = project_jobs.uuid_project_id', 'left');
$builder->join('users', 'users.id = project_jobs.assigned_to_user_id', 'left');
$builder->join('employees', 'employees.id = project_jobs.assigned_to_employee_id', 'left');
$builder->orderBy('project_jobs.created_at', 'DESC');

$result = $builder->get()->getResult();

echo "Jobs found: " . count($result) . "\n";
echo "Data:\n";
print_r($result);
