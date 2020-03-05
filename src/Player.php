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
    protected $noTotalLevel;

    /**
     * @param HiscoreRow[] $hiscores
     */
    public function __construct($hiscores = [])
    {
        $this->hiscores = $hiscores;

        if(is_null($this->get('overall')->level)) {
            $this->noTotalLevel = true;
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
     * Retrieve this users' total level, or the sum of the users' hiscores if there isn't any.
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

    public function __toString()
    {
        return json_encode($this->hiscores);
    }
}