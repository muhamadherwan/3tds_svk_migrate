<?php

/**
 * @author Herwan <mdherwan@gmail.com>
 * @link -
 */

// use PDO;

class Database
{

    // props
    // public \PDO $pdo;
    public $pdo;
    public static Database $db;

    /**
     * @param string
     *
     * @return void
     */
    public function __construct() {

        // connect to database
        $this->pdo = new PDO('mysql:host=localhost;port=3306;dbname=skv_migrate', 'herwan', '1234');
        // if cant connect, throw error
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$db = $this;
        
    }

    /**
     * @param array school name, no pusat, school code
     *
     * @return boolean
     */
    public function storedSchool( array $school )
     {
        // stored in db.
        $statement = $this->pdo->prepare("INSERT INTO schools (name, no_pusat, code) VALUE (:name, :no_pusat, :code)");
        $statement->bindValue(':name', $school['sch_Name'] );
        $statement->bindValue(':no_pusat', $school['no_pusat'] );
        $statement->bindValue(':code', $school['code']) ;
        $statement->execute();
    }

    /**
     * @param array student id, angka giliran, name, no mykad, school id
     *
     * @return boolean
     */
    public function storedStudent( array $student )
     {
        // stored in db.
        $statement = $this->pdo->prepare("INSERT INTO students (Stu_ID, Stu_Idx, Stu_Name, Stu_Mykad, Sch_ID) 
                                            VALUE (:Stu_ID, :Stu_Idx, :Stu_Name, :Stu_Mykad, :Sch_ID)");
        $statement->bindValue(':Stu_ID', $student['Stu_ID'] );
        $statement->bindValue(':Stu_Idx', $student['Stu_Idx'] );
        $statement->bindValue(':Stu_Name', $student['Stu_Name'] );
        $statement->bindValue(':Stu_Mykad', $student['Stu_Mykad'] );
        $statement->bindValue(':Sch_ID', $student['Sch_ID'] );
        $statement->execute();
    }

    /**
     * @param int no pusat
     *
     * @return int id schools
     */
    public function getStudentId( $stu_idx )
    {
        $statement = $this->pdo->prepare('SELECT Stu_ID FROM students WHERE Stu_Idx = :Stu_Idx');
        $statement->bindValue(':Stu_Idx', $stu_idx);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int no pusat
     *
     * @return int id schools
     */
    public function getSchoolId( $no_pusat )
    {
        $statement = $this->pdo->prepare('SELECT id FROM schools WHERE no_pusat = :no_pusat');
        $statement->bindValue(':no_pusat', $no_pusat);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int grade
     *
     * @return int grade id
     */
    public function getGradeId( $grade )
    {
        $statement = $this->pdo->prepare('SELECT id FROM grades WHERE name = :grade');
        $statement->bindValue(':grade', $grade);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

}
