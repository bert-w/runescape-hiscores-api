<?php

namespace BertW\RunescapeHiscoresApi;

class RS3Player extends Player
{
    public const COMBAT_SKILLS = ['attack', 'strength', 'defence', 'hitpoints', 'ranged', 'prayer', 'magic'];

    /**
     * Calculate the player's combat level.
     * @return float
     */
    public function combatLevel()
    {
        // Retrieve all combat skills, using `1` where they are undefined.
        $attack = $this->get('attack')->level ?: 1;
        $strength = $this->get('strength')->level ?: 1;
        $defence = $this->get('defence')->level ?: 1;
        $constitution = $this->get('constitution')->level ?: 10;
        $ranged = $this->get('ranged')->level ?: 1;
        $prayer = $this->get('prayer')->level ?: 1;
        $magic = $this->get('magic')->level ?: 1;
        $summoning = $this->get('summoning')->level ?: 1;

        $calculation = 13 / 10 * max($attack + $strength, 2 * $magic, 2 * $ranged)
            + $defence + $constitution + floor(0.5 * $prayer) + floor(0.5 * $summoning);

        return $calculation / 4;
    }
}
