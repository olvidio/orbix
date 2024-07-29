<?php

namespace unit\notas;

use PHPUnit\Framework\TestCase;

class notasTest extends TestCase
{

    /*
    public function getDataSet()
    {

        return $this->createFlatXmlDataSet('./tests/guestbook_fixture.xml');
    }
    */

    /*
    public function testRowCount()
    {

        $this->assertSame(2, $this->getConnection()->getRowCount('guestbook'), 'Pre-Condition');
    }
    */

    /*
    public function testAddGuest()
    {

        // get the class to be tested, providing the PDObject as database connection
        $guestbook = new Guestbook($this->pdo);

        // insert a new guest
        $guestbook->addGuest('Daniel', 'St Kilda, Scotland', '4545');

        // get the resulting table from our database
        $queryTable = $this->getConnection()->createQueryTable(
            'guestbook', 'SELECT id, name, address, phone FROM guestbook'
        );

        // get the table we would expect after inserting a new guest
        $expectedTable = $this->createFlatXmlDataSet('./tests/guestbook_expected.xml')->getTable('guestbook');

        // ...and compare both tables ...it passes!
        $this->assertTablesEqual($expectedTable, $queryTable, "New User Added");
    }
    */

    /*
    public function testFailingAddGuest()
    {

        // get the class to be tested, providing the PDObject as database connection
        $guestbook = new Guestbook($this->pdo);

        // insert a new guest, but omit the value for 'phone' to let the test fail
        $guestbook->addGuest('Daniel', 'St Kilda, Scotland', '');

        // get the resulting table from our database, changed by the Guestbook Class
        $queryTable = $this->getConnection()->createQueryTable(
            'guestbook', 'SELECT id, name, address, phone FROM guestbook'
        );

        // get the *expected* table from a flat XML dataset
        $expectedTable = $this->createFlatXmlDataSet('./tests/guestbook_expected.xml')->getTable('guestbook');

        // ...and compare both tables which will fail
        $this->assertTablesEqual($expectedTable, $queryTable, 'Failure On Purpose');
    }
    */
}