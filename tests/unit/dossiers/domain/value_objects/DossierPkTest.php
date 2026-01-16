<?php

namespace Tests\unit\dossiers\domain\value_objects;

use src\dossiers\domain\value_objects\DossierPk;
use Tests\myTest;

class DossierPkTest extends myTest
{
    public function test_create_valid_dossierPk()
    {
        $dossierPk = new DossierPk(1234, 5678,'p');
        $this->assertEquals('p:5678:1234', $dossierPk->__toString());
    }

    public function test_equals_returns_true_for_same_dossierPk()
    {
        $dossierPk1 = new DossierPk(1234, 5678,'p');
        $dossierPk2 = new DossierPk(1234, 5678,'p');
        $this->assertTrue($dossierPk1->equals($dossierPk2));
    }

    public function test_equals_returns_false_for_different_dossierPk()
    {
        $dossierPk1 = new DossierPk(1234,5678,'p');
        $dossierPk2 = new DossierPk(4563,1256,'a');
        $this->assertFalse($dossierPk1->equals($dossierPk2));
    }

}
