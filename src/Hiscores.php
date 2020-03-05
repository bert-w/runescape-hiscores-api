<?php

namespace BertW\RunescapeHiscoresApi;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class Hiscores
{
    const OSRS_HISCORES_URL = 'https://secure.runescape.com/m=hiscore_oldschool/c=1/hiscorepersonal';

    /** @var Client */
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Get Hiscore data for a particular player.
     * @param $player
     * @return Player
     */
    public function player($player)
    {
        // Retrieve and parse Hiscores HTML table for this user.
        $data = $this->getHiscoreTable($player);

        // Strip first 3 rows from the table.
        $data = array_slice($data, 3);

        $rows = [];
        $type = HiscoreRow::SKILL;

        // Loop table data and create a Player object.
        foreach($data as $row) {
            if(empty($row)) {
                // Conveniently, there's an empty table row that indicates we reached the bottom section,
                // which list the minigame hiscores.
                $type = HiscoreRow::MINIGAME;
                continue;
            }
            $rows[] = $type === HiscoreRow::SKILL ? $this->makeSkill($row) : $this->makeMinigame($row);
        }

        return new Player($rows);
    }

    /**
     * Retrieve the complete Hiscore HTML table data (parsed to array) if you want to parse it yourself.
     * @param string
     * @return array
     */
    public function getHiscoreTable($player)
    {
        return $this->getParsedTableFromResponse($this->request($player));
    }

    /**
     * @param $player
     * @return ResponseInterface
     */
    protected function request($player)
    {
        return $this->client->get(self::OSRS_HISCORES_URL, [
            'query' => [
                'user1' => $player
            ]
        ]);
    }

    protected function makeSkill($row)
    {
        return new HiscoreRow([
            'icon' => $row[0],
            'name' => $row[1],
            'rank' => (int)str_replace(',', '', $row[2]),
            'level' => (int)$row[3],
            'experience' => (int)str_replace(',', '', $row[4]),
            'type' => HiscoreRow::SKILL
        ]);
    }

    protected function makeMinigame($row)
    {
        return new HiscoreRow([
            'icon' => $row[0],
            'name' => $row[1],
            'rank' => (int)str_replace(',', '', $row[2]),
            'score' => (int)$row[3],
            'type' => HiscoreRow::MINIGAME
        ]);
    }

    protected function getParsedTableFromResponse(ResponseInterface $response)
    {
        $crawler = new Crawler((string)$response->getBody());
        $element = $crawler->filterXPath('//*[@id="contentHiscores"]/table');
        return $element->filter('tr')->each(function(Crawler $tr, $i) {
            return $tr->filter('td')->each(function(Crawler $td, $i) {
                $img = $td->children('img');
                if($img->count()) {
                    return $img->attr('src');
                }
                return $td->text();
            });
        });

    }
}
