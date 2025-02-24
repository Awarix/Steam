# Steam

This package provides an easy way to get details from the steam api service.  The services it can access are:

- `ISteamNews`
- `Steam_Player`
- `Steam_User`
- `Steam_User_Stats`
- `Steam_App`

## Installation

Begin by installing this package with composer.

	"require": {
		"syntax/steam-api": "dev-master"
	}

Next, update composer from the terminal.

	composer update syntax/steam-api

> Alternately, you can run "composer require syntax/steam-api:dev-master" from the command line.

Once that is finished, add the service provider to `app/config/app.php`

	'Syntax\SteamApi\SteamApiServiceProvider',

> The alias to Steam is already handled by the package.

Lastly, publish the config file.  You can get your API key from [Steam](http://steamcommunity.com/dev/apikey)

	php artisan config:publish syntax/steam-api

## Usage

Each service from the steam api has it's own methods you can use.

- [News](#news)
- [Player](#player)
- [User](#user)
- [User Stats](#user-stats)
- [App](#app)

### News
The [Steam News](https://developer.valvesoftware.com/wiki/Steam_Web_API#GetNewsForApp_.28v0002.29) web api is used to get articles for games.

```php
Steam::news()
```

#### GetNewsForApp
This method will get the news articles for a given app id.  It has three parameters.

##### Arguments

Name | Type | Description | Required | Default
-----|------|-------------|----------|---------
appId| int  | The id for the app you want news on | Yes
count | int | The number of news items to return | No | 5
maxlength | int | The maximum number of characters to return | No | null

Example usage

```php
<?php
	$news = Steam::news()->GetNewsForApp($appId, 5, 500)->newsitems;
?>
```

### Player
The [Player Service](https://developer.valvesoftware.com/wiki/Steam_Web_API#GetOwnedGames_.28v0001.29) is used to get details on players.

When instantiating the player class, you are required to pass a steamId.

```php
Steam::player($steamId)
```

#### GetSteamLevel
This method will return the level of the steam user given.  It simply returns the integer of their current level.

#### GetPlayerLevelDetails
This will return a Syntax\Containers\Player_Level object with full details for the players level.  The object contains the following properties.

- playerXp
- playerLevel
- xpToLevelUp
- xpForCurrentLevel
- currentLevelFloor
- currentLevelCeieling
- percentThroughLevel

#### GetBadges
This call will give you a list of the badges that the player currently has.  There is currently no schema for badges so all you will get is the id and details.

#### GetOwnedGames
GetOwnedGames returns a list of games a player owns along with some playtime information, if the profile is publicly visible. Private, friends-only, and other privacy settings are not supported unless you are asking for your own personal details (ie the WebAPI key you are using is linked to the steamid you are requesting).

##### Arguments

Name | Type | Description | Required | Default
-----|------|-------------|----------|---------
includeAppInfo| boolean  | Whether or not to include game details | No | true
includePlayedFreeGames | boolean | Whether or not to include free games | No | false
appIdsFilter | array | An array of appIds.  These will be the only ones returned if the user has them | No | array()

This will return a collection of Syntax\SteamApi\Containers\Game objects.  These objects contain the following properties.

- appId
- name
- playtimeTwoWeeks
- playtimeForever
- playtimeForeverReadable
- icon
- logo
- header
- hasCommunityVisibleStats

#### GetRecentlyPlayedGames
GetRecentlyPlayedGames returns a list of games a player has played in the last two weeks, if the profile is publicly visible. Private, friends-only, and other privacy settings are not supported unless you are asking for your own personal details (ie the WebAPI key you are using is linked to the steamid you are requesting).

##### Arguments

Name | Type | Description | Required | Default
-----|------|-------------|----------|---------
count| int  | The number of games to return | No | null

This will return a collection of Syntax\SteamApi\Containers\Game objects.

#### IsPlayingSharedGame
IsPlayingSharedGame returns the original owner's SteamID if a borrowing account is currently playing this game. If the game is not borrowed or the borrower currently doesn't play this game, the result is always 0.

##### Arguments

Name | Type | Description | Required | Default
-----|------|-------------|----------|---------
appId| int  | The game to check for  | Yes |

### User
The [User](https://developer.valvesoftware.com/wiki/Steam_Web_API#GetFriendList_.28v0001.29) WebAPI call is used to get details about the user specifically.

When instantiating the user class, you are required to pass a steamId.

```php
Steam::user($steamId)
```

#### GetPlayerSummaries
This will return details on the user.

##### Arguments

Name | Type | Description | Required | Default
-----|------|-------------|----------|---------
steamId| int[]  | An array of (or singular) steam id(s) to get details for  | No | Steam id passed to user()

```php
	$player = Steam::user($steamId)->GetPlayerSummaries()[0];
```

This will return a Syntax\SteamApi\Containers\Player object with the following properties.

- steamId
- communityVisibilityState
- profileState
- personaName
- lastLogoff
- profileUrl
- avatar
- avatarMedium
- avatarFull
- personaState
- primaryClanId
- timecreated
- personaStateFlags

#### GetFriendList
Returns the friend list of any Steam user, provided his Steam Community profile visibility is set to "Public".

##### Arguments

Name | Type | Description | Required | Default
-----|------|-------------|----------|---------
relationship| string (all or friend)  | The type of friends to get | No | all

Once the list of friends is gathered, it is passed through [GetPlayerSummaries](#GetPlayerSummaries).  This allows you to get back a collection of Player objects.

### User Stats
The [User Stats](https://developer.valvesoftware.com/wiki/Steam_Web_API#GetPlayerAchievements_.28v0001.29) WebAPI call is used to get details about a user's gaming.

When instantiating the user stats class, you are required to pass a steamId.

```php
Steam::userStats($steamId)
```

#### GetPlayerAchievements
Returns a list of achievements for this user by app id.

##### Arguments

Name | Type | Description | Required | Default
-----|------|-------------|----------|---------
appId| int | The id of the game you want the user's achievements in | Yes |

This will return a Syntax\SteamApi\Containers\Achievement object with the following properties.

- apiName
- achieved
- name
- description

#### GetGlobalAchievementPercentagesForApp
This method will return a list of all chievements for the specified game and the percentage that each achievement has been unlocked.

##### Arguments

Name | Type | Description | Required | Default
-----|------|-------------|----------|---------
appId| int | The id of the game you want the user's achievements in | Yes |

#### GetUserStatsForGame
Returns a list of achievements for this user by app id.

##### Arguments

Name | Type | Description | Required | Default
-----|------|-------------|----------|---------
appId| int | The id of the game you want the user's achievements in | Yes |

This returns the steam api objects.  The details are very minimal.

Example output for Rocksmith 2014

```php
Array
(
    [0] => stdClass Object
        (
            [name] => ACHIEVEMENT_1
            [achieved] => 1
        )

    [1] => stdClass Object
        (
            [name] => ACHIEVEMENT_2
            [achieved] => 1
        )

    [2] => stdClass Object
        (
            [name] => ACHIEVEMENT_45
            [achieved] => 1
        )

    [3] => stdClass Object
        (
            [name] => ACHIEVEMENT_49
            [achieved] => 1
        )

)
```

### App
This area will get details for games.

```php
Steam::app()
```

#### appDetails
This gets all the details for a game.  This is most of the infomation from the store page of a game.

##### Arguments

Name | Type | Description | Required | Default
-----|------|-------------|----------|---------
appIds| int[] | The ids of the games you want details for | Yes |

This will return a Syntax\SteamApi\Containers\App object with the following properties.

- id
- name
- controllerSupport
- description
- about
- header
- website
- pcRequirements
- legal
- developers
- publishers
- price
- platforms
- metacritic
- categories
- genres
- release

#### GetAppList
This method will return an array of app objects directly from steam.  It includes the appId and the app name.

Example output
```php
Array
(
    [0] => stdClass Object
        (
            [appid] => 5
            [name] => Dedicated Server
        )

    [1] => stdClass Object
        (
            [appid] => 7
            [name] => Steam Client
        )

    [2] => stdClass Object
        (
            [appid] => 8
            [name] => winui2
        )

    [3] => stdClass Object
        (
            [appid] => 10
            [name] => Counter-Strike
        )
)
```