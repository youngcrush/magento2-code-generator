<?php
/**
 * Amit Software.
 *
 * @package   Amit_CodeGenerator
 * @author    Amit
 */

namespace Amit\CodeGenerator\Model\Generate\Helper;

class Validator implements \Amit\CodeGenerator\Api\ValidatorInterface
{
    public function validate($data)
    {
        $module = $data['module'];
        $type = $data['type'];
        $name = $data['name'];
        $response = [];
        if ($module) {
            $moduleManager = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Module\ModuleListInterface::class);
            $moduleData = $moduleManager->getOne($module);
            if (!$moduleData) {
                throw new \InvalidArgumentException(__("invalid module name"));
            }
            $response["module"] = $module;
        } else {
            throw new \InvalidArgumentException(__("module name not provided"));
        }

        if ($name) {
            $response["name"] = $name;
        } else {
            throw new \InvalidArgumentException(__("name is required"));
        }

        $dir = \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Framework\Module\Dir::class);

        $modulePath = $dir->getDir($module);
        $response["path"] = $modulePath;
        $response["type"] = $type;
        
        return $response;
    }
}