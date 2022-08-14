<?php

/**
 * @author Herwan <mdherwan@gmail.com>
 * @link -
 */

declare(strict_types = 1);

class MkdirMigrate
{
    // props
    public string $root;


    /**
     * @param mixed
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * @param string title
     *
     * @return string result
     */
    public function create( string $title ): string
    {
        $newDir = str_replace( 'migrate:', '', $title );
        $fullDir = OUTPUT_PATH . $newDir;
            
        $result = '';
        
        // if the dir is not exist, create it. 
        // Else delete the dir and its content, then recreate the dir.
        if ( !is_dir( $fullDir ) )
        {
            // create new dir with writeable permission (chmod 0777).
            if ( mkdir ($fullDir, 0777, true ) )
            {
                $result .= "-- Creating Migration Directory: {$newDir}". PHP_EOL;
                $result .= "-- Created Migration Directory: {$newDir}". PHP_EOL;
            } else {
                $result .= "Fail to create {$newDir} directory...".PHP_EOL; exit;
            }    
        } else {
            $result .= "-- Migration Directory: {$newDir} already exist." . PHP_EOL;
            $result .= "-- Deleting all files in Migration Directory: {$newDir}" . PHP_EOL;

            // delete all file in the dir
            $files = glob( $fullDir. DIRECTORY_SEPARATOR .'*' ); // get all file names
            foreach( $files as $file ) { // iterate files
                if( is_file( $file ) )
                {
                    unlink( $file ); // delete file
                }
            }    
            $result .= "-- Successfully deleting all files in Migration Directory: {$newDir}" . PHP_EOL;

            // delete the dir
            $result .= "-- Deleting Migration Directory: {$newDir}". PHP_EOL;
            rmdir($fullDir);
            $result .= "-- Successfully deleting Migration Directory: {$newDir}". PHP_EOL;
            $result .= "-- Creating Migration Directory: {$newDir}". PHP_EOL;
            
            // create new dir
            if ( mkdir( $fullDir, 0777, true ) )
            {
                $result .= "-- Created Migration Directory: {$newDir}". PHP_EOL;
            } else {
                $result .= "-- Fail to create {$newDir} directory...". PHP_EOL; exit;
            }
        }

        return $result;
            
    }

}    