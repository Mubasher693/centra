<?php

namespace KanbanBoard;

/**
 * Authentication class for login and session management.
 */
class Authentication
{
    private $client_id;
    private $client_secret;
    private $client_state;

    /**
     *
     */
    public function __construct()
    {
        $this->client_id     = Utilities::env('GH_CLIENT_ID');
        $this->client_secret = Utilities::env('GH_CLIENT_SECRET');
        $this->client_state  = Utilities::env('GH_CLIENT_SECRET');
    }

    /**
     * @return void
     */
    public function logout()
    {
        unset($_SESSION['gh-token']);
    }

    /**
     * @return mixed|string|void|null
     */
    public function login()
    {
        session_start();
        $token = null;
        if (array_key_exists('gh-token', $_SESSION)) {
            $token = $_SESSION['gh-token'];
        } elseif (Utilities::hasValue($_GET, 'code')
            && Utilities::hasValue($_GET, 'state')
            && $_SESSION['redirected']
        ) {
            $_SESSION['redirected'] = false;
            $token = $this->_returnsFromGithub($_GET['code']);
        } else {
            $_SESSION['redirected'] = true;
            $this->_redirectToGithub();
        }
        $this->logout();
        $_SESSION['gh-token'] = $token;
        return $token;
    }

    /**
     * @return void
     */
    private function _redirectToGithub()
    {
        $url = 'Location: https://github.com/login/oauth/authorize';
        $url .= '?client_id=' . $this->client_id;
        $url .= '&scope=repo';
        $url .= '&state='.$this->client_state;
        header($url);
        exit();
    }

    /**
     * @param $code
     * @return mixed|string|void|null
     */
    private function _returnsFromGithub($code)
    {
        $url  = 'https://github.com/login/oauth/access_token';
        $data = [
            'code'          => $code,
            'state'         => $this->client_state,
            'client_id'     => $this->client_id,
            'client_secret' => $this->client_secret
        ];
        $options = [
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data),
            ],
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === false) {
            die('Error');
        }
        $result = explode('=', explode('&', $result)[0]);
        array_shift($result);
        return array_shift($result);
    }
}
