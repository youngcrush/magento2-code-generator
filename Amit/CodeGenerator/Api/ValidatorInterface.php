<?php
namespace Amit\CodeGenerator\Api;

interface ValidatorInterface
{

    /**
     * Generate code
     *
     * @param array $data
     * @return boolean
     */
    public function validate($data);
}
