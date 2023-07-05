<?php
namespace Amit\CodeGenerator\Model\Generate;

use Amit\CodeGenerator\Model\Helper;
use Amit\CodeGenerator\Api\GenerateInterface;
use Amit\CodeGenerator\Model\XmlGeneratorFactory;
use Magento\Framework\Simplexml\Config;

/**
 * Generate Cron
 */
class Cron implements GenerateInterface
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
     * __construct function
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
        $moduleName = $data['module'];
        $path = $data['path'];
        $data['cron-name'] = strtolower($moduleName.'-'.$data['name'].'-'.'cron');
        $data['cron-class'] = str_replace('_', '\\', $moduleName).'\\'.'Cron'.'\\'.$data['name'];
        
        $this->helper->createDirectory(
            $cronDirPath = $path.DIRECTORY_SEPARATOR.'Cron'
        );
        
        $this->helper->createDirectory(
            $etcDirPath = $path.DIRECTORY_SEPARATOR.'etc'
        );
        
        $this->createCron($cronDirPath, $data);
        $this->addCrontabXmlData($etcDirPath, $data);
       
        return ['status' => 'success', 'message' => "Cron Class Generated Successfully"];
    }

    /**
     * Create cron class
     *
     * @param string $dir
     * @param array $data
     * @return void
     */
    public function createCron($dir, $data)
    {
        $fileName = ucfirst($data['name']);
        $nameSpace = $data['module'];
        $nameArray = explode("_", $nameSpace);
        $cronFile = $this->helper->getTemplatesFiles('templates/cron/cron.php.dist');
        $cronFile = str_replace('%module_name%', $data['module'], $cronFile);
        $cronFile = str_replace('%cron_name%', $fileName, $cronFile);
        $cronFile = str_replace('%namespace%', $nameArray[0].'\\'.$nameArray[1], $cronFile);
        
        $this->helper->saveFile(
            $dir.DIRECTORY_SEPARATOR.$fileName.'.php',
            $cronFile
        );
    }

    /**
     * Add crontab.xml data
     *
     * @param string $etcDirPath
     * @param array $data
     * @return void
     */
    public function addCrontabXmlData($etcDirPath, $data)
    {
        $schedule = $data['schedule'];
        $cronName = $data['cron-name'];
        $cronClass = $data['cron-class'];
        $replace = [
            "module_name" => $data['module']
        ];
        $crontabXmlFile = $this->helper->loadTemplateFile(
            $etcDirPath,
            'crontab.xml',
            'templates/crontab.xml.dist',
            $replace
        );
        $xmlObj = new Config($crontabXmlFile);
        $configXml = $xmlObj->getNode();
        if (!$configXml->group) {
            throw new \RuntimeException(
                __('Incorrect crontab.xml schema found')
            );
        }
        $jobNode = $this->xmlGenerator->addXmlNode(
            $configXml->group,
            'job',
            '',
            ['instance'=>$cronClass, 'method'=>'execute', 'name'=>$cronName]
        );
        $this->xmlGenerator->addXmlNode($jobNode, 'schedule', $schedule);
        $xmlData = $this->xmlGenerator->formatXml($configXml->asXml());
        $this->helper->saveFile($crontabXmlFile, $xmlData);
    }
}
