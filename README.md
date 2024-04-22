# Todo

[![SymfonyInsight](https://insight.symfony.com/projects/7c26401c-056c-450c-86c8-8e182a4edb48/small.svg)]

## Description



## Features


## Technologies Used

- Symfony 7
- PHP 8.2
- Doctrine ORM

## Prerequisites

- PHP 8.2 or higher
- Composer
- MySQL or any other database management system compatible with Doctrine

## Installation

```bash
# Clone 
git clone https://github.com/pierrefayet/Todo.git
cd Todo
composer install
php bin/console doctrine:database:create
# Create the BDD and import the schema
php bin/console doctrine:schema:update --force
# Starting the Symfony server
symfony server:start
```

## Utilisation

After installation, open your browser and go to https://localhost:8000.

## Creating Admin User via Command Line

Todo offers a command line utility to easily create an admin user. This can be particularly useful for setting up the application or creating an admin user for testing purposes.

### Command
The command to create an admin user is as follows:

```bash
php bin/console app:create-user email@example.com password
```

## Contribution

Contributions are what make the open source community a place of learning, inspiration and creativity. We encourage
contributions large and small.

## Authors
Pierre Fayet
