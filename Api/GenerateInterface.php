<?php
/**
 * Amit Software.
 *
 * @package   Amit_CodeGenerator
 * @author    Amit
 */

namespace Amit\CodeGenerator\Api;

/**
 * Interface GenerateInterface
 */
interface GenerateInterface
{

    /**
     * generate code
     *
     * @param [] $data
     * @return boolean
     */
    public function execute($data);

}