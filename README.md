# bert-w/runescape-hiscores-api
[![Latest Stable Version](https://poser.pugx.org/bert-w/runescape-hiscores-api/v/stable)](https://packagist.org/packages/bert-w/runescape-hiscores-api)
[![Total Downloads](https://poser.pugx.org/bert-w/runescape-hiscores-api/downloads)](https://packagist.org/packages/bert-w/runescape-hiscores-api)
[![License](https://poser.pugx.org/bert-w/runescape-hiscores-api/license)](https://packagist.org/packages/bert-w/runescape-hiscores-api)

A PHP implementation to request player data in a nice format from the OSRS RuneScape hiscores web page.

![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_fishing1.gif)
![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_defence1.gif)
![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_slayer1.gif)
![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_woodcutting1.gif)
![](https://www.runescape.com/img/rsp777/hiscores/skill_icon_firemaking1.gif)
### Installation instructions
`composer require bert-w/runescape-hiscores-api`

##### Code Samples

###### Retrieving player data
```php
$hiscores = new \BertW\RunescapeHiscoresApi\Hiscores();

$player = $hiscores->player('someplayer');
```

###### Get total level
```php
$totalLevel = $player->totalLevel();
// Returns (int).
```

###### Get all skill or minigame data
```php
$skills = $player->skills();
$minigames = $player->minigames();
// Returns an array of \BertW\RunescapeHiscoresApi\HiscoreRow objects.
```

###### Get a specific skill / minigame
```php
// Case insensitive skill or minigame search. To be certain, use the
// exact name as used on the OSRS Hiscores page.
$player->get('agility');
$player->get('Clue Scrolls (all)');
// Returns a \BertW\RunescapeHiscoresApi\HiscoreRow object.
```
```php
$player->get('agility')->icon;
$player->get('agility')->rank;
$player->get('agility')->level;
$player->get('agility')->experience;
```

`HiscoreRow` is a simple object that holds the following properties:
- `$hiscoreRow->icon` (string|null)
  - URL to a skill icon that is also found on the hiscores page (like ![skill_icon_magic1](https://www.runescape.com/img/rsp777/hiscores/skill_icon_magic1.gif)).
- `$hiscoreRow->name` (string)
  - Name of the skill or minigame as shown on the hiscores ("Agility", "Defence", "Runecraft")
- `$hiscoreRow->rank` (int) {
  - Global ranking of this users' skill or minigame.
- `$hiscoreRow->level` (int|null)
  - Level of the skill (`null` for minigames).
- `$hiscoreRow->experience` (int|null)
  - Total experience of the skill  (`null` for minigames).
- `$hiscoreRow->score` (int|null)
  - Total score of the minigame (`null` for skills).