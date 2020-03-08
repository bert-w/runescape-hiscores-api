<?php

namespace BertW\RunescapeHiscoresApi\Exception;

/**
 * To be thrown when an unexpected response was retrieved from the RuneScape hiscores.
 * This happens often when the hiscores page is overloaded and shows an "Unavailable" page.
 */
class HiscoresException extends \Exception
{
    //
}
