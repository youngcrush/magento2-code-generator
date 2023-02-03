<?php
/**
 * Amit Software.
 *
 * @package   Amit_CodeGenerator
 * @author    Amit
 */

namespace Amit\CodeGenerator\Api;

/**
 * Interface ValidatorInterface
 */
interface ValidatorInterface
{

    /**
     * generate code
     *
     * @param [] $data
     * @return boolean
     */
    public function validate($data);

}