<?php

namespace BertW\RunescapeHiscoresApi;

use BertW\RunescapeHiscoresApi\Exception\HiscoresException;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class Hiscores
{
    const HISCORES_URL = null;

    /** @var Client */
    protected $client;

    /** @var string */
    protected $playerType;

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
        // Retrieve and parse Hiscores HTML table for this user.
        $rows = $this->parseTableData($this->getTableData($player));

        $class = $this->playerType;
        return new $class($player, $rows);
    }

    /**
     * Retrieve the complete Hiscore HTML table data (parsed to array) if you want to parse it yourself.
     * @param string
     * @return array
     * @throws HiscoresException
     */
    public function getTableData($player)
    {
        return $this->getParsedTableFromResponse($this->request($player));
    }

    /**
     * @param $value
     * @return int|null
     */
    protected function parseTextToNumber($value)
    {
        $value = trim($value);
        if(empty($value) || $value === '--') {
            return null;
        }

        return (int)str_replace(',', '', $value);
    }

    /**
     * @param $player
     * @return ResponseInterface
     */
    protected function request($player)
    {
        return $this->client->get(static::HISCORES_URL, [
            'query' => [
                'user1' => $player
            ]
        ]);
    }

    abstract protected function getParsedTableFromResponse(ResponseInterface $response);

    abstract protected function parseTableData(array $data);
}
