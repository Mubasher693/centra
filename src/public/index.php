<?php

use KanbanBoard\Github;
use KanbanBoard\Utilities;
use DevCoder\DotEnv;
use KanbanBoard\Authentication;
use KanbanBoard\Application;

require '../classes/KanbanBoard/Github.php';
require '../classes/Utilities.php';
require '../classes/KanbanBoard/Authentication.php';
require '../classes/DotEnv.php';

// Load env file.
(new DotEnv('../../.env'))->load();
$repositories   = explode('|', Utilities::env('GH_REPOSITORIES'));
$authentication = new Authentication();
$token  = $authentication->login();
$github = new Github($token, Utilities::env('GH_ACCOUNT'));
$board  = new Application($github, $repositories, ['waiting-for-feedback']);
$data   = $board->board();
$mustache = new Mustache_Engine(
    [
        'loader' => new Mustache_Loader_FilesystemLoader('../views'),
    ]
);

echo $mustache->render('index', ['milestones' => $data]);
