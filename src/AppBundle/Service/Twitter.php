<?php
namespace AppBundle\Service;

use Abraham\TwitterOAuth\TwitterOAuth;

class Twitter
{
    public $comsumerKey;
    public $comsumerSecret;

    public function __construct($comsumerKey, $comsumerSecret)
    {
        $this->comsumerKey = $comsumerKey;
        $this->comsumerSecret = $comsumerSecret;

        define('OAUTH_CALLBACK', getenv('OAUTH_CALLBACK'));
    }

    public function getConnection($accessToken = null, $accessTokenSecret = null)
    {
        return new TwitterOAuth($this->comsumerKey, $this->comsumerSecret, $accessToken, $accessTokenSecret);
    }

    public function getAuthorizeUrl($connection)
    {
        $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
        $_SESSION['oauth_token'] = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
        
        return $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
    }

    /**
     * Test connectin to twitter account.
     */
    public function testConnection($accessToken, $accessTokenSecret)
    {
        try {
            $connection = new TwitterOAuth($this->comsumerKey, $this->comsumerSecret, $accessToken, $accessTokenSecret);
            $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
            $content = $connection->get('account/verify_credentials');

            if ($connection->getLastHttpCode() == 200) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    public function post($accessToken, $accessTokenSecret, $status)
    {
        $connection = $this->getConnection($accessToken, $accessTokenSecret);
        $statues = $connection->post('statuses/update', ['status' => $status]);
    }
}
