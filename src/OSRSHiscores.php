<?php

namespace BertW\RunescapeHiscoresApi;

use BertW\RunescapeHiscoresApi\Exception\HiscoresException;
use BertW\RunescapeHiscoresApi\Exception\PlayerNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class OSRSHiscores extends Hiscores
{
    public const HISCORES_URL = 'https://secure.runescape.com/m=hiscore_oldschool/c=1/hiscorepersonal';

    /**
     * In the hiscores html, every row in the table is associated with a skill, which has an associated ID.
     * @var string[]
     */
    public const SKILL_MAP = [
        0 => 'Overall',
        1 => 'Attack',
        2 => 'Defence',
        3 => 'Strength',
        4 => 'Hitpoints',
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
        21 => 'Runecraft',
        22 => 'Hunter',
        23 => 'Construction',
    ];

    /** @var string */
    protected $playerType = OSRSPlayer::class;

    /**
     * Parse table data into skills and minigames.
     * @param array $data
     * @return array
     */
    protected function parseTableData(array $data)
    {
        // Strip first 3 rows from the table.
        $data = array_slice($data, 3);

        $skills = [];

        $minigames = [];

        $type = HiscoreRow::SKILL;

        // Loop table data and create a Player object.
        foreach ($data as $row) {
            if (empty($row)) {
                // Conveniently, there's an empty table row that indicates we reached the bottom section,
                // which lists the minigame hiscores.
                $type = HiscoreRow::MINIGAME;
                continue;
            }
            if($type === HiscoreRow::SKILL) {
                $skills[] = $this->parseSkill($row);
            } else {
                $minigames[] = $this->parseMinigame($row);
            }
        }

        return compact('skills', 'minigames');
    }

    /**
     * @param array $row
     * @return HiscoreRow
     */
    protected function parseSkill(array $row)
    {
        return new HiscoreRow([
            'icon' => $row[0],
            'name' => $row[1],
            'rank' => $this->parseTextToNumber($row[2]),
            'level' => $this->parseTextToNumber($row[3]),
            'experience' => $this->parseTextToNumber($row[4]),
            'type' => HiscoreRow::SKILL,
        ]);
    }

    /**
     * @param array $row
     * @return HiscoreRow
     */
    protected function parseMinigame(array $row)
    {
        return new HiscoreRow([
            'icon' => $row[0],
            'name' => $row[1],
            'rank' => $this->parseTextToNumber($row[2]),
            'score' => $this->parseTextToNumber($row[3]),
            'type' => HiscoreRow::MINIGAME,
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

        $element = $crawler->filterXPath('//*[@id="contentHiscores"]//table');

        $notFound = $crawler->filterXPath('//*[@id="contentHiscores"]/div[contains(., \'No player\')]');

        if ($notFound->count()) {
            throw new PlayerNotFoundException($notFound->text());
        }

        if (!$element->count()) {
            throw new HiscoresException('Unexpected response received. Could not find the hiscores table.');
        }

        return $element->filterXPath('//tr')->each(function (Crawler $tr, $i) {
            return $tr->filterXPath('//td')->each(function (Crawler $td, $i) {
                $img = $td->filterXPath('//img');
                if ($img->count()) {
                    return $img->attr('src');
                }
                return $td->text();
            });
        });
    }
}
