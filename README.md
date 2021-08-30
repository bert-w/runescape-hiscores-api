# bert-w/runescape-hiscores-api
![PHP Pipeline](https://github.com/bert-w/runescape-hiscores-api/actions/workflows/php.yml/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/bert-w/runescape-hiscores-api/v/stable)](https://packagist.org/packages/bert-w/runescape-hiscores-api)
[![Total Downloads](https://poser.pugx.org/bert-w/runescape-hiscores-api/downloads)](https://packagist.org/packages/bert-w/runescape-hiscores-api)
[![License](https://poser.pugx.org/bert-w/runescape-hiscores-api/license)](https://packagist.org/packages/bert-w/runescape-hiscores-api)

A PHP implementation to request player data in a nice format from:
- OSRS Hiscores https://secure.runescape.com/m=hiscore_oldschool/overall
- RS3 Hiscores https://secure.runescape.com/m=hiscore/ranking

![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_fishing1.gif)
![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_hitpoints1.gif)
![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_defence1.gif)
![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_slayer1.gif)
![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_woodcutting1.gif)
![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_firemaking1.gif)

# Installation instructions
`composer require bert-w/runescape-hiscores-api`

## Code Samples

### Retrieving player data
***Note**: It is important to define a user-agent as seen below, since the RuneScape website might throw errors
if none is given.*
```php
// OSRS
$hiscores = new \BertW\RunescapeHiscoresApi\OSRSHiscores([
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.131 Safari/537.36',
    ],
]);
// RS3
$hiscores = new \BertW\RunescapeHiscoresApi\RS3Hiscores([
    'headers' => [
        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/92.0.4515.131 Safari/537.36',
    ],
]);

$player = $hiscores->player('someplayer');
```

### Get total level
```php
$totalLevel = $player->totalLevel();
// Returns (int).
```

### Get all skills or minigames
```php
$skills = $player->skills();

// Only available for OSRS:
$minigames = $player->minigames();
```
Results in the following array:
```
Array
(
    [0] => BertW\RunescapeHiscoresApi\HiscoreRow Object
        (
            [icon] => 
            [name] => Overall
            [rank] => 1
            [level] => 2898
            [experience] => 5600000000
            [score] => 
            [type] => skill
        )

    [1] => BertW\RunescapeHiscoresApi\HiscoreRow Object
        (
            [icon] => 
            [name] => Attack
            [rank] => 263
            [level] => 99
            [experience] => 200000000
            [score] => 
            [type] => skill
        )
    ...
)
```

### Get a specific skill / minigame
```php
// Case insensitive skill or minigame search. To be certain, use the
// exact name as used on the OSRS Hiscores page.
$player->get('agility')->level;
$player->get('Clue Scrolls (all)')->rank;
// Returns a \BertW\RunescapeHiscoresApi\HiscoreRow object.
```
All properties on this `HiscoreRow` object are:
- `$player->get('agility')->icon` (string|null)
  - URL to a skill icon that is also found on the hiscores page (like ![skill_icon_magic1](https://www.runescape.com/img/rsp777/hiscores/skill_icon_magic1.gif)).
- `$player->get('agility')->name` (string)
  - Name of the skill or minigame as shown on the hiscores ("Agility", "Defence", "Runecraft")
- `$player->get('agility')->rank` (int) {
  - Global ranking of this users' skill or minigame.
- `$player->get('agility')->level` (int|null)
  - Level of the skill (`null` for minigames).
- `$player->get('agility')->experience` (int|null)
  - Total experience of the skill  (always `null` for minigames).
- `$player->get('agility')->score` (int|null)
  - Total score of the minigame (always `null` for skills).

### List of OSRS skills
```php
$player->get('Overall')->level;
$player->get('Attack')->level;
$player->get('Defence')->level;
$player->get('Strength')->level;
$player->get('Hitpoints')->level; // (differs from RS3 "Constitution")
$player->get('Ranged')->level;
$player->get('Prayer')->level;
$player->get('Magic')->level;
$player->get('Cooking')->level;
$player->get('Woodcutting')->level;
$player->get('Fletching')->level;
$player->get('Fishing')->level;
$player->get('Firemaking')->level;
$player->get('Crafting')->level;
$player->get('Smithing')->level;
$player->get('Mining')->level;
$player->get('Herblore')->level;
$player->get('Agility')->level;
$player->get('Thieving')->level;
$player->get('Slayer')->level;
$player->get('Farming')->level;
$player->get('Runecraft')->level; // (differs from RS3 "Runecrafting")
$player->get('Hunter')->level;
$player->get('Construction')->level;
```

### List of RS3 skills
```php
$player->get('Overall')->level;
$player->get('Attack')->level;
$player->get('Defence')->level;
$player->get('Strength')->level;
$player->get('Constitution')->level; // (differs from OSRS "Hitpoints")
$player->get('Ranged')->level;
$player->get('Prayer')->level;
$player->get('Magic')->level;
$player->get('Cooking')->level;
$player->get('Woodcutting')->level;
$player->get('Fletching')->level;
$player->get('Fishing')->level;
$player->get('Firemaking')->level;
$player->get('Crafting')->level;
$player->get('Smithing')->level;
$player->get('Mining')->level;
$player->get('Herblore')->level;
$player->get('Agility')->level;
$player->get('Thieving')->level;
$player->get('Slayer')->level;
$player->get('Farming')->level;
$player->get('Runecrafting')->level; // (differs from OSRS "Runecraft")
$player->get('Hunter')->level;
$player->get('Construction')->level;
$player->get('Summoning')->level;
$player->get('Dungeoneering')->level;
$player->get('Divination')->level;
$player->get('Invention')->level;
$player->get('Archaeology')->level;
```
