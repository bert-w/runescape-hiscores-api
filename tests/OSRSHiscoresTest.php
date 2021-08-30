<?php

namespace BertW\RunescapeHiscoresApi\Tests;

use BertW\RunescapeHiscoresApi\OSRSHiscores;
use PHPUnit\Framework\TestCase;

class OSRSHiscoresTest extends TestCase
{
    public function testTopPlayer()
    {
        $username = 'Lynx Titan';

        $hiscores = new OSRSHiscores([
            'headers' => [
                // Use Chrome user-agent so the Hiscores page doesn't return an error.
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.131 Safari/537.36',
            ],
        ]);

        $player = $hiscores->player($username);

        $this->assertEquals($username, $player->username());

        $this->assertEquals(99, $player->get('attack')->level);

        $this->assertEquals(99, $player->get('defence')->level);

        $this->assertEquals(99, $player->get('strength')->level);
    }
}
