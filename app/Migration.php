<?php

/**
 * @author Herwan <mdherwan@gmail.com>
 * @link -
 */

declare(strict_types = 1);

class Migration
{
    // props
    public string $title;
    public string $input;
    public object $mkdirMigrate;
    public object $migratePmr;
    public object $migrateStam;
    public string $newDir;
    public string $fullDir;

    /**
     * @param mixed
     *
     * @return void
     */
    public function __construct( string $title , string $input, MkdirMigrate $mkdirMigrate, MigratePmr $migratePmr, MigrateStam $migrateStam )
    {
        $this->title = $title;
        $this->input = $input;
        $this->mkdirMigrate = $mkdirMigrate;
        $this->migratePmr = $migratePmr;
        $this->migrateStam = $migrateStam;
        $this->newDir = str_replace( 'migrate:', '', $title );
        $this->fullDir = OUTPUT_PATH . $this->newDir;
    }

    /**
     * @param
     *
     * @return string
     */
    public function start()
    { 

        switch ( substr( $this->title, 0, 3 ) )
        {
            case 'PMR':
                $result = $this->startPmr();
                break;
            case 'STA':
                $result = $this->startStam();
                break;
            default:
                $result = "Please enter a correct command line!\n";
        }

        echo $result;

    }

    /**
     * @param string $title PMR
     *
     * @return string
     */
    public function startPmr(): string
    {
        // make migration dir
        $result = $this->mkdirMigrate->start();
        
        // make migration csv files
        $result .= $this->migratePmr->create( $this->input, $this->fullDir, $this->newDir );
        return $result;
    }

    /**
     * @param mixed string $title SPAM
     *
     * @return string
     */
    public function startStam(): string
    {
        // make migration dir
        $result = $this->mkdirMigrate->start();
        
        // make migration csv files
        $result .= $this->migrateStam->create( $this->input, $this->fullDir, $this->newDir );
        return $result;
    }

}