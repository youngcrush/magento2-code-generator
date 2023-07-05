<?php

namespace Amit\CodeGenerator\Model\Generate;

use Amit\CodeGenerator\Model\Helper;
use Amit\CodeGenerator\Api\GenerateInterface;
use Amit\CodeGenerator\Model\XmlGeneratorFactory;
use Magento\Framework\Simplexml\Config;

/**
 * Generate Logger
 */
class Logger implements GenerateInterface
{
    /**
     * @var Helper
     */
    protected $helper;
    
    /**
     * @var XmlGeneratorFactory
     */
    protected $xmlGenerator;

    /**
     * Constructor
     *
     * @param XmlGeneratorFactory $xmlGeneratorFactory
     * @param Helper $helper
     */
    public function __construct(
        XmlGeneratorFactory $xmlGeneratorFactory,
        Helper $helper
    ) {
        $this->helper = $helper;
        $this->xmlGenerator = $xmlGeneratorFactory->create();
    }

    /**
     * @inheritDoc
     */
    public function execute($data)
    {
        $path = $data['path'];
        
        $this->helper->createDirectory(
            $loggerDirPath = $path.DIRECTORY_SEPARATOR.'Logger'
        );
        
        $this->helper->createDirectory(
            $etcDirPath = $path.DIRECTORY_SEPARATOR.'etc'
        );
        
        $this->createLoggerClass($loggerDirPath, $data);
        $this->createHandlerClass($loggerDirPath, $data);
        $this->addDiXmlData($etcDirPath, $data);
       
        return ['status' => 'success', 'message' => "Logger Generated Successfully"];
    }

    /**
     * Create Logger class
     *
     * @param string $dir
     * @param array $data
     * @return void
     */
    public function createLoggerClass($dir, $data)
    {
        $nameSpace = $data['module'];
        $nameArray = explode("_", $nameSpace);
        $loggerFile = $this->helper->getTemplatesFiles('templates/logger/logger.php.dist');
        $loggerFile = str_replace('%module_name%', $data['module'], $loggerFile);
        $loggerFile = str_replace('%namespace%', $nameArray[0].'\\'.$nameArray[1], $loggerFile);
        
        $this->helper->saveFile(
            $dir.DIRECTORY_SEPARATOR.'Logger.php',
            $loggerFile
        );
    }

    /**
     * Create Handler class
     *
     * @param string $dir
     * @param array $data
     * @return void
     */
    public function createHandlerClass($dir, $data)
    {
        $nameSpace = $data['module'];
        $nameArray = explode("_", $nameSpace);
        $handlerFile = $this->helper->getTemplatesFiles('templates/logger/handler.php.dist');
        $handlerFile = str_replace('%module_name%', $data['module'], $handlerFile);
        $handlerFile = str_replace('%namespace%', $nameArray[0].'\\'.$nameArray[1], $handlerFile);
        $handlerFile = str_replace('%log_file%', $data['name'], $handlerFile);
        
        $this->helper->saveFile(
            $dir.DIRECTORY_SEPARATOR.'Handler.php',
            $handlerFile
        );
    }

    /**
     * Add di xml data
     *
     * @param string $etcDirPath
     * @param array $data
     * @return void
     */
    public function addDiXmlData($etcDirPath, $data)
    {
        $moduleName = $data['module'];
        $data['logger-class'] = str_replace('_', '\\', $moduleName).'\\'.'Logger'.'\\'.'Logger';
        $data['handler-class'] = str_replace('_', '\\', $moduleName).'\\'.'Logger'.'\\'.'Handler';
        $data['log-handler'] = lcfirst(str_replace('_', '', $moduleName)).'LogHandler';
        $diXmlFile = $this->helper->getDiXmlFile($etcDirPath, $data);
        $xmlObj = new Config($diXmlFile);
        $diXml = $xmlObj->getNode();
        $typeNode = $this->xmlGenerator->addXmlNode(
            $diXml,
            'type',
            '',
            ['name'=>$data['handler-class']]
        );
        $argsNode = $this->xmlGenerator->addXmlNode($typeNode, 'arguments');
        $this->xmlGenerator->addXmlNode(
            $argsNode,
            'argument',
            \Magento\Framework\Filesystem\Driver\File::class,
            ['name'=>'filesystem', 'xsi:type'=>'object']
        );

        $typeNode = $this->xmlGenerator->addXmlNode($diXml, 'type', '', ['name'=>$data['logger-class']]);
        $argsNode = $this->xmlGenerator->addXmlNode($typeNode, 'arguments');
        $this->xmlGenerator->addXmlNode(
            $argsNode,
            'argument',
            $data['log-handler'],
            ['name'=>'name', 'xsi:type'=>'string']
        );
        $argNode = $this->xmlGenerator->addXmlNode(
            $argsNode,
            'argument',
            '',
            ['name'=>'handlers', 'xsi:type'=>'array']
        );
        $this->xmlGenerator->addXmlNode(
            $argNode,
            'item',
            $data['handler-class'],
            ['name'=>'system', 'xsi:type'=>'object']
        );
        $xmlData = $this->xmlGenerator->formatXml($diXml->asXml());
        $this->helper->saveFile($diXmlFile, $xmlData);
    }
}
