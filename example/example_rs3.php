<?php

use BertW\RunescapeHiscoresApi\RS3Hiscores;

require __DIR__ . '/../vendor/autoload.php';

$username = 'le me';

$player = (new RS3Hiscores([
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
</table>
