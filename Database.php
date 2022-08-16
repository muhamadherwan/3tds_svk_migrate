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
