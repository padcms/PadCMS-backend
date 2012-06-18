<?php
/**
 * @file
 * AM_Cli_Task_Phpunitall class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Runs all phpunit test
 * @ingroup AM_Cli
 */
class AM_Cli_Task_Phpunitall extends AM_Cli_Task_Abstract
{
    /** @var string */
    protected $_sPathToXml   = null; /**< @type string */
    /** @var string */
    protected $_sPathToTests = null; /**< @type string */

    protected function _configure()
    {
        $this->_sPathToXml   = $this->getConfig()->temp->base . DIRECTORY_SEPARATOR . 'test';
        $this->_sPathToTests = $this->getConfig()->test->test_folder;
        $this->addOption('env', 'e', '=s', 'Set environment', 'test');
    }

    public function execute()
    {
        $this->_generateXmlSuitesForTests();

        $aTestFiles = AM_Tools_Finder::type('file')
                ->name('*.xml')
                ->in($this->_sPathToXml);

        foreach ($aTestFiles as $sTestFile) {
            AM_Tools_Standard::getInstance()->passthru('phpunit --configuration=' . $sTestFile);
        }
    }

    /**
     * Generation of test suites configuration
     */
    protected function _generateXmlSuitesForTests()
    {
        AM_Tools_Standard::getInstance()->mkdir($this->_sPathToXml, 0777);
        $this->_removeAllXmlSuites();

        $aFiles = array();
        $aFiles = $this->_getAllTestFiles();

        $this->_createXmlSuite($aFiles);
    }

    /**
     * Remove all xml suite files
     */
    protected function _removeAllXmlSuites()
    {
        AM_Tools_Standard::getInstance()->clearDir($this->_sPathToXml);
    }

    /**
     * Get all tests from dir
     *
     * @return array
     */
    protected function _getAllTestFiles()
    {
        $aFiles = AM_Tools_Finder::type('file')
            ->name('*Test.php')
            ->in($this->_sPathToTests);
        sort($aFiles);

        return (array) $aFiles;
    }

    /**
     * Creates xml suite
     *
     * @param array $aSuite
     * @param int $iSuiteNo
     */
    protected function _createXmlSuite($aSuite, $iSuiteNo = 1)
    {
        $rXmlSuiteFile = fopen($this->_sPathToXml . DIRECTORY_SEPARATOR . $iSuiteNo . '.xml', 'w');

        $oDomDocumentSuite  = new DOMDocument('1.0', 'iso-8859-1');
        $oDomElementPhpunit = $oDomDocumentSuite->createElement('phpunit');

        $this->_includeBootstrapToDom($oDomElementPhpunit);

        $oDomDocumentSuite->appendChild($oDomElementPhpunit);
        $oDomElementTestsute = $oDomDocumentSuite->createElement('testsuite');
        $oDomElementTestsute->setAttribute('name', 'PHPUnit');
        $oDomElementPhpunit->appendChild($oDomElementTestsute);

        foreach ($aSuite as $sTestFile) {
            $oDomElementFile = $oDomDocumentSuite->createElement('file', $sTestFile);
            $oDomElementTestsute->appendChild($oDomElementFile);
        }

        fwrite($rXmlSuiteFile, $oDomDocumentSuite->saveXML());
        fclose($rXmlSuiteFile);
    }

    /**
     * @param DOMElement $dom
     */
    protected function _includeBootstrapToDom(DOMElement $dom)
    {
        $dom->setAttribute('bootstrap', $this->_sPathToTests . DIRECTORY_SEPARATOR . 'bootstrap.php');
    }
}
