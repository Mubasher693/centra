<?php

namespace KanbanBoard;

require '../../vendor/autoload.php';

use Github\Api\Issue\Milestones;
use \Github\Client;
use \Github\HttpClient\CachedHttpClient;
use Guzzle\Http\EntityBodyInterface;

/**
 * Github Client module.
 */
class Github
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var array|Milestones
     */
    private $milestone_api;

    /**
     * @var
     */
    private $account;

    /**
     * @param $token
     * @param $account
     */
    public function __construct($token, $account)
    {
        $this->account = $account;
        $this->client  = new Client(new CachedHttpClient(['cache_dir' => '/tmp/github-api-cache']));
        $this->client->authenticate($token, Client::AUTH_HTTP_TOKEN);
        $this->milestone_api = $this->client->api('issues')->milestones();
    }

    /**
     * @param $repository
     * @return array
     */
    public function milestones($repository): array
    {
        return $this->milestone_api->all($this->account, $repository);
    }

    /**
     * @param $repository
     * @param $milestone_id
     * @return array|EntityBodyInterface|mixed|string
     */
    public function issues($repository, $milestone_id)
    {
        $issue_parameters = [
            'milestone' => $milestone_id,
            'state'     => 'all'
        ];
        return $this->client->api('issue')->all($this->account, $repository, $issue_parameters);
    }
}
