<?php
include '../../envi3/test/EnviCodeCoverage.php';

class EnviCodeCoverageTest
{
    function EnviCodeCoverageCreateTest()
    {
        $EnviCodeCoverage = EnviCodeCoverage::factory();
        $EnviCodeCoverage->start();
        include_once dirname(__FILE__).'/EnviParseSample.php';
        $EnviParseSample = new EnviParseSample;
        $EnviParseSample->andTest(false, false);
        $EnviCodeCoverage->finish();
        $EnviCodeCoverage->start();
        $EnviParseSample->andTest(false, true);
        $EnviCodeCoverage->finish();
        $EnviCodeCoverage->start();
        $EnviParseSample->andTest(true, false);
        $EnviCodeCoverage->finish();
        var_dump($EnviCodeCoverage->getCodeCoverage());
    }
}


$res = new EnviCodeCoverageTest;
$res->EnviCodeCoverageCreateTest();
