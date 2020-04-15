<?php

namespace BertW\RunescapeHiscoresApi;

use BertW\RunescapeHiscoresApi\Exception\HiscoresException;
use BertW\RunescapeHiscoresApi\Exception\PlayerNotFoundException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class Hiscores
{
    const OSRS_HISCORES_URL = 'https://secure.runescape.com/m=hiscore_oldschool/c=1/hiscorepersonal';

    /** @var Client */
    protected $client;

    /** @var string */
    protected $player;

    /**
     * @param mixed ...$arguments Pass arguments straight to the Guzzle Client, allowing you to set a
     * timeout or other settings.
     * @see http://docs.guzzlephp.org/en/stable/request-options.html
     */
    public function __construct(...$arguments)
    {
        $this->client = new Client(...$arguments);

    }

    /**
     * Get Hiscore data for a particular player.
     * @param string $player
     * @return Player
     * @throws HiscoresException
     */
    public function player($player)
    {
        $this->player = $player;

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
     * @throws HiscoresException
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

    /**
     * @param array $row
     * @return HiscoreRow
     */
    protected function makeSkill($row)
    {
        return new HiscoreRow([
            'icon' => $row[0],
            'name' => $row[1],
            'rank' => (int)str_replace(',', '', $row[2]),
            'level' => (int)str_replace(',', '', $row[3]),
            'experience' => (int)str_replace(',', '', $row[4]),
            'type' => HiscoreRow::SKILL
        ]);
    }

    /**
     * @param array $row
     * @return HiscoreRow
     */
    protected function makeMinigame($row)
    {
        return new HiscoreRow([
            'icon' => $row[0],
            'name' => $row[1],
            'rank' => (int)str_replace(',', '', $row[2]),
            'score' => (int)str_replace(',', '', $row[3]),
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
        $element = $crawler->filterXPath('//*[@id="contentHiscores"]/table');

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
