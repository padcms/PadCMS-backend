<?php
/**
 * @file
 * AM_Cli_Task_Staticpdf class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * Generates horisontal pngs from given pdfs
 * @ingroup AM_Cli
 */
class AM_Cli_Task_Staticpdf extends AM_Cli_Task_Abstract
{
    protected function _configure()
    {
        $this->addOption('issue', 'i', '=i', 'Issue ID');

        AM_Handler_Locator::getInstance()
                ->setHandler('horisontal_pdf', 'AM_Handler_HorisontalPdf');
    }

    public function execute()
    {
        $iIssueId = $this->_getOption('issue');

        $aIssues = array();

        if (!empty($iIssueId)) {
            $aIssues[] = AM_Model_Db_Table_Abstract::factory('issue')->findOneBy('id', $iIssueId);
        } else {
            $aIssues = AM_Model_Db_Table_Abstract::factory('issue')->fetchAll(array('deleted = ?'=> 'no'));
        }

        foreach ($aIssues as $oIssue) {
            /* @var $oIssue AM_Model_Db_Issue */
            $this->getLogger()->info(sprintf('Compilling static pdf for issue "%s"...', $oIssue->id));
            $oIssue->compileHorizontalPdfs();
            $this->getLogger()->info(sprintf('Done'));
        }
    }
}