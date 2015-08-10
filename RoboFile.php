<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
use Joomla\Github\Github;
use Joomla\Http\Http;
use Joomla\Registry\Registry;

class RoboFile extends \Robo\Tasks
{
	public function release()
	{
		$this->buildPackage('full_packager.xml');

		//$repository = $this->askDefault("What is the git remote where you want to do the release?", 'origin');
		$remote     = $this->askDefault("What is the remote?", 'javi');

		$version = $this->getExtensionVersion();

		$this->say("Creating github tag: $version");
		$githubRepository = $this->getGithubRepo();
		$githubToken = $this->getGithubToken();

		$this->taskGitStack()
		     ->stopOnFail()
		     ->tag($version)
		     ->push($remote, $version)
		     ->run();
		$this->say("Tag created: $version and published at $githubRepository->owner/$githubRepository->name");

		$this->say("Creating the release at: https://github.com/$githubRepository->owner/$githubRepository->name/releases/tag/$version");
		$github = $this->getGithub();
		$changesInRelease = "#Changelog: \n\n" . implode("\n* ", $this->getPullsInLatestRelease());
		$response = $github->repositories->releases->create(
			$githubRepository->owner,
			$githubRepository->name,
			(string) $version,
			'',
			'develop',
			$changesInRelease,
			false,
			true
		);

		$this->say("Uploading the Extension package to the Github release: $version");
		$uploadUrl = str_replace("{?name}", "?access_token=$githubToken&name=redslider-v${version}_fullpackage-unzipfirst.zip", $response->upload_url);

		$http    = new Http();
		$data    = array("file" => "@.dist/redslider-v${version}_fullpackage-unzipfirst.zip");
		$headers = array("Content-Type" => "application/zip");
		$http->post($uploadUrl, $data, $headers);
	}

	private function getPullsInLatestRelease()
	{
		$version = $this->getExtensionVersion();

		$github           = $this->getGithub();
		$githubRepository = $this->getGithubRepo();

		$latestRelease = $github->repositories->releases->get($githubRepository->owner, $githubRepository->name, 'latest');

		$pulls = $this->getAllRepoPulls();


		$changes = array();
		foreach ($this->$pulls as $pull)
		{
			if (strtotime($pull->merged_at) > strtotime($latestRelease->published_at))
			{
				$changes[] = $pull->title;
			}
		}

		return $changes;
	}

	/**
	 * @param   string  $release1  You can use Release Tag, for example tags/2.0.24. Or use Release Id, for example: 1643513
	 * @param   string  $release2
	 *
	 * @return array
	 */
	public function getPullsBetweenTwoVersions($release1, $release2)
	{
		$github           = $this->getGithub();
		$githubRepository = $this->getGithubRepo();

		$release1 = $github->repositories->releases->get($githubRepository->owner, $githubRepository->name, $release1);
		$release2 = $github->repositories->releases->get($githubRepository->owner, $githubRepository->name, $release2);

		$pulls = $this->getAllRepoPulls();

		$changes = array();
		foreach ($pulls as $pull)
		{
			if (
			(strtotime($pull->merged_at) > strtotime($release1->published_at))
			&& strtotime($pull->merged_at) < strtotime($release2->published_at)
			)
			{
				$changes[] = $pull->title;
			}
		}

		return $changes;
	}


	public function updateChangelog()
	{
		$version = $this->getExtensionVersion();

		$changes = $this->getPullsInLatestRelease();

		if ($changes)
		{
			$this->taskChangelog()
			     ->changes()
			     ->version($version)
			     ->run();
		}
	}

	public function createChangelog()
	{
		$github           = $this->getGithub();
		$githubRepository = $this->getGithubRepo();

		$releases = array_values($github->repositories->releases->getList($githubRepository->owner, $githubRepository->name));
		for ($i = 0, $j = count($releases);$i<$j;$i++)
		{
			if(!array_key_exists($i+1, $releases))
			{
				break;
			}

			$version = $releases[$i]->tag_name;
			$tag = 'tags/' . $releases[$i]->tag_name;
			$previousTag = 'tags/' . $releases[$i+1]->tag_name;

			$changes = $this->getPullsBetweenTwoVersions($previousTag,$tag);

			if ($changes)
			{
				$this->taskChangelog()
				     ->changes($this->getPullsBetweenTwoVersions($previousTag,$tag))
				     ->version($version)
				     ->run();
			}
		}
	}

	private function getGithub()
	{
		$githubToken = $this->getGithubToken();
		//$githubRepository->owner = $this->askDefault("What is the reporitory user?", 'redCOMPONENT-COM');

		$options = new Registry;
		$options->set('api.url', 'https://api.github.com');
		$options->set('gh.token', (string) $githubToken);

		return new Github($options);
	}

	private function getGithubRepo()
	{
		if (!isset($this->githubRepository))
		{
			$this->githubRepository = new stdClass;
			$this->githubRepository->owner = $this->askDefault("What is the reporitory user?", 'redCOMPONENT-COM');
			$this->githubRepository->name = $this->askDefault("What is the reporitory project?", 'redSLIDER2');
		}

		return $this->githubRepository;
	}

	private function getExtensionVersion()
	{
		if (!isset($this->extensionVersion))
		{
			$componentManifest      = simplexml_load_file('redslider.xml');
			$this->extensionVersion = $componentManifest->version;
		}

		return $this->extensionVersion;
	}

	private function getGithubToken()
	{
		if (!isset($this->githubToken))
		{
			//$this->githubToken = $this->askHidden("What is your Github Auth token? get it at https://github.com/settings/tokens");
			$this->githubToken = $this->askDefault("What is your Github Auth token? get it at https://github.com/settings/tokens", 'fe8a47e259e77673d3df06400c3dd8ca2a6a0264');
		}

		return $this->githubToken;
	}

	public function buildPackage($buildFile)
	{
		$this->_exec("vendor/bin/phing -f $buildFile autopack");
	}

	private function getAllRepoPulls($state = 'closed')
	{
		$github = $this->getGithub();

		if (!isset($this->allClosedPulls))
		{
			$this->allClosedPulls = $github->pulls->getList($this->getGithubRepo()->owner, $this->getGithubRepo()->name, $state);
		}

		return $this->allClosedPulls;
	}
}