<?php
/**
 * @file
 * AM_Tools_Profiler class definition.
 *
 * LICENSE
 *
 * $DOXY_LICENSE
 *
 * @author $DOXY_AUTHOR
 * @version $DOXY_VERSION
 */

/**
 * @ingroup AM_Tools
 */
class AM_Tools_Profiler
{
    protected $_mTimeStart  = null;
    protected $_mTimeFinish = null;

    /**
     * Start counter
     */
    public function start()
    {
        $this->_mTimeStart = microtime(true);
    }

    /**
     * Finish counter
     */
    public function finish()
    {
        $this->_mTimeFinish = microtime(true);
    }

    /**
     * Returns execution time in human readable format
     * @return string
     */
    public function getExecutionTime()
    {
        return sprintf('%03.4f Seconds', $this->_mTimeFinish - $this->_mTimeStart);
    }
}