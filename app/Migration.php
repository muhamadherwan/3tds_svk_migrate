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
    public object $mkdirMigrate;

    /**
     * @param mixed
     *
     * @return void
     */
    public function __construct( string $title , MkdirMigrate $mkdirMigrate)
    {
        $this->title = $title;
        $this->mkdirMigrate = $mkdirMigrate;

        // $this->mkdirMigrate = new MkdirMigrate(  $this->fullDir, $this->newDir );
        
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
     * @param string $title PMR or STAM
     *
     * @return string
     */
    public function startPmr(): string
    {
        // make migration dir
        $result = $this->mkdirMigrate->create( $this->title );
        return $result;
    }

    /**
     * @param mixed string $title PMR or SPAM
     *
     * @return string
     */
    public function startStam(): string
    {
        // make migration dir
        $result = $this->mkdirMigrate->create( $this->title );
        return $result;
    }

}