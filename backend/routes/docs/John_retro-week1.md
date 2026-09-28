# Week One Retrospective

## What Went Well

The team successfully established the foundation of the Field Student Management System. The database structure, Laravel backend, authentication, user management, role assignment and student registration were implemented. The backend APIs were tested using Postman, and the integration between the Laravel backend and MySQL database was successfully verified.

## What Went Badly

During development, we experienced several database migration and authorization issues. Some migrations were executed in the wrong order, especially the relationship between the roles and role_user tables. We also encountered missing database columns and role configuration problems during frontend and backend integration. These issues required additional debugging before the system could work correctly.

## Process Change for Week Two

From Week Two, the team will follow a more structured development process. Database changes will be planned before implementation, migrations will be checked before running the application, and every new backend endpoint will be accompanied by automated feature tests. We will also use separate Git branches and pull requests for individual features to reduce conflicts and make troubleshooting easier.