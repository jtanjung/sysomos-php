<?php
namespace SysOmos\Interfaces;

/**
 * Interface IModule
 * @package SysOmos\Interfaces
 */
interface IModule {

    /**
     * Extarct response into model structure
     *
     * @return boolean
     */
    public function DoExtract();
}