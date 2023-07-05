<?php

namespace Amit\CodeGenerator\Model\Generate;

use Amit\CodeGenerator\Api\GenerateInterface;
use Amit\CodeGenerator\Model\Helper;
use Magento\Framework\Module\StatusFactory;

/**
 * Generate NewModule.php
 */
class NewModule implements GenerateInterface
{
    public const MODULE_PATH = 'app/code/';

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Module\Status
     */
    protected $moduleStatus;

    /**
     * Construct
     *
     * @param Helper $helper
     * @param StatusFactory $moduleStatusFactory
     */
    public function __construct(
        Helper $helper,
        StatusFactory $moduleStatusFactory
    ) {
        $this->helper = $helper;
        $this->moduleStatus = $moduleStatusFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function execute($data)
    {
        $moduleName = $data['module'];
        $preparedModuleName = str_replace('_', '/', $moduleName);
        $moduleDir = $this->getModuleBasePath().'/'.$preparedModuleName;
        // @codingStandardsIgnoreStart
        if (!is_dir($moduleDir)) {
            mkdir($moduleDir, 0777, true);
        }
        // @codingStandardsIgnoreEnd
        $this->createModuleXmlFile($moduleDir, $moduleName);
        $this->createRegistrationFile($moduleDir, $moduleName);
        $this->createComposerFile($moduleDir, $moduleName);
        \Magento\Framework\Component\ComponentRegistrar::register('module', $moduleName, $moduleDir);
        $this->moduleStatus->setIsEnabled(true, [$moduleName]);
        return ['status' => 'success', 'message' => "new module generated successfully"];
    }

    /**
     * Create module.xml
     *
     * @param string $moduleDir
     * @param string $moduleName
     * @return void
     */
    private function createModuleXmlFile($moduleDir, $moduleName)
    {
        $moduleXmlTemplate = $this->getModuleXmlTemplate();
        $moduleXmlTemplate = str_replace('%moduleName%', $moduleName, $moduleXmlTemplate);
        $moduleEtcDir = $moduleDir.'/etc';
        // @codingStandardsIgnoreStart
        if (!is_dir($moduleEtcDir)) {
            mkdir($moduleEtcDir, 0777, true);
        }
        $moduleXmlFile = $moduleEtcDir . '/module.xml';
        file_put_contents($moduleXmlFile, $moduleXmlTemplate);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Create registration.php
     *
     * @param string $moduleDir
     * @param string $moduleName
     * @return void
     */
    private function createRegistrationFile($moduleDir, $moduleName)
    {
        $registrationTemplate = $this->getRegistrationTemplate();
        $registrationTemplate = str_replace('%moduleName%', $moduleName, $registrationTemplate);
        $registrationFile = $moduleDir . '/registration.php';
        // @codingStandardsIgnoreStart
        file_put_contents($registrationFile, $registrationTemplate);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Create composer.json
     *
     * @param string $moduleDir
     * @param string $moduleName
     * @return void
     */
    private function createComposerFile($moduleDir, $moduleName)
    {
        $composerModuleName = explode('_', $moduleName);
        $moduleComposerTemplate = $this->getModuleComposerTemplate();

        $moduleComposerTemplate = str_replace(
            '%moduleName%',
            $composerModuleName[0].'\\\\'.$composerModuleName[1].'\\\\',
            $moduleComposerTemplate
        );
        $moduleComposerTemplate = str_replace(
            '%vendor%',
            strtolower($composerModuleName[0]),
            $moduleComposerTemplate
        );
        $moduleComposerTemplate = str_replace(
            '%composerName%',
            strtolower($composerModuleName[1]),
            $moduleComposerTemplate
        );

        $composerFile = $moduleDir . '/composer.json';
        // @codingStandardsIgnoreStart
        file_put_contents($composerFile, $moduleComposerTemplate);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Return base path
     *
     * @return string
     */
    public function getModuleBasePath() : string
    {
        return BP.'/'.self::MODULE_PATH;
    }

    /**
     * Get module.xml template
     *
     * @return string
     */
    protected function getModuleXmlTemplate() : string
    {
        // @codingStandardsIgnoreStart
        return file_get_contents(dirname(dirname( dirname(__FILE__) )) . '/templates/module.xml.dist');
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get registration.php template
     *
     * @return string
     */
    protected function getRegistrationTemplate() : string
    {
        // @codingStandardsIgnoreStart
        return file_get_contents(dirname(dirname( dirname(__FILE__) )) . '/templates/registration.php.dist');
        // @codingStandardsIgnoreEnd
    }

    /**
     * Get registration.php template
     *
     * @return string
     */
    protected function getModuleComposerTemplate() : string
    {
        // @codingStandardsIgnoreStart
        return file_get_contents(dirname(dirname( dirname(__FILE__) )) . '/templates/composer.json.dist');
        // @codingStandardsIgnoreEnd
    }
}
