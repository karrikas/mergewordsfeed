<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TwitterConnect
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $access_token;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $access_token_secret;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $screen_name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $x_auth_expires;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set accessToken
     *
     * @param string $accessToken
     *
     * @return TwitterConnect
     */
    public function setAccessToken($accessToken)
    {
        $this->access_token = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->access_token;
    }

    /**
     * Set accessTokenSecret
     *
     * @param string $accessTokenSecret
     *
     * @return TwitterConnect
     */
    public function setAccessTokenSecret($accessTokenSecret)
    {
        $this->access_token_secret = $accessTokenSecret;

        return $this;
    }

    /**
     * Get accessTokenSecret
     *
     * @return string
     */
    public function getAccessTokenSecret()
    {
        return $this->access_token_secret;
    }

    /**
     * Set userId
     *
     * @param string $userId
     *
     * @return TwitterConnect
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Set screenName
     *
     * @param string $screenName
     *
     * @return TwitterConnect
     */
    public function setScreenName($screenName)
    {
        $this->screen_name = $screenName;

        return $this;
    }

    /**
     * Get screenName
     *
     * @return string
     */
    public function getScreenName()
    {
        return $this->screen_name;
    }

    /**
     * Set xAuthExpires
     *
     * @param string $xAuthExpires
     *
     * @return TwitterConnect
     */
    public function setXAuthExpires($xAuthExpires)
    {
        $this->x_auth_expires = $xAuthExpires;

        return $this;
    }

    /**
     * Get xAuthExpires
     *
     * @return string
     */
    public function getXAuthExpires()
    {
        return $this->x_auth_expires;
    }
}
