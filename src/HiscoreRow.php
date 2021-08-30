<?php

namespace BertW\RunescapeHiscoresApi;

class HiscoreRow
{
    public const SKILL = 'skill';
    public const MINIGAME = 'minigame';

    /**
     * URL to the skill icon that is prepended to each skill in the hiscores table.
     * @var string|null
     */
    public $icon;

    /**
     * Name of the skill or name of the minigame (including "Overall" for total).
     * @var string
     */
    public $name;

    /**
     * The global rank of this users' skill.
     * @var int
     */
    public $rank;

    /**
     * Skills only.
     * @var int
     */
    public $level;

    /**
     * Skills only.
     * @var int|null
     */
    public $experience;

    /**
     * Minigames only.
     * @var int|null
     */
    public $score;

    /**
     * One of the constants in this class.
     * @var string
     */
    public $type;


    /**
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        foreach ($properties as $property => $value) {
            $this->$property = $value;
        }
    }

    public function __toString()
    {
        return json_encode($this);
    }

    public function toArray()
    {
        return [
            'icon' => $this->icon,
            'name' => $this->name,
            'rank' => $this->rank,
            'level' => $this->level,
            'experience' => $this->experience,
            'score' => $this->score,
            'type' => $this->type,
        ];
    }
}
