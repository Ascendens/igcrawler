<?php

namespace Ascendens\Igcrawler\Test\Report;

use PHPUnit_Framework_TestCase;
use Ascendens\Igcrawler\Report\CrawlingTableReportGenerator;

class CrawlingTableReportGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function testDataCollecting()
    {
        $reportGenerator = new CrawlingTableReportGenerator(['id' => 'ID']);

        $reportGenerator->add(['id' => 1, 'name' => 'test']);
        $this->assertEquals([
            ['id' => 1, 'name' => 'test']
        ], $reportGenerator->getData()->getArrayCopy());

        $reportGenerator->add(['id' => 2]);
        $this->assertEquals([
            ['id' => 1, 'name' => 'test'],
            ['id' => 2]
        ], $reportGenerator->getData()->getArrayCopy());
    }

    public function testGenerate()
    {
        $reportGenerator = new CrawlingTableReportGenerator(['id' => 'ID', 'code' => 'Code']);
        $reportGenerator->add(['id' => 1, 'code' => 100]);
        $reportGenerator->add(['id' => 2, 'code' => 200]);
        $reportGenerator->add(['id' => 3, 'code' => 300, 'extra' => 'Some']);
        $report = $reportGenerator->generate();
        $expected = '<table >';
        $expected .= '<tr><th>ID</th><th>Code</th></tr>';
        $expected .= '<tr><td>1</td><td>100</td></tr>';
        $expected .= '<tr><td>2</td><td>200</td></tr>';
        $expected .= '<tr><td>3</td><td>300</td></tr>';
        $expected .= '</table>';
        $this->assertEquals($expected, $report);
    }

    public function testGenerateWithAscSorting()
    {
        $reportGenerator = new CrawlingTableReportGenerator(['id' => 'ID', 'code' => 'Code'], 'id');
        $reportGenerator->add(['id' => 15, 'code' => 300]);
        $reportGenerator->add(['id' => 5, 'code' => 100]);
        $reportGenerator->add(['id' => 10, 'code' => 200]);
        $report = $reportGenerator->generate();
        $expected = '<table >';
        $expected .= '<tr><th>ID</th><th>Code</th></tr>';
        $expected .= '<tr><td>5</td><td>100</td></tr>';
        $expected .= '<tr><td>10</td><td>200</td></tr>';
        $expected .= '<tr><td>15</td><td>300</td></tr>';
        $expected .= '</table>';
        $this->assertEquals($expected, $report);
    }

    public function testGenerateWithDescSorting()
    {
        $reportGenerator = new CrawlingTableReportGenerator(
            ['id' => 'ID', 'code' => 'Code'],
            'id',
            CrawlingTableReportGenerator::SORT_DESC,
            'border="1"'
        );
        $reportGenerator->add(['id' => 15, 'code' => 300]);
        $reportGenerator->add(['id' => 5, 'code' => 100]);
        $reportGenerator->add(['id' => 10, 'code' => 200]);
        $report = $reportGenerator->generate();
        $expected = '<table border="1">';
        $expected .= '<tr><th>ID</th><th>Code</th></tr>';
        $expected .= '<tr><td>15</td><td>300</td></tr>';
        $expected .= '<tr><td>10</td><td>200</td></tr>';
        $expected .= '<tr><td>5</td><td>100</td></tr>';
        $expected .= '</table>';
        $this->assertEquals($expected, $report);
    }

    public function testGenerateWithTemplate()
    {
        $reportGenerator = new CrawlingTableReportGenerator(['id' => 'ID', 'code' => 'Code', 'extra' => 'Extra']);
        $reportGenerator->add(['id' => 1, 'code' => 100]);
        $reportGenerator->add(['id' => 2, 'code' => 200]);
        $reportGenerator->add(['id' => 3, 'code' => 300, 'extra' => 'Some']);
        $report = $reportGenerator->generate(['template' => '<div></div><div>{body}</div>']);
        $expected = '<div></div><div><table >';
        $expected .= '<tr><th>ID</th><th>Code</th><th>Extra</th></tr>';
        $expected .= '<tr><td>1</td><td>100</td><td></td></tr>';
        $expected .= '<tr><td>2</td><td>200</td><td></td></tr>';
        $expected .= '<tr><td>3</td><td>300</td><td>Some</td></tr>';
        $expected .= '</table></div>';
        $this->assertEquals($expected, $report);
    }
}
