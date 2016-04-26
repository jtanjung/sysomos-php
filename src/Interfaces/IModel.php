<?php
namespace SysOmos\Interfaces;

/**
 * Interface IModel
 * @package SysOmos\Interfaces
 */
interface IModel {

    /**
     * Add new data to list storage
     *
     * @param mixed $value
     * @return self
     */
    public function AddList( $value );
}