<?php

use BertW\RunescapeHiscoresApi\Hiscores;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/HiscoreRow.php';
require __DIR__ . '/../src/Hiscores.php';
require __DIR__ . '/../src/Player.php';

$player = (new Hiscores())->player('SENZE');

?>
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

<p>Total level: <?php echo $player->totalLevel() ?> <?php echo $player->noTotalLevel ? '(estimate)' : '' ?></p>
<p>Combat
    level: <?php echo $player->combatLevel() ?> <?php echo $player->incompleteCombatLevel ? '(estimate)' : '' ?></p>