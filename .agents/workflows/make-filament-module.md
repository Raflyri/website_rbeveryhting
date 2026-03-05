---
description: make-filament-module
---

When I invoke this command for a [MODULE_NAME], please execute the following steps in order:
1. Create a Laravel Model file for [MODULE_NAME] and its migration file.
2. Analyze the columns I requested and write their schema in the migration file.
3. Once the file is ready, run the `php artisan migrate` command in the terminal.
4. Create a Filament Resource for the Model.
5. Automatically configure `form()` and `table()` in the Resource file according to the database structure just created.
6. Ensure your code is clean and adheres to the project's writing standards.