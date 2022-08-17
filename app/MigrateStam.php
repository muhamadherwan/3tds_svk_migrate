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
                    $schoolCode = trim(substr($str,195,7));
                    // var_dump($schoolCode);exit;

                    // check if current row have school code.
                    // if none, continue loop, else get the required data and save in new array set.
                    if ( strlen( $schoolCode ) !== 7 ) 
                    {
                        continue;
                    } else {
                        $data2 = [];
                        $data2['sch_ID'] = $id++;
                        $data2['sch_Name'] = trim(substr($str,12,60));
                        $data2['sch_PhoneNo'] = trim(substr($str,179,16));
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
                    $schoolCode = trim(substr($str,196,7));

                    // check if current row have school code.
                    // if none, continue loop, else get the required data and save in new array set.
                    if ( strlen( $schoolCode ) !== 7 ) 
                    {
                        // get school id from db based on student no pusat
                        $sch_id = $db->getSchoolId( trim(substr($str,3,6)) );

                        $data2 = [];
                        $data2['Stu_ID'] = $id++;
                        $data2['Stu_Idx'] = trim( substr( $str,0,12 ));
                        $data2['Stu_Name'] = trim( substr( $str,12,40 ));
                        $data2['Stu_Mykad'] = (int)trim( substr( $str,52,12 )); 
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
}   