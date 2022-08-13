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
     * @param string $type PMR or SPM
     *
     * @return void
     */
    public function start( string $type )
    { 

        echo $result = ( preg_match( "/\PMR:\b/", $type ) ) ? self::startPmr( $type ) : self::startSpm( $type );

    }

    /**
     * @param mixed string $type PMR or SPM
     *
     * @return string
     */
    public function startPmr( string $type ):string
    {
        return "start {$type}{$this->name}";
    }

    /**
     * @param mixed string $type PMR or SPM
     *
     * @return string
     */
    public function startSpm( string $type ):string
    {
        return "start {$type}";
    }

}