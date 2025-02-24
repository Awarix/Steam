<?php namespace Syntax\SteamApi;

class Steam_Player extends Client {

	public function __construct($steamId)
	{
		parent::__construct();
		$this->interface = 'IPlayerService';
		$this->steamId   = $steamId;
		$this->isService = true;
	}

	public function GetSteamLevel()
	{
		// Set up the api details
		$this->method  = __FUNCTION__;
		$this->version = 'v0001';

		// Set up the arguments
		$arguments = ['steamId' => $this->steamId];
		$arguments = json_encode($arguments);

		// Get the client
		$client = $this->setUpClient($arguments)->response;

		return $client->player_level;
	}

	public function GetPlayerLevelDetails()
	{
		$details = $this->GetBadges();

		$details = new Containers\Player_Level($details);

		return $details;
	}

	public function GetBadges()
	{
		// Set up the api details
		$this->method  = __FUNCTION__;
		$this->version = 'v0001';

		// Set up the arguments
		$arguments = ['steamId' => $this->steamId];
		$arguments = json_encode($arguments);

		// Get the client
		$client = $this->setUpClient($arguments)->response;

		return $client;
	}

	public function GetCommunityBadgeProgress($badgeId = null)
	{
		// Set up the api details
		$this->method  = __FUNCTION__;
		$this->version = 'v0001';

		// Set up the arguments
		$arguments   = ['steamId' => $this->steamId];
		if ($badgeId != null) $arguments['badgeid'] = $badgeId;
		$arguments   = json_encode($arguments);

		// Get the client
		$client = $this->setUpClient($arguments)->response;

		return $client;
	}

	public function GetOwnedGames($includeAppInfo = true, $includePlayedFreeGames = false, $appIdsFilter = array())
	{
		// Set up the api details
		$this->method  = __FUNCTION__;
		$this->version = 'v0001';

		// Set up the arguments
		$arguments                                                           = ['steamId' => $this->steamId];
		if ($includeAppInfo) $arguments['include_appinfo']                   = $includeAppInfo;
		if ($includePlayedFreeGames) $arguments['include_played_free_games'] = $includePlayedFreeGames;
		if (count($appIdsFilter) > 0) $arguments['appids_filter']            = $appIdsFilter;
		$arguments                                                           = json_encode($arguments);

		// Get the client
		$client = $this->setUpClient($arguments)->response;

		// Clean up the games
		$games = $this->convertToObjects($client->games);

		return $games;
	}

	public function GetRecentlyPlayedGames($count = null)
	{
		// Set up the api details
		$this->method  = __FUNCTION__;
		$this->version = 'v0001';

		// Set up the arguments
		$arguments                                = ['steamId' => $this->steamId];
		if (!is_null($count)) $arguments['count'] = $count;
		$arguments                                = json_encode($arguments);

		// Get the client
		$client = $this->setUpClient($arguments)->response;

		if ($client->total_count > 0) {
			// Clean up the games
			$games = $this->convertToObjects($client->games);

			return $games;
		}

		return null;
	}

	public function IsPlayingSharedGame($appIdPlaying)
	{
		// Set up the api details
		$this->method  = __FUNCTION__;
		$this->version = 'v0001';

		// Set up the arguments
		$arguments = [
			'steamId'       => $this->steamId,
			'appid_playing' => $appIdPlaying
		];
		$arguments = json_encode($arguments);

		// Get the client
		$client = $this->setUpClient($arguments)->response;

		return $client->lender_steamId;
	}

	protected function convertToObjects($games, $totalTimeFlag = true)
	{
		$cleanedGames = new Collection;

		foreach ($games as $game) {
			$cleanedGames->add(new Containers\Game($game));
		}

		$games = $cleanedGames->sortBy(function ($game) {
			return $game->name;
		});

		return $games;
	}
}