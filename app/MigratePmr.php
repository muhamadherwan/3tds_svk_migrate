<?php

/**
 * @author Herwan <mdherwan@gmail.com>
 * @link -
 */

declare( strict_types = 1 );

class MigratePmr
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
    public function create( $input, $fullDir, $newDir,  $db )
    {
        $result = '';
        $result .= $this->migratePmrGvsSchools( $input, $fullDir, $newDir, $db );
        $result .= $this->migratePmrStudents( $input, $fullDir, $newDir, $db );
        $result .= $this->migratePmrSubjects( $input, $fullDir, $newDir );
        $result .= $this->migratePmrGrades( $input, $fullDir, $newDir );
        $result .= $this->migratePmrMarks( $input, $fullDir, $newDir, $db );

        return $result;
    }

    /**
     * @param string $input, $fullDir, $newDir 
     *
     * @return string result
     */
    public function migratePmrGvsSchools( $input, $fullDir, $newDir, $db )
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
                    $schoolCode = trim(substr($str,123,8));

                    // var_dump($schoolCode);exit;
                    // check if current row have school code.
                    // if none, continue loop, else get the required data and save in new array set.
                    if ( strlen( $schoolCode ) !== 7 ) 
                    {
                        continue;
                    } else {

                        // prepare the array for csv
                        $data2 = [];
                        $data2['sch_ID'] = $id++;
                        $data2['sch_Name'] = trim(substr($str,14,54));
                        $data2['sch_PhoneNo'] = '';
                        $data2['sch_Email'] = '';
                        $data2['sch_Address'] = '';
                        $data2['sch_Code'] = $schoolCode;
                        $data2['sch_Year'] = '';
                        $data2['sch_Status'] = '';

                        $rows[] = $data2;

                        // save data for db
                        $data3 = [];
                        $data3['sch_Name'] = $data2['sch_Name'];
                        $data3['no_pusat'] = trim(substr($str,6,6));
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
    public function migratePmrStudents( $input, $fullDir, $newDir, $db )
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
                    $schoolCode = trim(substr($str,123,8));
                    
                    // check if current row have school code.
                    // if none, continue loop, else get the required data and save in new array set.
                    if ( strlen( $schoolCode ) !== 7 ) 
                    {
                        // get school id from db based on student no pusat
                        $sch_id = $db->getSchoolId( trim(substr($str,6,6)) );
                        
                        // prepare array for csv
                        $data2 = [];
                        $data2['Stu_ID'] = $id++;
                        $data2['Stu_Idx'] = trim( substr( $str,6,9 ));
                        $data2['Stu_Name'] = trim( substr( $str,16,40 ));
                        $data2['Stu_Mykad'] = (int)trim( substr( $str,56,12 )); 
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
    public function migratePmrSubjects( $input, $fullDir, $newDir )
    {
        $result = '';

        # read .txt files #
        if ( file_exists( $input ) ) {
            if (( $txtfile = fopen($input, 'r') )  !== false )
            {
                while ( ($data = fgetcsv($txtfile, 1000, ",")) !== false ) 
                {
                    // convert to string
                    $str = implode(" ", $data);

                    // get the school code in the string:
                    $schoolCode = trim(substr($str,123,8));

                    // check if current row have school code.
                    // if none, continue loop, else get the subject code and save in new array set.
                    if ( strlen( $schoolCode ) !== 7 ) 
                    {
                        $data2 = [];
                        $data2['sub_code1'] = trim( substr( $str,100,2 ));
                        $data2['sub_code2'] = trim( substr( $str,109,2 ));
                        $data2['sub_code3'] = trim( substr( $str,118,2 ));
                        $data2['sub_code4'] = trim( substr( $str,127,2 ));
                        $data2['sub_code5'] = trim( substr( $str,136,2 ));
                        $data2['sub_code6'] = trim( substr( $str,145,2 ));
                        $data2['sub_code7'] = trim( substr( $str,154,2 ));
                        $data2['sub_code8'] = trim( substr( $str,163,2 ));
                        $data2['sub_code9'] = trim( substr( $str,172,2 ));
                        break;
                    } else {
                        continue;
                    } 

                    var_dump($data2['sub_code1'] = trim( substr( $str,100,2 )));exit;
                }

                // set the subject rows with its subject code.
                $rows = [];
                $subject1 = [1, $data2['sub_code1'], 'MATA01'];
                $subject2 = [2, $data2['sub_code2'], 'MATA02'];
                $subject3 = [3, $data2['sub_code3'], 'MATA03'];
                $subject4 = [4, $data2['sub_code4'], 'MATA04'];
                $subject5 = [5, $data2['sub_code5'], 'MATA05'];
                $subject6 = [6, $data2['sub_code6'], 'MATA06'];
                $subject7 = [7, $data2['sub_code7'], 'MATA07'];
                $subject8 = [8, $data2['sub_code8'], 'MATA08'];
                $subject9 = [9, $data2['sub_code9'], 'MATA09'];
               
                array_push( $rows,
                     $subject1, $subject2, $subject3, $subject4, $subject5,
                     $subject6, $subject7, $subject8, $subject9
                );
            }   

        } else {
            $result .= "-- Fail to create {$newDir} file...". PHP_EOL; exit;
        }
        fclose($txtfile);

        # write Subjects.csv #
        $csvName = $newDir."_Subjects.csv";
        if ( $open = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w') !== false )
        {
            $csvfile = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w');

            // write the columns
            $columns = ["Sub_ID", "Sub_Code", "Sub_Name"];
            fputcsv($csvfile, $columns);

            // print_r($rows); exit;
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
    public function migratePmrGrades( $input, $fullDir, $newDir )
    {
        $result = '';

        // EXAMPLE GRED (NEED TO CONFORM!):
        // A,A1,A2,B,B1,B2,C,C1,C2,D,D1,D2

        // set the grade rows with its subject code.
        $rows = [];
        $gred1 = [1, 'A'];
        $gred2 = [2, 'A1'];
        $gred3 = [3, 'A2'];
        $gred4 = [4, 'B'];
        $gred5 = [5, 'B1'];
        $gred6 = [6, 'B2'];
        $gred7 = [7, 'C'];
        $gred8 = [8, 'C1'];
        $gred9 = [9, 'C2'];
        $gred10 = [10, 'D'];
        $gred10 = [11, 'D1'];
        $gred10 = [12, 'D2'];
        
        array_push( $rows,
                $gred1, $gred2, $gred3, $gred4, $gred5,
                $gred6, $gred7, $gred8, $gred9, $gred10
        );

        # write Grades.csv #
        $csvName = $newDir."_Grades.csv";
        if ( $open = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w') !== false )
        {
            $csvfile = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w');

            // write the columns
            $columns = ["Grade_ID", "Grade"];
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
    public function migratePmrMarks( $input, $fullDir, $newDir, $db )
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
                    $schoolCode = trim(substr($str,123,8));

                    if ( strlen( $schoolCode ) !== 7 ) 
                    {
                        // prepare the array for csv
                        
                        // get student id from db based on student idx
                        $studentId = $db->getStudentId( trim(substr($str,6,9)) );

                        // mata 01
                        $mata01 = [];
                        // get student id
                        $mata01['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get mata01 subject id
                        $mata01['Sub_ID'] = 1;
                        // get mata01 gred id
                        $gradeId = $db->getGradeId( trim( substr( $str,102,2 )) );
                        $mata01['Grade_ID'] = (int)$gradeId['id'];

                        // mata 02
                        $mata02 = [];
                        // get student id
                        $mata02['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get mata02 subject id
                        $mata02['Sub_ID'] = 2;
                        // get mata02 gred id
                        $gradeId = $db->getGradeId( trim( substr( $str,111,2 )) );
                        $mata02['Grade_ID'] = (int)$gradeId['id'];

                        // mata 03
                        $mata03 = [];
                        // get student id
                        $mata03['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get mata03 subject id
                        $mata03['Sub_ID'] = 3;
                        // get mata03 gred id
                        $gradeId = $db->getGradeId( trim( substr( $str,120,2 )) );
                        $mata03['Grade_ID'] = (int)$gradeId['id'];
                        
                        // mata 04
                        $mata04 = [];
                        // get student id
                        $mata04['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get mata04 subject id
                        $mata04['Sub_ID'] = 4;
                        // get mata04 gred id
                        $gradeId = $db->getGradeId( trim( substr( $str,129,2 )) );
                        $mata04['Grade_ID'] = (int)$gradeId['id'];

                        // mata 05
                        $mata05 = [];
                        // get student id
                        $mata05['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get mata05 subject id
                        $mata05['Sub_ID'] = 5;
                        // get mata05 gred id
                        $gradeId = $db->getGradeId( trim( substr( $str,138,2 )) );
                        $mata05['Grade_ID'] = (int)$gradeId['id'];

                        // mata 06
                        $mata06 = [];
                        // get student id
                        $mata06['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get mata06 subject id
                        $mata06['Sub_ID'] = 6;
                        // get mata06 gred id
                        $gradeId = $db->getGradeId( trim( substr( $str,147,2 )) );
                        $mata06['Grade_ID'] = (int)$gradeId['id'];

                        // mata 07
                        $mata07 = [];
                        // get student id
                        $mata07['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get mata07 subject id
                        $mata07['Sub_ID'] = 7;
                        // get mata07 gred id
                        $gradeId = $db->getGradeId( trim( substr( $str,156,2 )) );
                        $mata07['Grade_ID'] = (int)$gradeId['id'];

                        // mata 08
                        $mata08 = [];
                        // get student id
                        $mata08['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get mata08 subject id
                        $mata08['Sub_ID'] = 8;
                        // get mata08 gred id
                        $gradeId = $db->getGradeId( trim( substr( $str,165,2 )) );
                        $mata08['Grade_ID'] = (int)$gradeId['id'];

                        // mata 09
                        $mata09 = [];
                        // get student id
                        $mata09['Stu_ID'] = (int)$studentId['Stu_ID'];
                        // get mata09 subject id
                        $mata09['Sub_ID'] = 9;
                        // get mata09 gred id
                        $gradeId = $db->getGradeId( trim( substr( $str,174,2 )) );
                        $mata09['Grade_ID'] = (int)$gradeId['id'];
                        
                        // add all subject array to rows array                        
                        array_push( $rows,
                            $mata01, $mata02, $mata03, $mata04, $mata05,
                            $mata06, $mata07, $mata08, $mata09
                        );    
                    } else {
                        continue;
                    }
                }
            }
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