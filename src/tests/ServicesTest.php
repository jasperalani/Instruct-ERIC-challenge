<?php declare(strict_types=1);
use PHPUnit\Framework\TestCase;

final class ServicesTest extends TestCase
{
    function prepareTests(): void
    {
        // Delete services.csv file
        unlink("../services.csv");
        // Create new services.csv file so we know what we are testing on
        file_put_contents("../services.csv", "Ref,Centre,Service,Country
        APPLAB1,Aperture Science,Portal Technology,fr
        BLULAB1,Blue Sun Corp,Behaviour Modification,FR
        BMELAB1,Black Mesa,Interdimensional Travel,de
        WEYLAB1,Weyland Yutani Research,Xeno-biology,gb
        BLULAB3,Blue Sun R&D,Behaviour Modification,cz
        BMELAB2,Black Mesa Second Site,Interdimensional Travel,DE
        TYRLAB1,Tyrell Research,Synthetic Consciousness,GB
        BLULAB2,Blue Sun Corp,Behaviour Modification,it
        TYRLAB2,Tyrell Research,Synthetic Optics,pt");
    }

    public function testQueryEmpty(): void
    {
        $this->prepareTests();

        $output = '';
        exec('php ../services.php query', $output);

        $expected_output = "If you want to query please provide the word \"query\" as the first argument and the country code you would like to query as the second argument. Please note the country code is not case-specific.
        Example: php services.php query gb";

        $this->assertSame($output, $expected_output);
    }

    public function testQueryCorrect(): void
    {
        $this->prepareTests();

        $output = '';
        exec('php ../services.php query gb', $output);

        $expected_output = "Query found 2 results for supplied country code: gb

        Ref: WEYLAB1
        Centre: Weyland Yutani Research
        Service: Xeno-biology
        
        Ref: TYRLAB1
        Centre: Tyrell Research
        Service: Synthetic Consciousness";

        $this->assertSame($output, $expected_output);
    }

    public function testQueryNotFound(): void
    {
        $this->prepareTests();

        $output = '';
        exec('php ../services.php query aa', $output);

        $expected_output = "No data matched the supplied country code: aa";

        $this->assertSame($output, $expected_output);
    }

    public function testSummary(): void
    {
        $this->prepareTests();

        $output = '';
        exec('php ../services.php summary', $output);

        $expected_output = "Summary:
        Country code fr has 2 related services.
        Country code de has 2 related services.
        Country code gb has 2 related services.
        Country code cz has 1 related service.
        Country code it has 1 related service.
        Country code pt has 1 related service.";

        $this->assertSame($output, $expected_output);
    }
}