<?php

namespace Experius\Postcode\Test\Integration;

use Experius\Postcode\Helper\Data;
use Experius\Postcode\Model\PostcodeManagement;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Reader as DeploymentConfigReader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class PostcodeTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    private $moduleName = 'Experius_Postcode';

    protected function setUp(): void
    {
        $this->objectManager = Bootstrap::getObjectManager();
    }

    public function testModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $this->assertArrayHasKey($this->moduleName, $registrar->getPaths(ComponentRegistrar::MODULE));
    }

    public function testModuleIsConfiguredAndEnabledInTestEnvironment()
    {
        $moduleList = $this->objectManager->create(ModuleList::class);
        $this->assertTrue($moduleList->has($this->moduleName), 'The module is not enabled in the Test env');
    }

    public function testModuleIsConfiguredAndEnabled()
    {
        $dirList = $this->objectManager->create(DirectoryList::class, ['root' => BP]);
        $configReader = $this->objectManager->create(DeploymentConfigReader::class, ['dirList' => $dirList]);
        $deploymentConfig = $this->objectManager->create(DeploymentConfig::class, ['reader' => $configReader]);
        $moduleList = $this->objectManager->create(ModuleList::class, ['config' => $deploymentConfig]);

        $this->assertTrue($moduleList->has($this->moduleName), 'The module is not enabled in the real env');
    }

    public function testGetPostcodeInformation()
    {
        $addressData = \GuzzleHttp\json_decode('{"street":"Teststraat","streetNen":"Teststraat","houseNumber":12,"houseNumberAddition":null,"postcode":"1234AB","city":"Utrecht","cityShort":"Utrecht","municipality":"Utrecht","municipalityShort":"Utrecht","province":"Utrecht","rdX":90000,"rdY":435000,"latitude":51.90500000,"longitude":4.45000000,"bagNumberDesignationId":"0599200100026629","bagAddressableObjectId":"0000000000000","addressType":"building","purposes":["residency"],"surfaceArea":100,"houseNumberAdditions":[]}', true);

        $postcodeHelper = $this->createPartialMock(Data::class, ['callApiUrlGet', 'getStoreConfig', 'isDebugging', 'checkApiReady']);
        $postcodeHelper->method('callApiUrlGet')->willReturn($addressData);
        $postcodeHelper->method('getStoreConfig')->willReturn(false);
        $postcodeHelper->method('isDebugging')->willReturn(false);
        $postcodeHelper->method('processErrorMessage')->willReturn($addressData);
        $postcodeHelper->method('checkApiReady')->willReturn(false);

        $postcodeManagement = new PostcodeManagement($postcodeHelper);
        $postcodeInformation = $postcodeManagement->getPostCodeInformation('1234AB', '12', '');

        $this->assertJson($postcodeInformation);

        $postcodeInformation = \GuzzleHttp\json_decode($postcodeInformation, true);

        $this->assertEquals('Teststraat', $postcodeInformation['street']);
        $this->assertEquals('Utrecht', $postcodeInformation['city']);
        $this->assertEquals(12, $postcodeInformation['houseNumber']);
        $this->assertEquals(null, $postcodeInformation['houseNumberAddition']);
        $this->assertEquals('1234AB', $postcodeInformation['postcode']);
    }
}
