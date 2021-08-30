<?php

use BertW\RunescapeHiscoresApi\OSRSHiscores;

require __DIR__ . '/../vendor/autoload.php';

$username = 'Lynx Titan';

$player = (new OSRSHiscores([
    'headers' => [
        // Use Chrome user-agent so the Hiscores page doesn't return an error.
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.131 Safari/537.36',
    ],
]))->player($username);

?>
<p>Username: <?php echo $player->username() ?></p>
<p>Total level: <?php echo $player->totalLevel() ?> <?php echo $player->noTotalLevelFound() ? '(estimate)' : '' ?></p>
<p>Combat level: <?php echo $player->combatLevel() ?> <?php echo $player->incompleteCombatLevel() ? '(estimate)' : '' ?></p>

<table>
    <?php foreach($player->skills() as $skill): ?>
        <tr>
            <td><img src="<?php echo $skill->icon ?>" alt="<?php echo $skill->name ?>"/></td>
            <td><?php echo $skill->name ?></td>
            <td><?php echo $skill->rank ?></td>
            <td><?php echo $skill->level ?></td>
            <td><?php echo $skill->experience ?></td>
        </tr>
    <?php endforeach ?>
    <tr>
        <td colspan="5">
            <hr/>
        </td>
    </tr>
    <?php foreach($player->minigames() as $skill): ?>
        <tr>
            <td><img src="<?php echo $skill->icon ?>" alt="<?php echo $skill->name ?>"/></td>
            <td><?php echo $skill->name ?></td>
            <td><?php echo $skill->rank ?></td>
            <td></td>
            <td><?php echo $skill->score ?></td>
        </tr>
    <?php endforeach ?>
</table>
