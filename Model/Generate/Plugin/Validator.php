<?php
/**
 * Amit Software.
 *
 * @package   Amit_CodeGenerator
 * @author    Amit
 */

namespace Amit\CodeGenerator\Model\Generate\Plugin;

class Validator implements \Amit\CodeGenerator\Api\ValidatorInterface
{
    /**
     * Validate command params
     *
     * @param array $data
     * @return array
     */
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
                throw new \InvalidArgumentException(__("Invalid module name"));
            }
            $response["module"] = $module;
        } else {
            throw new \InvalidArgumentException(__("Module name not provided"));
        }

        if ($name) {
            $response["name"] = $name;
        } else {
            throw new \InvalidArgumentException(__("name is required"));
        }

        if (isset($data['plugin']) && $data['plugin']) {
            $response["plugin-type"] = ltrim($data['plugin'], '\\');
        } else {
            throw new \InvalidArgumentException(__("plugin is required"));
        }

        $dir = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get(\Magento\Framework\Module\Dir::class);

        $modulePath = $dir->getDir($module);
        $response["path"] = $modulePath;
        if (isset($data['area']) && $data['area']) {
            $response["area"] = $data['area'];
        } else {
            $response["area"] = null;
        }
        $response["type"] = $type;
        
        return $response;
    }
}