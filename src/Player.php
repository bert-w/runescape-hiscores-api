<?php

namespace BertW\RunescapeHiscoresApi;

abstract class Player
{
    const COMBAT_SKILLS = [];
    /** @var string */
    protected $username;
    /** @var HiscoreRow[] */
    protected $hiscores;
    /**
     * This indicates whether an explicit total level was found on the hiscores or not.
     * If this value is true (no total level), the `totalLevel()` function calculates a minimum from the skills.
     * @var bool
     */
    protected $noTotalLevelFound;
    /**
     * When this property is true, the combat level couldn't be completely calculated, because
     * one or more of the combat skills were not in the hiscores. For those values, `1` is used,
     * resulting in a possibly lower combat level than is the case.
     * @var bool
     */
    protected $incompleteCombatLevel;

    /**
     * @param HiscoreRow[] $hiscores
     */
    public function __construct($username, $hiscores = [])
    {
        $this->username = $username;

        $this->hiscores = $hiscores;

        if(is_null($this->get('overall')->level)) {
            $this->noTotalLevelFound = true;
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
     * Calculate the player's combat level.
     * @return float
     */
    abstract public function combatLevel();

    /**
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    /**
     * @return bool
     */
    public function noTotalLevelFound()
    {
        return $this->noTotalLevelFound;
    }

    /**
     * @return bool
     */
    public function incompleteCombatLevel()
    {
        return $this->incompleteCombatLevel;
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
        if($this->noTotalLevelFound) {
            $sum = 0;
            foreach($this->skills() as $skill) {
                $sum += $skill->level;
            }
            return $sum;
        }
        return $this->get('overall')->level;
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
            'noTotalLevelFound' => $this->noTotalLevelFound
        ];
    }
}
