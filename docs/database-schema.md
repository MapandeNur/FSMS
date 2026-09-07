# FSMS Database Design

## Project
Field Student Management System (FSMS)

## Database
MySQL 8.x



# Tables

## 1. users

Stores users who can access the system.

| Column | Type | Constraint |
|---|---|---|
| id | BIGINT | Primary Key |
| name | VARCHAR(255) | Required |
| email | VARCHAR(255) | Unique |
| phone | VARCHAR(20) | Unique |
| password | VARCHAR(255) | Required |
| is_active | BOOLEAN | Default true |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Soft Delete |



## 2. roles

Stores system roles.

| Column | Type | Constraint |
|---|---|---|
| id | BIGINT | Primary Key |
| name | VARCHAR(100) | Unique |
| description | TEXT | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

Roles:

- admin
- supervisor
- hr_officer
- student


## 3. role_user

Pivot table connecting users and roles.

| Column | Type | Constraint |
|---|---|---|
| id | BIGINT | Primary Key |
| user_id | BIGINT | Foreign Key |
| role_id | BIGINT | Foreign Key |
| start_date | DATE | Required |
| end_date | DATE | Nullable |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

The start_date and end_date columns track the period during which a role or task is assigned.



## 4. students

Stores field student information.

| Column | Type | Constraint |
|---|---|---|
| id | BIGINT | Primary Key |
| registration_number | VARCHAR(50) | Unique |
| first_name | VARCHAR(100) | Required |
| middle_name | VARCHAR(100) | Nullable |
| last_name | VARCHAR(100) | Required |
| gender | VARCHAR(20) | Required |
| date_of_birth | DATE | Required |
| email | VARCHAR(255) | Unique |
| phone | VARCHAR(20) | Unique |
| institution_name | VARCHAR(255) | Required |
| programme_of_study | VARCHAR(255) | Required |
| year_of_study | INTEGER | Required |
| start_date | DATE | Required |
| end_date | DATE | Required |
| status | VARCHAR(50) | Default active |
| created_by | BIGINT | Foreign Key to users |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |
| deleted_at | TIMESTAMP | Soft Delete |



# Relationships

- One User can have many Roles through role_user.
- One Role can belong to many Users through role_user.
- One User can register many Students.
- Each Student is registered by one User.