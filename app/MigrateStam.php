<?php

/**
 * @author Herwan <mdherwan@gmail.com>
 * @link -
 */

declare( strict_types = 1 );

class MigrateStam
{
    // props

    /**
     * @param string
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param string $input, $fullDir, $newDir 
     *
     * @return string result
     */
    public function create( $input, $fullDir, $newDir, $db )
    {
        $result = '';
        $result .= $this->migrateStamGvsSchools( $input, $fullDir, $newDir, $db );
        $result .= $this->migrateStamStudents( $input, $fullDir, $newDir, $db );
        $result .= $this->migrateStamSubjects( $input, $fullDir, $newDir );
        $result .= $this->migrateStamGrades( $input, $fullDir, $newDir );
        $result .= $this->migrateStamMarks( $input, $fullDir, $newDir, $db );
        return $result;
    }

    /**
     * @param string $input, $fullDir, $newDir 
     *
     * @return string result
     */
    public function migrateStamGvsSchools( $input, $fullDir, $newDir, $db )
    {
        $result = '';
        $rows = [];

        # read .txt files #
        if ( file_exists( $input ) ) {
            if (( $txtfile = fopen($input, 'r') )  !== false )
            {
                $id = 1;
                while ( ($data = fgetcsv($txtfile, 1000, ",")) !== false ) 
                {
                    // convert to string
                    $str = implode(" ", $data);

                    // get the school code in the string:
                    $schoolCode = trim(substr($str,195,10));
                    // var_dump($schoolCode);exit;

                    // check if current row have school code.
                    // if none, continue loop, else get the required data and save in new array set.
                    if ( strlen( $schoolCode ) !== 10 )
                    {
                        continue;
                    } else {
                        $data2 = [];
                        $data2['sch_ID'] = $id++;
                        $data2['sch_Name'] = trim(substr($str,12,60));
                        $data2['sch_PhoneNo'] = trim(substr($str,178,16));
                        $data2['sch_Email'] = '';
                        $data2['sch_Address'] = trim(substr($str,72,91));
                        $data2['sch_Code'] = $schoolCode;
                        $data2['sch_Year'] = '';
                        $data2['sch_Status'] = '';

                        $rows[] = $data2;

                        // save data for db
                        $data3 = [];
                        $data3['sch_Name'] = $data2['sch_Name'];
                        $data3['no_pusat'] = trim(substr($str,3,6));
                        $data3['code'] = $schoolCode;
                        $db->storedSchool( $data3 );
                    }           
                }
            }
        } else {
            $result .= "-- Fail to create {$newDir} file...". PHP_EOL; exit;
        }
        fclose($txtfile);

        # write gvs_schools.csv #
        $csvName = $newDir."_gvs_schools.csv";
        if ( $open = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w') !== false )
        {
            $csvfile = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w');

            // write the columns
            $columns = ["sch_ID", "sch_Name", "sch_PhoneNo", "sch_Email", "sch_Address", "sch_Code", "sch_Year", "sch_Status"];
            fputcsv($csvfile, $columns);

            // write the rows
            foreach ($rows as $row) 
            {
                fputcsv($csvfile, $row);
            }
            fclose($csvfile);

            $result .= "-- Creating Migration File: {$csvName}". PHP_EOL;
            $result .= "-- Created Migration File: {$csvName}". PHP_EOL;
        } else {
            $result .= "-- Creating Migration File: {$csvName}". PHP_EOL;
            $result .= "-- Fail Create Migration File: {$csvName}. Try again later.". PHP_EOL;
        }

        return $result;
    }

    /**
     * @param string $input, $fullDir, $newDir 
     *
     * @return string result
     */
    public function migrateStamStudents( $input, $fullDir, $newDir, $db )
    {
        $result = '';
        $rows = [];

        # read .txt files #
        if ( file_exists( $input ) ) {
            if (( $txtfile = fopen($input, 'r') )  !== false )
            {
                $id = 1;
                while ( ($data = fgetcsv($txtfile, 1000, ",")) !== false ) 
                {
                    // convert to string
                    $str = implode(" ", $data);

                    // get the school code in the string:
//                    $schoolCode = trim(substr($str,196,10));

                    $schoolCode = trim(substr($str,195,10));

                    // check if current row have school code.
                    // if none, continue loop, else get the required data and save in new array set.
                    if ( strlen( $schoolCode ) !== 10 )
                    {
                        // get school id from db based on student no pusat
                        $sch_id = $db->getSchoolId( trim(substr($str,3,6)) );

                        $data2 = [];
                        $data2['Stu_ID'] = $id++;
                        $data2['Stu_Idx'] = trim( substr( $str,0,12 ));
                        $data2['Stu_Name'] = trim( substr( $str,12,40 ));
//                        $data2['Stu_Mykad'] = (int)trim( substr( $str,92,12 ));
                        $data2['Stu_Mykad'] = trim( substr( $str,92,12 ));

                        $data2['Sch_ID'] = (int)$sch_id['id'];
                        $rows[] = $data2;

                        // stored array to db
                        $db->storedStudent( $data2 );
                    } else {
                        continue;
                    }           
                }
            }    
        } else {
            $result .= "-- Fail to create {$newDir} file...". PHP_EOL; exit;
        }
        fclose($txtfile);
        
        # write Students.csv #
        $csvName = $newDir."_Students.csv";
        if ( $open = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w') !== false )
        {
            $csvfile = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w');

            // write the columns
            $columns = ["Stu_ID", "Stu_Idx", "Stu_Name", "Stu_Mykad", "Sch_ID"];
            fputcsv($csvfile, $columns);

            // write the rows
            foreach ($rows as $row) 
            {
//                $row['Stu_Mykad'] = "=\"$row[Stu_Mykad]\"";
                fputcsv($csvfile, $row);
            }
            fclose($csvfile);

            $result .= "-- Creating Migration File: {$csvName}". PHP_EOL;
            $result .= "-- Created Migration File: {$csvName}". PHP_EOL;
        } else {
            $result .= "-- Creating Migration File: {$csvName}". PHP_EOL;
            $result .= "-- Fail Create Migration File: {$csvName}. Try again later.". PHP_EOL;
        }

        return $result;
    }

    /**
     * @param string $input, $fullDir, $newDir 
     *
     * @return string result
     */
    public function migrateStamSubjects( $input, $fullDir, $newDir )
    {
        $result = '';

       // set the subject rows with its subject code.
       $rows = [];
       $subject1 = [1, '101', 'HIFZ AL-QURAN DAN TAJWID'];
       $subject2 = [2, '102', 'FIQH'];
       $subject3 = [3, '103', 'TAUHID DAN MANTIQ'];
       $subject4 = [4, '104', 'TAFSIR DAN ULUMUHU'];
       $subject5 = [5, '105', 'HADITH DAN MUSTOLAH'];
       $subject6 = [6, '106', 'NAHU DAN SARF'];
       $subject7 = [7, '107', 'INSYA\' DAN MUTALAAH'];
       $subject8 = [8, '108', 'ADAB DAN NUSUS'];
       $subject9 = [9, '109', '\'ARUDH DAN QAFIYAH'];
       $subject10 = [10, '110', 'BALAGHAH'];

       array_push( $rows,
            $subject1, $subject2, $subject3, $subject4, $subject5,
            $subject6, $subject7, $subject8, $subject9, $subject10
       );

        # write Subjects.csv #
        $csvName = $newDir."_Subjects.csv";
        if ( $open = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w') !== false )
        {
            $csvfile = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w');

            // write the columns
            $columns = ["Sub_ID", "Sub_Code", "Sub_Name"];
            fputcsv($csvfile, $columns);
            
            // write the rows
            foreach($rows as $row) 
            {
                fputcsv($csvfile, $row);
            }
            fclose($csvfile);

            $result .= "-- Creating Migration File: {$csvName}". PHP_EOL;
            $result .= "-- Created Migration File: {$csvName}". PHP_EOL;
        } else {
            $result .= "-- Creating Migration File: {$csvName}". PHP_EOL;
            $result .= "-- Fail Create Migration File: {$csvName}. Try again later.". PHP_EOL;
        }

        return $result;
    }

    /**
     * @param string $input, $fullDir, $newDir 
     *
     * @return string result
     */
    public function migrateStamGrades( $input, $fullDir, $newDir )
    {
        $result = '';

       // set the grade rows with its subject code.
       $rows = [];
       $subject1 = [1, '1', 'MUMTAZ'];
       $subject2 = [2, '2', 'JAYYID JIDDAN'];
       $subject3 = [3, '3', 'JAYYID'];
       $subject4 = [4, '4', 'MAQBUL'];
       $subject5 = [5, '5', 'RASIB'];
       $subject6 = [6, 'T', 'TIDAK HADIR'];

       array_push( $rows,
            $subject1, $subject2, $subject3,
            $subject4, $subject5, $subject6 
       );

        # write Subjects.csv #
        $csvName = $newDir."_Grades.csv";
        if ( $open = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w') !== false )
        {
            $csvfile = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w');

            // write the columns
            $columns = ["Sub_ID", "Sub_Code", "Sub_Name"];
            fputcsv($csvfile, $columns);
            
            // write the rows
            foreach($rows as $row) 
            {
                fputcsv($csvfile, $row);
            }
            fclose($csvfile);

            $result .= "-- Creating Migration File: {$csvName}". PHP_EOL;
            $result .= "-- Created Migration File: {$csvName}". PHP_EOL;
        } else {
            $result .= "-- Creating Migration File: {$csvName}". PHP_EOL;
            $result .= "-- Fail Create Migration File: {$csvName}. Try again later.". PHP_EOL;
        }

        return $result;
    }
    
        /**
     * @param string $input, $fullDir, $newDir 
     *
     * @return string result
     */
    public function migrateStamMarks( $input, $fullDir, $newDir, $db )
    {
        $result = '';
        $rows = [];

        # read .txt files #
        if ( file_exists( $input ) ) {
            if (( $txtfile = fopen($input, 'r') )  !== false )
            {
                while ( ($data = fgetcsv($txtfile, 1000, ",")) !== false ) 
                {
                    // convert to string
                    $str = implode(" ", $data);

                    // get the school code in the string:
                    $schoolCode = trim(substr($str,195,7));

                    if ( strlen( $schoolCode ) !== 7 ) 
                    {
                        // prepare the array for csv
                
                        // get student id from db based on student idx at data file
                        $studentId = $db->getStudentId( trim( substr($str,0,12)) );

                        // subject 1
                        $subject1 = [];
                        // get student id
                        $subject1['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get subject 1 subject id
                        $subject1['Sub_ID'] = 1;
                        // get subject 1 gred id
                        $gradeId = $db->getGradeStamId( trim( substr( $str,187,1 )) );
                        $subject1['Grade_ID'] = (int)$gradeId['Grade_ID'];

                        // var_dump($subject1);exit;


                        // subject 2
                        $subject2 = [];
                        // get student id
                        $subject2['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get subject 2 subject id
                        $subject2['Sub_ID'] = 2;
                        // get subject 2 gred id
                        $gradeId = $db->getGradeStamId( trim( substr( $str,212,1 )) );
                        $subject2['Grade_ID'] = (int)$gradeId['Grade_ID'];

                        // subject 3
                        $subject3 = [];
                        // get student id
                        $subject3['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get subject 3 subject id
                        $subject3['Sub_ID'] = 3;
                        // get subject 3 gred id
                        $gradeId = $db->getGradeStamId( trim( substr( $str,237,1 )) );
                        $subject3['Grade_ID'] = (int)$gradeId['Grade_ID'];
                        
                        // subject 4
                        $subject4 = [];
                        // get student id
                        $subject4['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get subject 4 subject id
                        $subject4['Sub_ID'] = 4;
                        // get subject 4 gred id
                        $gradeId = $db->getGradeStamId( trim( substr( $str,262,1 )) );
                        $subject4['Grade_ID'] = (int)$gradeId['Grade_ID'];

                        // subject 5
                        $subject5 = [];
                        // get student id
                        $subject5['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get subject 5 subject id
                        $subject5['Sub_ID'] = 5;
                        // get subject 5 gred id
                        $gradeId = $db->getGradeStamId( trim( substr( $str,287,1 )) );
                        $subject5['Grade_ID'] = (int)$gradeId['Grade_ID'];

                        // subject 6
                        $subject6 = [];
                        // get student id
                        $subject6['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get subject 6 subject id
                        $subject6['Sub_ID'] = 6;
                        // get subject 6 gred id
                        $gradeId = $db->getGradeStamId( trim( substr( $str,312,2 )) );
                        $subject6['Grade_ID'] = (int)$gradeId['Grade_ID'];

                        // subject 7
                        $subject7 = [];
                        // get student id
                        $subject7['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get subject 7 subject id
                        $subject7['Sub_ID'] = 7;
                        // get subject 7 gred id
                        $gradeId = $db->getGradeStamId( trim( substr( $str,337,2 )) );
                        $subject7['Grade_ID'] = (int)$gradeId['Grade_ID'];

                        // subject 8
                        $subject8 = [];
                        // get student id
                        $subject8['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get subject 8 subject id
                        $subject8['Sub_ID'] = 8;
                        // get subject 8 gred id
                        $gradeId = $db->getGradeStamId( trim( substr( $str,362,2 )) );
                        $subject8['Grade_ID'] = (int)$gradeId['Grade_ID'];

                        // subject 9
                        $subject9 = [];
                        // get student id
                        $subject9['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get subject 9 subject id
                        $subject9['Sub_ID'] = 9;
                        // get subject 9 gred id
                        $gradeId = $db->getGradeStamId( trim( substr( $str,387,2 )) );
                        $subject9['Grade_ID'] = (int)$gradeId['Grade_ID'];
                        
                        // subject 10
                        $subject10 = [];
                        // get student id
                        $subject10['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get subject 10 subject id
                        $subject10['Sub_ID'] = 10;
                        // get subject 10 gred id
                        $gradeId = $db->getGradeStamId( trim( substr( $str,412,2 )) );
                        $subject10['Grade_ID'] = (int)$gradeId['Grade_ID'];

                        array_push( $rows,
                            $subject1, $subject2, $subject3, $subject4, $subject5,
                            $subject6, $subject7, $subject8, $subject9, $subject10
                        );
                        
                        // print_r($rows);exit;
                    } else {
                        continue;
                    }
                }
            }

        // print_r($rows);exit;

        } else {
            $result .= "-- Fail to create {$newDir} file...". PHP_EOL; exit;
        }
        fclose($txtfile);

        # write Marks.csv #
        $csvName = $newDir."_Marks.csv";
        if ( $open = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w') !== false )
        {
            $csvfile = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w');

            // write the columns
            $columns = ["Stu_ID", "Sub_ID", "Grade_ID"];
            fputcsv($csvfile, $columns);

            // write the rows
            foreach ($rows as $row) 
            {
                fputcsv($csvfile, $row);
            }
            fclose($csvfile);

            $result .= "-- Creating Migration File: {$csvName}". PHP_EOL;
            $result .= "-- Created Migration File: {$csvName}". PHP_EOL;
        } else {
            $result .= "-- Creating Migration File: {$csvName}". PHP_EOL;
            $result .= "-- Fail Create Migration File: {$csvName}. Try again later.". PHP_EOL;
        }

        return $result;    
    }
}   