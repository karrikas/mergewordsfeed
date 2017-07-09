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

    public function getConnection($accessToken, $accessTokenSecret)
    {
        return new TwitterOAuth($this->comsumerKey, $this->comsumerSecret, $accessToken, $accessTokenSecret);
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
