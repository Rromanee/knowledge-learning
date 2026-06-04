{
    "phases": {
      "build": {
        "cmds": [
          "install-php-extensions pdo_pgsql",
          "composer install --no-dev --optimize-autoloader --no-scripts"
        ]
      }
    }
  }