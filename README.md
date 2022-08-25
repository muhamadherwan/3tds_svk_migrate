# 3tds svk migrate
### About

Script for fetch PMR or STAMP data from .txt file to .csv file for migration to svk system database.

### Built with.

* PHP 7.4
* MySql 8

### Installation.

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
  * PMR_1998_Grades.csv
  * PMR_1998_Marks.csv
* Inside the skv_migrate database table schools should have been populated with students data.

### References.

#### STAM Subjects:
* 1, 101, HIFZ AL-QURAN DAN TAJWID
* 2, 102, FIQH
* 3, 103, TAUHID DAN MANTIQ
* 4, 104, TAFSIR DAN ULUMUHU
* 5, 105, HADITH DAN MUSTOLAH
* 6, 106, NAHU DAN SARF
* 7, 107, INSYA' DAN MUTALAAH
* 8, 108, ADAB DAN NUSUS
* 9, 109, \ARUDH DAN QAFIYAH
* 10, 110, BALAGHAH

#### STAM Grades:
* 1, 1, MUMTAZ
* 2, 2, JAYYID JIDDAN
* 3, 3, JAYYID
* 4, 4, MAQBUL
* 5, 5, RASIB
* 6, T, TIDAK HADIR

#### PMR Subjects:
* 1, 02,MATA01
* 2,12,MATA02
* 3,21,MATA03
* 4,23,MATA04
* 5,31,MATA05
* 6,45,MATA06
* 7,50,MATA07
* 8,55,MATA08
* 9,76,MATA09

#### PMR Grades:
* 1,A
* 2,A1
* 3,A2
* 4,B
* 5,B1
* 6,B2
* 7,C
* 8,C1
* 9,C2
* 10,D
* 11,D1
* 12,D2

### Migration Logs:
* 25 AUG 2002: STAM 2020. School id start at 30001.
