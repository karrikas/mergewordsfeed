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
}
