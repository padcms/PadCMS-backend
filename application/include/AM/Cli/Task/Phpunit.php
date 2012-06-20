<?php
/**
 * @file
 * AM_Cli_Task_Phpunit class definition.
 *
 * LICENSE
 *
 * This software is governed by the CeCILL-C  license under French law and
 * abiding by the rules of distribution of free software.  You can  use,
 * modify and/ or redistribute the software under the terms of the CeCILL-C
 * license as circulated by CEA, CNRS and INRIA at the following URL
 * "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and  rights to copy,
 * modify and redistribute granted by the license, users are provided only
 * with a limited warranty  and the software's author,  the holder of the
 * economic rights,  and the successive licensors  have only  limited
 * liability.
 *
 * In this respect, the user's attention is drawn to the risks associated
 * with loading,  using,  modifying and/or developing or reproducing the
 * software by the user in light of its specific status of free software,
 * that may mean  that it is complicated to manipulate,  and  that  also
 * therefore means  that it is reserved for developers  and  experienced
 * professionals having in-depth computer knowledge. Users are therefore
 * encouraged to load and test the software's suitability as regards their
 * requirements in conditions enabling the security of their systems and/or
 * data to be ensured and,  more generally, to use and operate it in the
 * same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL-C license and that you accept its terms.
 *
 * @author Copyright (c) PadCMS (http://www.padcms.net)
 * @version $DOXY_VERSION
 */

/**
 * Runs single phpunit test
 * @ingroup AM_Cli
 */
class AM_Cli_Task_Phpunit extends AM_Cli_Task_Abstract
{
    /** @var string */
    protected $_sPathToXml   = null; /**< @type string */
    /** @var string */
    protected $_sPathToTests = null; /**< @type string */

    protected function _configure()
    {
        $this->_sPathToXml   = $this->getConfig()->temp->base . DIRECTORY_SEPARATOR . 'test';
        $this->_sPathToTests = $this->getConfig()->test->test_folder;

        $this->addArgument("case", "=s");
        //Option filter with required string parameter
        $this->addOption("filter", "f", "=s", "Filter which tests to run");
        $this->addOption("env", "e", "=s", "Set environment", "test");
    }

    public function execute()
    {
        $this->_generateXmlSuitesForTest();

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
    protected function _generateXmlSuitesForTest()
    {
        AM_Tools_Standard::getInstance()->mkdir($this->_sPathToXml, 0777);
        $this->_removeAllXmlSuites();

        $aFiles = array();
        $aFiles = $this->_getTestFile();

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
     * @return string
     */
    protected function _getTestFile()
    {
        $sCaseName = $this->_getArgument('case');

        if (!$sCaseName) {
            throw new AM_Cli_Task_Exception('Please specify the test file name' . PHP_EOL . PHP_EOL);
        }
        $sTestCaseFileName     = $sCaseName . '.php' ;
        $sTestCaseFileBasePath = Zend_Registry::get("config")->test->test_folder;
        $aTestCaseFilesPathes  = AM_Tools_Finder::type('file')->name($sTestCaseFileName)->in($sTestCaseFileBasePath);

        if (empty($aTestCaseFilesPathes)) {
            throw new AM_Cli_Task_Exception(sprintf('Test file "%s" is not found%s%s', $sTestCaseFileName, PHP_EOL, PHP_EOL));
        }

        return $aTestCaseFilesPathes;
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
