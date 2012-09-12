<?php
/**
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

class FactoryTest extends PHPUnit_Framework_TestCase
{
    protected $_oStandardMock = null;

    public function setUp()
    {
        $this->_oStandardMock = $this->getMock('AM_Tools_Standard', array('is_file'));
    }

    public function testShouldCreateSourceInstance()
    {
        //GIVEN
        $sSourceFile = 'source_file.mock';

        //WHEN
        $this->_oStandardMock->expects($this->any())
             ->method('is_file')
             ->with($this->equalTo($sSourceFile))
             ->will($this->returnValue(true));

        $oSource = AM_Resource_Factory::create($sSourceFile);

        $this->assertTrue($oSource instanceof AM_Resource_Concrete_Mock);
    }

    public function testShouldThrowExceptionWhenFileNotFound()
    {
        //GIVEN
        $sSourceFile = 'source_file.mock';

        //WHEN
        $this->_oStandardMock->expects($this->any())
             ->method('is_file')
             ->with($this->equalTo($sSourceFile))
             ->will($this->returnValue(false));

        //THEN
        $this->setExpectedException('AM_Resource_Factory_Exception', '', 501);

        //WHEN
        $oSource = AM_Resource_Factory::create($sSourceFile);
    }

    public function testShouldThrowExceptionWhenSourceClassNotFound()
    {
        //GIVEN
        $sSourceFile = 'source_file.fail';

        //WHEN
        $this->_oStandardMock->expects($this->any())
             ->method('is_file')
             ->with($this->equalTo($sSourceFile))
             ->will($this->returnValue(true));

        //THEN
        $this->setExpectedException('AM_Resource_Factory_Exception', '', 502);

        //WHEN
        $oSource = AM_Resource_Factory::create($sSourceFile);
    }
}