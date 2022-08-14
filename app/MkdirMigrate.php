<?php

/**
 * @author Herwan <mdherwan@gmail.com>
 * @link -
 */

declare( strict_types = 1 );

class MkdirMigrate
{
    // props
    public string $title;
    public string $newDir;
    public string $fullDir;

    /**
     * @param string title
     *
     * @return void
     */
    public function __construct( string $title )
    {
        $this->title = $title;
        $this->newDir = str_replace( 'migrate:', '', $title );
        $this->fullDir = OUTPUT_PATH . $this->newDir;
    }

    /**
     * @param
     *
     * @return string result
     */
    public function start()
    {
        // if the dir is not exist, create it. Else delete the dir and its content, then recreate the dir.
        return $result = ( !is_dir( $this->fullDir ) ) ? $this->create() : $this->reCreate(); 
    }

    /**
     * @param
     *
     * @return string result
     */
    public function create(): string
    {
        $result = '';
    
        if ( !is_dir( $this->fullDir ) )
        {
            // create new dir with writeable permission (chmod 0777).
            if ( mkdir ( $this->fullDir, 0777, true ) )
            {
                $result .= "-- Creating Migration Directory: {$this->newDir}". PHP_EOL;
                $result .= "-- Created Migration Directory: {$this->newDir}". PHP_EOL;
            } else {
                $result .= "Fail to create {$this->newDir} directory...".PHP_EOL; exit;
            }
        }

        return $result;
    }

    /**
     * @param
     *
     * @return string result
     */
    public function reCreate()
    {
        $result = '';
        $result .= "-- Migration Directory: {$this->newDir} already exist." . PHP_EOL;
        $result .= $this->destroy(); // delete the dir
        return $result .= $this->create(); // // create new dir with writeable permission (chmod 0777).
    }

    /**
     * @param
     *
     * @return string result
     */
    public function destroy()
    {
        $result = '';
        $result .= "-- Deleting all files in Migration Directory: {$this->newDir}" . PHP_EOL;

        // delete all file in the dir
        $files = glob( $this->fullDir. DIRECTORY_SEPARATOR .'*' ); // get all file names
        foreach( $files as $file ) { // iterate files
            if( is_file( $file ) )
            {
                unlink( $file ); // delete file
            }
        }

        $result .= "-- Successfully deleting all files in Migration Directory: {$this->newDir}" . PHP_EOL;

        // delete the dir
        $result .= "-- Deleting Migration Directory: {$this->newDir}". PHP_EOL;
        rmdir($this->fullDir);
        return $result .= "-- Successfully deleting Migration Directory: {$this->newDir}". PHP_EOL;
    }

}    