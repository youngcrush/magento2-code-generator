<?php
namespace Amit\CodeGenerator\Model\Generate\Cron;

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

        if (isset($data['schedule']) && $data['schedule']) {
            $response["schedule"] = $data['schedule'];
        } else {
            $response["schedule"] = '0 1 * * *';
        }

        $dir = \Magento\Framework\App\ObjectManager::getInstance()
                    ->get(\Magento\Framework\Module\Dir::class);

        $modulePath = $dir->getDir($module);
        $response["path"] = $modulePath;
        $response["type"] = $type;
        
        return $response;
    }
}
