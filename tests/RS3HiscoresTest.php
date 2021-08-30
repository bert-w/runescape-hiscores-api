<?php

namespace BertW\RunescapeHiscoresApi\Tests;

use BertW\RunescapeHiscoresApi\RS3Hiscores;
use PHPUnit\Framework\TestCase;

class RS3HiscoresTest extends TestCase
{
    public function testTopPlayer()
    {
        $username = 'le me';

        $hiscores = new RS3Hiscores([
            'headers' => [
                // Use Chrome user-agent so the Hiscores page doesn't return an error.
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.131 Safari/537.36',
            ],
        ]);

        $player = $hiscores->player($username);

        $this->assertEquals($username, $player->username());

        $this->assertIsNumeric($player->get('attack')->level);

        $this->assertIsNumeric($player->get('defence')->level);

        $this->assertIsNumeric($player->get('strength')->level);

        $this->assertIsNumeric($player->combatLevel());

        $this->assertIsNumeric($player->totalLevel());
    }
}
