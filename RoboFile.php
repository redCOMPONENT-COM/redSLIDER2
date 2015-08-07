<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
use Joomla\Github\Github;
use Joomla\Registry\Registry;

class RoboFile extends \Robo\Tasks
{
    public function release()
    {
//        $this->_exec('vendor/bin/phing -f full_packager.xml autopack');
        //$repository = $this->askDefault("What is the git remote where you want to do the release?", 'origin');
        $repository = $this->askDefault("What is the remote?", 'javi');
        $repositoryUser = $this->askDefault("What is the reporitory user?", 'javigomez');
        $repositoryName = $this->askDefault("What is the reporitory project?", 'redSLIDER2');

        $componentManifest = simplexml_load_file('redslider.xml');
        $version = $componentManifest->version;

        $this->say("Creating release: redSLIDER $version");

        $this->taskGitStack()
            ->stopOnFail()
            ->tag($version)
            ->push($repository,$version)
            ->run();
        $this->say("Tag created: $version and published at $repositoryUser/$repositoryName");

        $githubToken = $this->askHidden("What is your Github Auth token? get it at https://github.com/settings/tokens");
        //$repositoryUser = $this->askDefault("What is the reporitory user?", 'redCOMPONENT-COM');

        $options = new Registry;
        $options->set('api.url', 'https://api.github.com');
        $options->set('gh.token', (string) $githubToken);
        $github = new Github($options);

        $github->repositories->releases->create(
            $repositoryUser,
            $repositoryName,
            (string) $version,
            '',
            'develop',
            'test',
            false,
            true
        );

        $this->say("Release created at: https://github.com/$repositoryUser/$repositoryName/releases/tag/$version");

    }
}