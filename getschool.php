<?php

// current dir
$curDir = getcwd();

// $newDir = 'test';
$output = "output";
// full dir
$fullDir = $curDir.'/'.$output.'/';

// print $fullDir;exit;    
// start creating the .csv file

// $fileHandle = $curDir.'/input/PMR1998.txt';
$input = $curDir.'/input/PMR1998a.txt';
// read the data row in .txt file 
// put it in array separated by coma


$lines = [];

// $data = [];

if (file_exists($input)) {
    if (( $txtfile = fopen($input, 'r') )  !== false )
    {
        while ( ($data = fgetcsv($txtfile, 1000, ",")) !== false ) 
        {
            // convert to string
            $str = implode(" ",$data);

            // get the school code in the string:
            // $schoolCode = substr($str,123,7);
            $schoolCode = trim(substr($str,122,8));

            // check if current row have school code. if have get the required data and save in new array set.
            if ( strlen($schoolCode) !== 7 ) 
            {
                continue;
            } else {

                $data2 = [];
                $data2['sch_ID'] = '';
                $data2['sch_Name'] = trim(substr($str,14,54));
                $data2['sch_PhoneNo'] = '';
                $data2['sch_Email'] = '';
                $data2['sch_Address'] = '';
                $data2['sch_Code'] = $schoolCode;
                $data2['sch_Year'] = '';
                $data2['sch_Status'] = '';

                $lines[] = $data2;
            }           
        }
    }    
} else {
    echo "-- Fail to create {$newDir} file...". PHP_EOL;exit;
}
fclose($txtfile);

# start create gvs_schools.csv #
$headers = ["sch_ID", "sch_Name", "sch_PhoneNo", "sch_Email", "sch_Address", "sch_Code", "sch_Year", "sch_Status"];
					
$csvName = "gvs_schools.csv";
// $fileHandle = fopen($csvName, 'w') or die('Can\'t create .csv file, try again later.');

// write the array in .csv file row by rows
$csvfile = fopen($fullDir. DIRECTORY_SEPARATOR . $csvName, 'w') or die('Can\'t create .csv file, try again later.');

echo "-- Creating Migration File: {$csvName}". PHP_EOL;

//Add the headers
fputcsv($csvfile, $headers);

foreach ($lines as $line) {
    fputcsv($csvfile, $line);
}
fclose($csvfile);
// echo "-- {$csvName} file has been migrated successfully...". PHP_EOL;
echo "-- Created Migration File: {$csvName}". PHP_EOL;