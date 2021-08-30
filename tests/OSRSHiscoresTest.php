<?php

namespace BertW\RunescapeHiscoresApi\Tests;

use BertW\RunescapeHiscoresApi\OSRSHiscores;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class OSRSHiscoresTest extends TestCase
{
    public function testTopPlayer()
    {
        $username = 'Lynx Titan';

        $hiscores = \Mockery::mock(OSRSHiscores::class);

        $hiscores->shouldAllowMockingProtectedMethods()
            ->makePartial()
            ->shouldReceive('request')
            ->andReturn(new Response(200, [], file_get_contents(__DIR__ . '/mocks/hiscores_osrs_lynx_titan.html')));

        $player = $hiscores->player($username);

        $this->assertEquals($username, $player->username());

        $total = 0;

        foreach($hiscores->skillMap() as $i => $skill) {
            if ($skill === 'Overall') {
                // Overall is a cumulative and not a skill.
                continue;
            }
            $level = $player->get($skill)->level;

            $this->assertEquals(99, $level, 'Invalid for ' . $skill);

            $total += $level;
        }

        $this->assertEquals($total, $player->totalLevel());
    }
}
