<?php

namespace BertW\RunescapeHiscoresApi\Tests;

use BertW\RunescapeHiscoresApi\OSRSHiscores;
use BertW\RunescapeHiscoresApi\RS3Hiscores;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class RS3HiscoresTest extends TestCase
{
    public function testTopPlayer()
    {
        $username = 'le me';

        $hiscores = \Mockery::mock(RS3Hiscores::class);

        $hiscores->shouldAllowMockingProtectedMethods()
            ->makePartial()
            ->shouldReceive('request')
            ->andReturn(new Response(200, [], file_get_contents(__DIR__ . '/mocks/hiscores_rs3_le_me.html')));

        $player = $hiscores->player($username);

        $this->assertEquals($username, $player->username());

        $total = 0;

        $skill120 = ['Herblore', 'Slayer', 'Farming', 'Dungeoneering', 'Invention', 'Archaeology'];

        foreach($hiscores->skillMap() as $i => $skill) {
            if ($skill === 'Overall') {
                // Overall is a cumulative and not a skill.
                continue;
            }
            $level = $player->get($skill)->level;

            $this->assertEquals(in_array($skill, $skill120) ? 120 : 99, $level, 'Invalid for ' . $skill);

            $total += $level;
        }

        $this->assertEquals($total, $player->totalLevel());
    }
}
