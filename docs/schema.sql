# Svk migrate v1.0 database schema.
# @author Herwan <mdherwan@gmail.com>
# Date: 22 Aug 2022

# Database svk_migrate.
DROP DATABASE IF EXISTS svk_migrate;
CREATE DATABASE IF NOT EXISTS svk_migrate;
USE svk_migrate;

# Table schools.
CREATE TABLE schools
(
   id INT NOT NULL,
   name VARCHAR(255) NOT NULL,
   no_pusat VARCHAR(20) NOT NULL,
   code VARCHAR(20) NOT NULL,
   PRIMARY KEY ( id )
);

# Table students
create table students
(
    Stu_ID INT NOT NULL,
    Stu_Idx VARCHAR(255) NOT NULL,
    Stu_Name VARCHAR(255) NOT NULL,
    Stu_Mykad VARCHAR(255) NOT NULL,
    Sch_ID INT NOT NULL,
    PRIMARY KEY ( Stu_ID )
);

# Table subjects for pmr.
CREATE TABLE subjects_pmr
(
   Sub_ID INT NOT NULL AUTO_INCREMENT,
   Sub_Code VARCHAR(255) NOT NULL,
   Sub_Name VARCHAR(255) NOT NULL,
   PRIMARY KEY ( Sub_ID )
);

# Table subject_pmr values.
INSERT INTO subjects_pmr (Sub_Code, Sub_Name) 
VALUE
('02','BAHASA MELAYU'),
('12','BAHASA INGGERIS'),
('21','SEJARAH'),
('23','GEOGRAFI'),
('31','BAHASA ARAB'),
('32','BAHASA CINA'),
('33','BAHASA TAMIL'),
('34','BAHASA PUNJABI'),
('37','BAHASA IBAN'),
('38','BAHASA KADAZANDUSUN'),
('45','PENDIDIKAN ISLAM'),
('50','MATHEMATICS'),
('55','SCIENCE'),
('60','PENDIDIKAN KESENIAN'),
('76','KHB KEMAHIRAN TEKNIKAL'),
('77','KHB EKONOMI RUMAH TANGGA'),
('78','KHB PERTANIAN'),
('79','KHB PERDAGANGAN DAN KEUSAHAWANAN');

# Table grades for pmr.
CREATE TABLE grades_pmr(
   Grade_ID INT NOT NULL AUTO_INCREMENT,
   Grade VARCHAR(255) NOT NULL,
   PRIMARY KEY ( Grade_ID )
);

# Table grades_pmr values.
INSERT INTO grades_pmr (Grade) VALUE
('A'),
('B'),
('C'),
('D'),
('E'),
('T');

# Table subjects for stam.
CREATE TABLE subjects_stam
(
    Sub_ID INT NOT NULL AUTO_INCREMENT,
    Sub_Code VARCHAR(255) NOT NULL,
    Sub_Name VARCHAR(255) NOT NULL,
    PRIMARY KEY ( Sub_ID )
);

# Table subjects_stam values.
INSERT INTO subjects_stam (Sub_Code, Sub_Name)
VALUE
('101','HIFZ AL-QURAN DAN TAJWID'),
('102', 'FIQH'),
('103', 'TAUHID DAN MANTIQ'),
('104', 'TAFSIR DAN ULUMUHU'),
('105', 'HADITH DAN MUSTOLAH'),
('106', 'NAHU DAN SARF'),
('107', 'INSYA\' DAN MUTALAAH'),
('108', 'ADAB DAN NUSUS'),
('109', '\'ARUDH DAN QAFIYAH'),
('110', 'BALAGHAH');

# Table grades_stam.
CREATE TABLE grades_stam
(
    Grade_ID INT NOT NULL AUTO_INCREMENT,
    Grade_Code VARCHAR(255) NOT NULL,
    Grade_Name VARCHAR(255) NOT NULL,
    PRIMARY KEY ( Grade_ID )
);

# Table grades_stam.
INSERT INTO grades_stam (Grade_Code, Grade_Name)
VALUE
('1', 'MUMTAZ'),
('2', 'JAYYID JIDDAN'),
('3', 'JAYYID'),
('4', 'MAQBUL'),
('5', 'RASIB'),
('T', 'TIDAK HADIR');