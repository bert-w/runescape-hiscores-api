<?php

namespace BertW\RunescapeHiscoresApi;

class Player
{
    /** @var HiscoreRow[] */
    protected $hiscores;

    /**
     * This indicates whether an explicit total level is defined or not.
     * If this value is true (no total level), the `totalLevel()` function calculates a minimum from the skills.
     * @var bool
     */
    public $noTotalLevel;

    /**
     * When this property is true, the combat level couldn't be completely calculated, because
     * one or more of the combat skills were not in the hiscores. For those values, `1` is used,
     * resulting in a possibly lower combat level than is the case.
     * @var bool
     */
    public $incompleteCombatLevel;

    CONST COMBAT_SKILLS = ['attack', 'strength', 'defence', 'hitpoints', 'ranged', 'prayer', 'magic'];

    /**
     * @param HiscoreRow[] $hiscores
     */
    public function __construct($hiscores = [])
    {
        $this->hiscores = $hiscores;

        if(is_null($this->get('overall')->level)) {
            $this->noTotalLevel = true;
        }

        foreach(static::COMBAT_SKILLS as $skill) {
            if(is_null($this->get($skill)->level)) {
                $this->incompleteCombatLevel = true;
                break;
            }
        }
    }

    /**
     * Get a hiscore record by name (the exact hiscores page spelling),
     * i.e. 'agility', 'herblore', 'clue scrolls (all)', 'runecraft', 'defence', 'overall' (total).
     * @param string $name
     * @return HiscoreRow
     */
    public function get($name)
    {
        $name = strtolower($name);
        foreach($this->hiscores as $hiscore) {
            if(strtolower($hiscore->name) === $name) {
                return $hiscore;
            }
        }
        return new HiscoreRow();
    }

    /**
     * @return HiscoreRow[]
     */
    public function skills()
    {
        return array_filter($this->hiscores, function(HiscoreRow $hiscore) {
            return $hiscore->type === HiscoreRow::SKILL;
        });
    }

    /**
     * @return HiscoreRow[]
     */
    public function minigames()
    {
        return array_filter($this->hiscores, function(HiscoreRow $hiscore) {
            return $hiscore->type === HiscoreRow::MINIGAME;
        });
    }

    /**
     * Retrieve this player's total level, or the sum of the users' hiscores if there isn't any.
     * @return int
     */
    public function totalLevel()
    {
        if($this->noTotalLevel) {
            $sum = 0;
            foreach($this->skills() as $skill) {
                $sum += $skill->level;
            }
            return $sum;
        }
        return $this->get('overall')->level;
    }

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
        $hitpoints = $this->get('hitpoints')->level ?: 10;
        $ranged = $this->get('ranged')->level ?: 1;
        $prayer = $this->get('prayer')->level ?: 1;
        $magic = $this->get('magic')->level ?: 1;

        $calculation = 13 / 10 * max($attack + $strength, 2 * $magic, 2 * $ranged)
            + $defence + $hitpoints + floor(0.5 * $prayer);

        return $calculation / 4;
    }

    public function __toString()
    {
        return json_encode($this->hiscores);
    }

    public function toArray()
    {
        return [
            'hiscores' => array_map(function(HiscoreRow $hiscore) {
                return $hiscore->toArray();
            }, $this->hiscores),
            'noTotalLevel' => $this->noTotalLevel
        ];
    }
}
