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
    // public static Database $db;

    /**
     * @param string
     *
     * @return void
     */
    public function __construct() {

        // connect to database
         $this->pdo = new PDO('mysql:host=localhost;port=3306;dbname=svk_migrate', 'herwan', '1234');
//        $this->pdo = new PDO('mysql:host=localhost;port=3306;dbname=svk_migrate_staging', 'herwan', '1234');
        // if cant connect, throw error
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // self::$db = $this;
        
    }

    /**
     * @param array school name, no pusat, school code
     *
     * @return boolean
     */
    public function storedSchool( array $school )
     {
        // stored in db.
        $statement = $this->pdo->prepare("INSERT INTO schools (id, name, no_pusat, code) VALUE (:id, :name, :no_pusat, :code)");
        $statement->bindValue(':id', $school['sch_ID'] );
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
    public function getGradePmrId( $grade )
    {
        $statement = $this->pdo->prepare('SELECT Grade_ID FROM grades_pmr WHERE Grade = :grade');
        $statement->bindValue(':grade', $grade);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int grade
     *
     * @return int grade id
     */
    public function getGradeStamId( $grade )
    {
        $statement = $this->pdo->prepare('SELECT Grade_ID FROM grades_stam WHERE Grade_Code = :grade');
        $statement->bindValue(':grade', $grade);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param string file title
     *
     * @return string result message
     */
    public function createDb( string $newDir )
    {

        $file = 'db.sql';
        
        $mysqlnd = function_exists('mysqli_fetch_all');
	
	    if ($mysqlnd && version_compare(PHP_VERSION, '5.3.0') >= 0) 
	    {
            $statement = file_get_contents($file);
            $statement->execute();

            return TRUE;
		
	    }
					
	    return FALSE;

        // $dsn = "mysql:dbname=$database;host=$db_hostname";
            // $db = new PDO($dsn, $db_username, $db_password);
            // $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, 0);
            // $sql = file_get_contents($file);
            // $db->exec($sql);

        // check if database is exist.
        // if exist, drop the db and its tables, then create new db with all the tables;
        // else create new db with all the tables.

        /*
          $dbh->exec("CREATE DATABASE `$db`;
                CREATE USER '$user'@'localhost' IDENTIFIED BY '$pass';
                GRANT ALL ON `$db`.* TO '$user'@'localhost';
                FLUSH PRIVILEGES;")
        or die(print_r($dbh->errorInfo(), true));

        */

        // try {

        //     $statement = $this->pdo->prepare('CREATE DATABASE IF NOT EXISTS :dbName');
        //     $statement->bindValue(':dbName', $dbName);
        //     $statement->execute();

            
        //     $sql = "CREATE DATABASE myDBPDO";
        //     // use exec() because no results are returned
        //     $conn->exec($sql);
        //     echo "Database created successfully<br>";
        //   } catch(PDOException $e) {
        //     echo $sql . "<br>" . $e->getMessage();
        //   }



        // $result = "-- Creating Migration Database: {$newDir}". PHP_EOL;

        // return $result;

    }
}
