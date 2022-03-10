<?php

namespace BertW\RunescapeHiscoresApi;

use BertW\RunescapeHiscoresApi\Exception\HiscoresException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class RS3Hiscores extends Hiscores
{
    public const HISCORES_URL = 'https://secure.runescape.com/m=hiscore/compare';

    /**
     * In the hiscores html, every row in the table is associated with a skill, which has an associated ID.
     * @var string[]
     */
    public const SKILL_MAP = [
        0 => 'Overall',
        1 => 'Attack',
        2 => 'Defence',
        3 => 'Strength',
        4 => 'Constitution',
        5 => 'Ranged',
        6 => 'Prayer',
        7 => 'Magic',
        8 => 'Cooking',
        9 => 'Woodcutting',
        10 => 'Fletching',
        11 => 'Fishing',
        12 => 'Firemaking',
        13 => 'Crafting',
        14 => 'Smithing',
        15 => 'Mining',
        16 => 'Herblore',
        17 => 'Agility',
        18 => 'Thieving',
        19 => 'Slayer',
        20 => 'Farming',
        21 => 'Runecrafting',
        22 => 'Hunter',
        23 => 'Construction',
        24 => 'Summoning',
        25 => 'Dungeoneering',
        26 => 'Divination',
        27 => 'Invention',
        28 => 'Archaeology',
    ];

    /** @var string */
    protected $playerType = RS3Player::class;


    /**
     * Parse table data into skills.
     * @param array $data
     * @return array
     */
    protected function parseTableData(array $data)
    {
        // Strip 1 row from the table.
        array_shift($data);

        $rows = [];

        // Loop table data and create a Player object.
        foreach ($data as $row) {
            $rows[] = $this->parseSkill($row);
        }

        // Minigames are always empty in the RS3 hiscores page.
        return ['skills' => $rows, 'minigames' => []];
    }

    /**
     * @param array $row
     * @return HiscoreRow
     */
    protected function parseSkill(array $row)
    {
        return new HiscoreRow([
            'name' => static::SKILL_MAP[$row[0]] ?? null,
            'rank' => $this->parseTextToNumber($row[1]),
            'experience' => $this->parseTextToNumber($row[2]),
            'level' => $this->parseTextToNumber($row[3]),
            'type' => HiscoreRow::SKILL,
        ]);
    }

    /**
     * @param ResponseInterface $response
     * @return array
     * @throws HiscoresException
     */
    protected function getParsedTableFromResponse(ResponseInterface $response)
    {
        $crawler = new Crawler((string)$response->getBody());

        $element = $crawler->filterXPath('//*[@class="playerStats"][1]//table');

        if (!$element->count()) {
            throw new HiscoresException('Unexpected response received. Could not find the hiscores table.');
        }

        return $element->filterXPath('//tr')->each(function (Crawler $tr, $i) {
            return array_merge([$this->findSkillId($tr)], $tr->filterXPath('//td')->each(function (Crawler $td, $i) {
                return $td->text();
            }));
        });
    }

    /**
     * Find the skill ID from a crawler object that has an anchor tag. The skill ID is hidden inside
     * a "table=3" query parameter.
     * @param Crawler $obj
     * @return int|null
     */
    protected function findSkillId(Crawler $obj)
    {
        $a = $obj->filterXPath('//a');
        if ($a->count()) {
            parse_str(parse_url($a->link()->getUri(), PHP_URL_QUERY), $query);

            return isset($query['table']) ? (int)$query['table'] : null;
        }
        return null;
    }
}
