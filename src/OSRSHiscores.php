<?php

namespace BertW\RunescapeHiscoresApi;

use BertW\RunescapeHiscoresApi\Exception\HiscoresException;
use BertW\RunescapeHiscoresApi\Exception\PlayerNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class OSRSHiscores extends Hiscores
{
    const HISCORES_URL = 'https://secure.runescape.com/m=hiscore_oldschool/c=1/hiscorepersonal';

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

        $rows = [];
        $type = HiscoreRow::SKILL;

        // Loop table data and create a Player object.
        foreach($data as $row) {
            if(empty($row)) {
                // Conveniently, there's an empty table row that indicates we reached the bottom section,
                // which lists the minigame hiscores.
                $type = HiscoreRow::MINIGAME;
                continue;
            }
            $rows[] = $type === HiscoreRow::SKILL ? $this->parseSkill($row) : $this->parseMinigame($row);
        }

        return $rows;
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
            'type' => HiscoreRow::SKILL
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
            'type' => HiscoreRow::MINIGAME
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

        if($crawler->filterXPath('//*[@id="contentHiscores"]/div[contains(., \'No player\')]')->count()) {
            throw new PlayerNotFoundException('No player "' . $this->player . '" found.');
        }

        if(!$element->count()) {
            throw new HiscoresException('Unexpected response received. Could not find the hiscores table.');
        }

        return $element->filterXPath('//tr')->each(function(Crawler $tr, $i) {
            return $tr->filterXPath('//td')->each(function(Crawler $td, $i) {
                $img = $td->filterXPath('//img');
                if($img->count()) {
                    return $img->attr('src');
                }
                return $td->text();
            });
        });
    }
}
