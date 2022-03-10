<?php

namespace BertW\RunescapeHiscoresApi\Tests;

use BertW\RunescapeHiscoresApi\Exception\PlayerNotFoundException;
use BertW\RunescapeHiscoresApi\OSRSHiscores;

class OSRSHiscoresTest extends HiscoresTest
{
    public function testMaxSkills()
    {
        $username = 'Lynx Titan';

        $hiscores = $this->getHiscoresWithMockedResponse(OSRSHiscores::class, 'hiscores_osrs_max_skills.html');

        $player = $hiscores->player($username);

        $this->assertEquals($username, $player->username());

        $total = 0;

        foreach($hiscores::SKILL_MAP as $i => $skill) {
            if($skill === 'Overall') {
                // Overall is a cumulative and not a skill.
                continue;
            }
            $level = $player->get($skill)->level;

            $this->assertEquals(99, $level, 'Invalid for ' . $skill);

            $total += $level;
        }

        $this->assertEquals($total, $player->totalLevel());

        $this->assertEquals(3, count($player->minigames()));

        $this->assertEquals(24, count($player->skills()));

        $this->assertEquals(27, count($player->hiscores()));
    }

    public function testIncompleteHiscoresWithOneSkill()
    {
        $hiscores = $this->getHiscoresWithMockedResponse(OSRSHiscores::class, 'hiscores_osrs_one_skill.html');

        $player = $hiscores->player('Example');

        // Non-existing hiscores should have level `null`.
        $this->assertEquals(null, $player->get('Firemaking')->level);

        $this->assertEquals(1, count($player->skills()));

        $this->assertEquals(0, count($player->minigames()));

        // Check if missing skills are added to the total as level 1.
        $this->assertEquals(22 + 45, $player->totalLevel());
    }

    public function testIncompleteHiscoresWithSomeSkillsWithoutTotal()
    {
        $hiscores = $this->getHiscoresWithMockedResponse(OSRSHiscores::class, 'hiscores_osrs_some_skills_without_total.html');

        $player = $hiscores->player('Example');

        $this->assertEquals(7, count($player->skills()));

        $this->assertEquals(43, $player->get('Slayer')->level);

        // Check if missing skills are added to the total as level 1.
        $this->assertEquals(215, $player->totalLevel());
    }

    public function testIncompleteHiscoresWithSomeSkillsWithTotal()
    {
        $hiscores = $this->getHiscoresWithMockedResponse(OSRSHiscores::class, 'hiscores_osrs_some_skills_with_total.html');

        $player = $hiscores->player('Example');

        $this->assertEquals(6, count($player->skills()));

        $this->assertEquals(709, $player->totalLevel());
    }

    public function testNoHiscoresFound()
    {
        $hiscores = $this->getHiscoresWithMockedResponse(OSRSHiscores::class, 'hiscores_osrs_not_found.html');

        $this->expectException(PlayerNotFoundException::class);

        $player = $hiscores->player('Example');
    }
}
