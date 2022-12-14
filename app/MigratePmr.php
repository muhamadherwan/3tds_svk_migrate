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
        echo "Start Migrate School....\n";
        $result = $this->migratePmrGvsSchools($input, $fullDir, $newDir, $db);
        echo "Done Migrate School...\n";
        echo "Start Migrate Students...\n";
        $result .= $this->migratePmrStudents( $input, $fullDir, $newDir, $db );
        echo "Done migrate Students...\n";
        echo "Start migrate Marks...\n";
        $result .= $this->migratePmrMarks( $input, $fullDir, $newDir, $db );
        //$result .= $this->migratePmrSubjects( $input, $fullDir, $newDir );
        //$result .= $this->migratePmrGrades( $input, $fullDir, $newDir );

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
                $lastId = $db->getSchoollastId();
                if (!empty($lastId))
                {
                    $id = implode('', $lastId);
                    $id += 1;
                } else {
                    $id = 1;
                }

                while ( ($data = fgetcsv($txtfile, 1000, ",")) !== false ) 
                {
                    // convert to string
                    $str = implode(" ", $data);

                    $jenRek = trim(substr($str,2,1));
                    $schoolCode = trim(substr($str,3,6));

                    if ( $jenRek != '2')
                    {
                        $exist = $db->getSchoolId($schoolCode);
                        if (empty($exist)) {
                            // prepare the array for csv
                            $data2 = [];
//                            $newId = $id + 1;
                            $data2['sch_ID'] = $id++;
//                            $data2['sch_ID'] = $newId;
                            $data2['sch_Name'] = trim(substr($str,13,54));
                            $data2['sch_PhoneNo'] = '';
                            $data2['sch_Email'] = '';
                            $data2['sch_Address'] = '';
                            $data2['sch_Code'] = $schoolCode;
                            $data2['sch_Year'] = '';
                            $data2['sch_Status'] = '';
                            $rows[] = $data2;

                            // save data for db
                            $data3 = [];
                            $data3['sch_ID'] = $data2['sch_ID'];
                            $data3['sch_Name'] = $data2['sch_Name'];
                            $data3['no_pusat'] = trim(substr($str,4,5));
                            $data3['code'] = $schoolCode;
                            $db->storedSchool( $data3 );
                        }
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
//                $id = 1;
                $lastId = $db->getStudentlastId();
                if (!empty($lastId))
                {
                    $id = implode('', $lastId);
                    $id += 1;
                } else {
                    $id = 1;
                }

                while ( ($data = fgetcsv($txtfile, 1000, ",")) !== false ) 
                {
                    // convert to string
                    $str = implode(" ", $data);

                    $jenRek = trim(substr($str,2,1));
                    $schoolCode = trim(substr($str,3,6));

                    if ( $jenRek != '1')
                    {
                        $sch_id = $db->getSchoolId($schoolCode);

                        // prepare array for csv
                        $data2 = [];
                        $data2['Stu_ID'] = $id++;
                        $data2['Stu_Idx'] = trim(substr($str, 3, 10));
                        $data2['Stu_Name'] = trim(substr($str, 13, 40));
                        //$data2['Stu_Mykad'] = (int)trim(substr($str, 53, 12));
                        $data2['Stu_Mykad'] = trim(substr($str, 53, 12));
                        $data2['Sch_ID'] = (int)$sch_id['id'];
                        $rows[] = $data2;

                        // stored array to db
                        $db->storedStudent($data2);
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

                    $jenRek = trim(substr($str,2,1));

                    if ( '1'  != $jenRek )
                    {
                        // prepare the array for csv
                        $studentId = $db->getStudentId( trim(substr($str,3,10)) );

                        // get total subject
                        $total = trim(substr($str, 94,1));

                        if ( '9' == $total ) {
                            $mata01 = [];
                            // set student id
                            $mata01['Stu_ID'] = (int)$studentId['Stu_ID'];

                            $subject1 = $db->getPmrSubjectId(trim(substr($str, 97,2)));
                            $mata01['Sub_ID'] = (int)$subject1['Sub_ID'];
                            // set mata01 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,99,1 )) );
                            $mata01['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 02
                            $mata02 = [];
                            // get student id
                            $mata02['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata02 subject id
                            $subject2 = $db->getPmrSubjectId(trim(substr($str, 106,2)));
                            $mata02['Sub_ID'] = (int)$subject2['Sub_ID'];
                            // get mata02 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,108,1)) );
                            $mata02['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 03
                            $mata03 = [];
                            // get student id
                            $mata03['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata03 subject id
                            $subject3 = $db->getPmrSubjectId(trim(substr($str, 115,2)));
                            $mata03['Sub_ID'] = (int)$subject3['Sub_ID'];
                            // get mata03 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,117,1 )) );
                            $mata03['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 04
                            $mata04 = [];
                            // get student id
                            $mata04['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata04 subject id
                            $subject4 = $db->getPmrSubjectId(trim(substr($str, 124,2)));
                            $mata04['Sub_ID'] = (int)$subject4['Sub_ID'];
                            // get mata04 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,126,1 )) );
                            $mata04['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 05
                            $mata05 = [];
                            // get student id
                            $mata05['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata05 subject id
                            $subject5 = $db->getPmrSubjectId(trim(substr($str, 133,2)));
                            $mata05['Sub_ID'] = (int)$subject5['Sub_ID'];
                            // get mata05 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,135,1 )) );
                            $mata05['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 06
                            $mata06 = [];
                            // get student id
                            $mata06['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata06 subject id
                            $subject6 = $db->getPmrSubjectId(trim(substr($str, 142,2)));
                            $mata06['Sub_ID'] = (int)$subject6['Sub_ID'];
                            // get mata06 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,144,1 )) );
                            $mata06['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 07
                            $mata07 = [];
                            // get student id
                            $mata07['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata07 subject id
                            $subject7 = $db->getPmrSubjectId(trim(substr($str, 151,2)));
                            $mata07['Sub_ID'] = (int)$subject7['Sub_ID'];
                            // get mata07 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,153,1 )) );
                            $mata07['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 08
                            $mata08 = [];
                            // get student id
                            $mata08['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata08 subject id
                            $subject8 = $db->getPmrSubjectId(trim(substr($str, 160,2)));
                            $mata08['Sub_ID'] = (int)$subject8['Sub_ID'];
                            // get mata08 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,162,1 )) );
                            $mata08['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 09
                            $mata09 = [];
                            // get student id
                            $mata09['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata09 subject id
                            $subject9 = $db->getPmrSubjectId(trim(substr($str, 169,2)));
                            $mata09['Sub_ID'] = (int)$subject9['Sub_ID'];
                            // get mata09 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,171,1 )) );
                            $mata09['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // add all subject array to rows array
                            array_push( $rows,
                                $mata01, $mata02, $mata03, $mata04, $mata05,
                                $mata06, $mata07, $mata08, $mata09
                            );
                        } elseif ( '8'  == $total )
                        {
                            $mata01 = [];
                            // set student id
                            $mata01['Stu_ID'] = (int)$studentId['Stu_ID'];

                            $subject1 = $db->getPmrSubjectId(trim(substr($str, 97,2)));
                            $mata01['Sub_ID'] = (int)$subject1['Sub_ID'];
                            // set mata01 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,99,1 )) );
                            $mata01['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 02
                            $mata02 = [];
                            // get student id
                            $mata02['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata02 subject id
                            $subject2 = $db->getPmrSubjectId(trim(substr($str, 106,2)));
                            $mata02['Sub_ID'] = (int)$subject2['Sub_ID'];
                            // get mata02 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,108,1)) );
                            $mata02['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 03
                            $mata03 = [];
                            // get student id
                            $mata03['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata03 subject id
                            $subject3 = $db->getPmrSubjectId(trim(substr($str, 115,2)));
                            $mata03['Sub_ID'] = (int)$subject3['Sub_ID'];
                            // get mata03 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,117,1 )) );
                            $mata03['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 04
                            $mata04 = [];
                            // get student id
                            $mata04['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata04 subject id
                            $subject4 = $db->getPmrSubjectId(trim(substr($str, 124,2)));
                            $mata04['Sub_ID'] = (int)$subject4['Sub_ID'];
                            // get mata04 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,126,1 )) );
                            $mata04['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 05
                            $mata05 = [];
                            // get student id
                            $mata05['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata05 subject id
                            $subject5 = $db->getPmrSubjectId(trim(substr($str, 133,2)));
                            $mata05['Sub_ID'] = (int)$subject5['Sub_ID'];
                            // get mata05 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,135,1 )) );
                            $mata05['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 06
                            $mata06 = [];
                            // get student id
                            $mata06['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata06 subject id
                            $subject6 = $db->getPmrSubjectId(trim(substr($str, 142,2)));
                            $mata06['Sub_ID'] = (int)$subject6['Sub_ID'];
                            // get mata06 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,144,1 )) );
                            $mata06['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 07
                            $mata07 = [];
                            // get student id
                            $mata07['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata07 subject id
                            $subject7 = $db->getPmrSubjectId(trim(substr($str, 151,2)));
                            $mata07['Sub_ID'] = (int)$subject7['Sub_ID'];
                            // get mata07 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,153,1 )) );
                            $mata07['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 08
                            $mata08 = [];
                            // get student id
                            $mata08['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata08 subject id
                            $subject8 = $db->getPmrSubjectId(trim(substr($str, 160,2)));
                            $mata08['Sub_ID'] = (int)$subject8['Sub_ID'];
                            // get mata08 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,162,1 )) );
                            $mata08['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // add all subject array to rows array
                            array_push( $rows,
                                $mata01, $mata02, $mata03, $mata04, $mata05,
                                $mata06, $mata07, $mata08
                            );
                        } elseif ('7' == $total) {
                            $mata01 = [];
                            // set student id
                            $mata01['Stu_ID'] = (int)$studentId['Stu_ID'];

                            $subject1 = $db->getPmrSubjectId(trim(substr($str, 97,2)));
                            $mata01['Sub_ID'] = (int)$subject1['Sub_ID'];
                            // set mata01 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,99,1 )) );
                            $mata01['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 02
                            $mata02 = [];
                            // get student id
                            $mata02['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata02 subject id
                            $subject2 = $db->getPmrSubjectId(trim(substr($str, 106,2)));
                            $mata02['Sub_ID'] = (int)$subject2['Sub_ID'];
                            // get mata02 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,108,1)) );
                            $mata02['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 03
                            $mata03 = [];
                            // get student id
                            $mata03['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata03 subject id
                            $subject3 = $db->getPmrSubjectId(trim(substr($str, 115,2)));
                            $mata03['Sub_ID'] = (int)$subject3['Sub_ID'];
                            // get mata03 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,117,1 )) );
                            $mata03['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 04
                            $mata04 = [];
                            // get student id
                            $mata04['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata04 subject id
                            $subject4 = $db->getPmrSubjectId(trim(substr($str, 124,2)));
                            $mata04['Sub_ID'] = (int)$subject4['Sub_ID'];
                            // get mata04 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,126,1 )) );
                            $mata04['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 05
                            $mata05 = [];
                            // get student id
                            $mata05['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata05 subject id
                            $subject5 = $db->getPmrSubjectId(trim(substr($str, 133,2)));
                            $mata05['Sub_ID'] = (int)$subject5['Sub_ID'];
                            // get mata05 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,135,1 )) );
                            $mata05['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 06
                            $mata06 = [];
                            // get student id
                            $mata06['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata06 subject id
                            $subject6 = $db->getPmrSubjectId(trim(substr($str, 142,2)));
                            $mata06['Sub_ID'] = (int)$subject6['Sub_ID'];
                            // get mata06 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,144,1 )) );
                            $mata06['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // mata 07
                            $mata07 = [];
                            // get student id
                            $mata07['Stu_ID'] = (int)$studentId['Stu_ID'];
                            // get mata07 subject id
                            $subject7 = $db->getPmrSubjectId(trim(substr($str, 151,2)));
                            $mata07['Sub_ID'] = (int)$subject7['Sub_ID'];
                            // get mata07 gred id
                            $gradeId = $db->getGradePmrId( trim( substr( $str,153,1 )) );
                            $mata07['Grade_ID'] = (int)$gradeId['Grade_ID'];

                            // add all subject array to rows array
                            array_push( $rows,
                                $mata01, $mata02, $mata03, $mata04, $mata05,
                                $mata06, $mata07
                            );
                        }

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



    function setMarks9($studentId, $str, $db)
    {
        $mata01 = [];
        // set student id
        $mata01['Stu_ID'] = (int)$studentId['Stu_ID'];
        // set subject id
        $subject1 = $db->getPmrSubjectId(trim(substr($str, 100,2)));
        $mata01['Sub_ID'] = (int)$subject1['Sub_ID'];
        // set mata01 gred id
        $gradeId = $db->getGradePmrId( trim( substr( $str,102,1 )) );
        $mata01['Grade_ID'] = (int)$gradeId['Grade_ID'];

        // mata 02
        $mata02 = [];
        // get student id
        $mata02['Stu_ID'] = (int)$studentId['Stu_ID'];
        // get mata02 subject id
        $subject2 = $db->getPmrSubjectId(trim(substr($str, 109,2)));
        $mata02['Sub_ID'] = (int)$subject2['Sub_ID'];
        // get mata02 gred id
        $gradeId = $db->getGradePmrId( trim( substr( $str,111,1)) );
        $mata02['Grade_ID'] = (int)$gradeId['Grade_ID'];

        // mata 03
        $mata03 = [];
        // get student id
        $mata03['Stu_ID'] = (int)$studentId['Stu_ID'];
        // get mata03 subject id
        $subject3 = $db->getPmrSubjectId(trim(substr($str, 118,2)));
        $mata03['Sub_ID'] = (int)$subject3['Sub_ID'];
        // get mata03 gred id
        $gradeId = $db->getGradePmrId( trim( substr( $str,120,1 )) );
        $mata03['Grade_ID'] = (int)$gradeId['Grade_ID'];

        // mata 04
        $mata04 = [];
        // get student id
        $mata04['Stu_ID'] = (int)$studentId['Stu_ID'];
        // get mata04 subject id
        $subject4 = $db->getPmrSubjectId(trim(substr($str, 127,2)));
        $mata04['Sub_ID'] = (int)$subject4['Sub_ID'];
        // get mata04 gred id
        $gradeId = $db->getGradePmrId( trim( substr( $str,129,1 )) );
        $mata04['Grade_ID'] = (int)$gradeId['Grade_ID'];

        // mata 05
        $mata05 = [];
        // get student id
        $mata05['Stu_ID'] = (int)$studentId['Stu_ID'];
        // get mata05 subject id
        $subject5 = $db->getPmrSubjectId(trim(substr($str, 136,2)));
        $mata05['Sub_ID'] = (int)$subject5['Sub_ID'];
        // get mata05 gred id
        $gradeId = $db->getGradePmrId( trim( substr( $str,138,1 )) );
        $mata05['Grade_ID'] = (int)$gradeId['Grade_ID'];

        // mata 06
        $mata06 = [];
        // get student id
        $mata06['Stu_ID'] = (int)$studentId['Stu_ID'];
        // get mata06 subject id
        $subject6 = $db->getPmrSubjectId(trim(substr($str, 145,2)));
        $mata06['Sub_ID'] = (int)$subject6['Sub_ID'];
        // get mata06 gred id
        $gradeId = $db->getGradePmrId( trim( substr( $str,147,1 )) );
        $mata06['Grade_ID'] = (int)$gradeId['Grade_ID'];

        // mata 07
        $mata07 = [];
        // get student id
        $mata07['Stu_ID'] = (int)$studentId['Stu_ID'];
        // get mata07 subject id
        $subject7 = $db->getPmrSubjectId(trim(substr($str, 154,2)));
        $mata07['Sub_ID'] = (int)$subject7['Sub_ID'];
        // get mata07 gred id
        $gradeId = $db->getGradePmrId( trim( substr( $str,156,1 )) );
        $mata07['Grade_ID'] = (int)$gradeId['Grade_ID'];

        // mata 08
        $mata08 = [];
        // get student id
        $mata08['Stu_ID'] = (int)$studentId['Stu_ID'];
        // get mata08 subject id
        $subject8 = $db->getPmrSubjectId(trim(substr($str, 163,2)));
        $mata08['Sub_ID'] = (int)$subject8['Sub_ID'];
        // get mata08 gred id
        $gradeId = $db->getGradePmrId( trim( substr( $str,165,1 )) );
        $mata08['Grade_ID'] = (int)$gradeId['Grade_ID'];

        // mata 09
        $mata09 = [];
        // get student id
        $mata09['Stu_ID'] = (int)$studentId['Stu_ID'];
        // get mata09 subject id
        $subject9 = $db->getPmrSubjectId(trim(substr($str, 172,2)));
        $mata09['Sub_ID'] = (int)$subject9['Sub_ID'];
        // get mata09 gred id
        $gradeId = $db->getGradePmrId( trim( substr( $str,174,1 )) );
        $mata09['Grade_ID'] = (int)$gradeId['Grade_ID'];

        // add all subject array to rows array
        $rows = [];
        array_push( $rows,
            $mata01, $mata02, $mata03, $mata04, $mata05,
            $mata06, $mata07, $mata08, $mata09
        );
        return $rows;
    }

}
