<?php

namespace BertW\RunescapeHiscoresApi\Tests;

use BertW\RunescapeHiscoresApi\RS3Hiscores;

class RS3HiscoresTest extends HiscoresTest
{
    public function testTopPlayer()
    {
        $username = 'le me';

        $hiscores = $this->getHiscoresWithMockedResponse(RS3Hiscores::class, 'hiscores_rs3_max_skills.html');

        $player = $hiscores->player($username);

        $this->assertEquals($username, $player->username());

        $total = 0;

        $skill120 = ['Herblore', 'Slayer', 'Farming', 'Dungeoneering', 'Invention', 'Archaeology'];

        foreach($hiscores::SKILL_MAP as $i => $skill) {
            if($skill === 'Overall') {
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
