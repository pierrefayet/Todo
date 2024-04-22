# Todo

[![SymfonyInsight](https://insight.symfony.com/projects/28622bf2-1b20-47aa-b67f-f4fdac60d8c1/small.svg)]

## Description

Todo is an application for adding and listing many tasks. it's possible to make a task done or not.
For an administrator you can list all tasks for all users; you have crud at the user and task.
For a user, you can only create, view, modify and delete your tasks.

## Features

- Create, read, modify or delete a task
- Change the status of a task to completed or not completed
- Create, read, modify or delete a user if you are an admin
- Various unit and functional tests

## Technologies Used

- Symfony 7
- PHP 8.2
- Doctrine ORM

## Prerequisites

- PHP 8.3 or higher
- Composer
- MySQL or any other database management system compatible with Doctrine
- phpunit
- circle ci
- webpack encore
- boostrap
- sass

## Installation

```bash
# Clone 
git clone https://github.com/pierrefayet/Todo.git
cd Todo
composer install
composer require symfony/webpack-encore-bundle
npm install
npm install bootstrap --save-dev
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
symfony server:start
```

## Utilisation

After installation, open your browser and go to https://localhost:8000.

## Creating Admin User via Command Line

Todo offers a command line utility to easily create an admin user.
This can be particularly useful for setting up the application or creating an admin user for testing purposes.

### Command

The command to create an admin user is as follows:

```bash
php bin/console app:create-user email@example.com password
```

### Coverage

Link to the test dashboard coverage:
http://localhost:63342/Todo/public/test-coverage/dashboard.html?_ijt=vtmvbrjq8ulpm9rfncltae6ur4&_ij_reload=RELOAD_ON_SAVE

## Contribution

Contributions are what make the open source community a place of learning, inspiration and creativity. We encourage
contributions large and small.

### How to Contribute?

1. **Fork the Project**: Start by forking the repository, so you can work on your own copy of the project.
2. **Clone Your Fork**: Clone your fork to your local machine to start making changes.
3. **Create a Branch**: Create a branch for each set of changes you want to make. Use descriptive and specific branch
   names.
4. **Make Changes**: Make your changes on your branch. Ensure that you test your code and follow the coding conventions
   of the project.
5. **Pull Request**: Submit a pull request (PR) from your branch to the main branch of the original repository. Clearly
   describe the changes made and their necessity.

### Contribution Guidelines

- **Clean and Well-Commented Code**: Please ensure your code is clean and well-documented to facilitate review by
  others.
- **Tests**: Add tests for new features or bug fixes to help maintain the project's quality.
- **Documentation**: Update the documentation as necessary, especially if you introduce new features or significant
  changes.
- **Adhere to the Project's Design Principles and Coding Standards**: Familiarize yourself with the existing code style
  of the project and follow it.

### Need Help?

If you have questions or need help getting started, feel free to create an issue in the repository or contact me.

We look forward to seeing your contributions!

## Authors

Pierre Fayet
