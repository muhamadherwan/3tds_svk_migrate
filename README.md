# 3tds svk migrate version: 1.0
## About

Script for fetch PMR or STAMP data from .txt file to .csv file for migration to svk system database.

### Built with

* [![Php7[Php.net]][Php-url]
* [![Mysql[Mysq.com][Php7-url]

### Installation

* Clone svk_migrate source code to webroot.
* Create database skv_migrate and all its table in mysql. Use the table schema in docs/schema.sql
* Set the database connection value in Database.php line 26.

### How to create new migration files.
* Put the .txt source file to input directory.
* Set the data source file name at svk file line 23. exp: $input = INPUT_PATH . "PMR1998.txt"
* Open terminal and execute the script with the Exam name follow with underscore and exam year. exp: php svk migrate:PMR_1998
* If successful check the new generated Migration files in output directory. exp: svk_migrate/ouput/PMR_1998.
* Inside the folder should have 5 migration files. exp:
  * PMR_1998_gvs_schools.csv
  * PMR_1998_Students.csv
  * PMR_1998_Subjects.csv
  * PMR_1998_Grades.csv"
  * PMR_1998_Marks.csv
* Inside the skv_migrate database table schools should have been populated with students data. 