<?php

namespace BertW\RunescapeHiscoresApi\Tests;

use BertW\RunescapeHiscoresApi\OSRSHiscores;
use BertW\RunescapeHiscoresApi\RS3Hiscores;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class HiscoresTest extends TestCase
{
    /**
     * Create a Hiscores class where the html response is predetermined by a given html file.
     * @param string $classname
     * @param string $filename html file from the tests/mocks directory
     * @return OSRSHiscores
     */
    protected function getHiscoresWithMockedResponse($classname, $filename)
    {
        $hiscores = \Mockery::mock($classname);

        $hiscores->shouldAllowMockingProtectedMethods()
            ->makePartial()
            ->shouldReceive('request')
            ->andReturn(new Response(200, [], file_get_contents(__DIR__ . '/mocks/' . $filename)));

        return $hiscores;
    }
}
