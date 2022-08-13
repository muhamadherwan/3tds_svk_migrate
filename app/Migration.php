<?php
/**
 * @author Herwan <mdherwan@gmail.com>
 * @link -
 */

declare(strict_types = 1);

class Migration
{
    // props
    public string $name;

    /**
     * @param mixed
     *
     * @return void
     */
    public function __construct() {
        $this->name = 'ZUCK';
        // excess static props
        // self::$counter++;
    }

    /**
     * @param string $type PMR or STAM
     *
     * @return string
     */
    public function start( string $type )
    { 

        switch ( substr( $type,0,3 ) )
        {
            case 'PMR':
                $result = $this->startPmr( $type );
                break;
            case 'STA':
                $result = $this->startStam( $type );
                break;
            default:
                $result = "Please enter a correct command line!\n";
        }

        echo $result;

    }

    /**
     * @param mixed string $type PMR or SPAM
     *
     * @return string
     */
    public function startPmr( string $type ):string
    {
        return "start {$type}{$this->name}";
    }

    /**
     * @param mixed string $type PMR or SPAM
     *
     * @return string
     */
    public function startStam( string $type ):string
    {
        return "start {$type}";
    }

}