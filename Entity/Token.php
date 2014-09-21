<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) Sandro Groganz <sandro@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Security\Authentication\Client\OAuthBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="campaignchain_security_authentication_client_oauth_token")
 */
class Token
{
    /**
     * @ORM\ManyToOne(targetEntity="Application", inversedBy="tokens")
     */
    protected $application;

    /**
     * @ORM\OneToOne(targetEntity="CampaignChain\CoreBundle\Entity\Location")
     * @ORM\JoinColumn(name="location_id", referencedColumnName="id", nullable=true)
     */
    protected $location;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $accessToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $refreshToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $tokenSecret;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $expiresIn;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $expiresAt;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $scope;

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
     * @return Token
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Get accessToken
     *
     * @return string 
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * Set refreshToken
     *
     * @param string $refreshToken
     * @return Token
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * Get refreshToken
     *
     * @return string 
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * Set tokenSecret
     *
     * @param string $tokenSecret
     * @return Token
     */
    public function setTokenSecret($tokenSecret)
    {
        $this->tokenSecret = $tokenSecret;

        return $this;
    }

    /**
     * Get tokenSecret
     *
     * @return string 
     */
    public function getTokenSecret()
    {
        return $this->tokenSecret;
    }

    /**
     * Set expiresIn
     *
     * @param string $expiresIn
     * @return Token
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * Get expiresIn
     *
     * @return string 
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * Set expiresAt
     *
     * @param string $expiresAt
     * @return Token
     */
    public function setExpiresAt($expiresAt)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * Get expiresAt
     *
     * @return string 
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * Set application
     *
     * @param \CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Application $application
     * @return Token
     */
    public function setApplication(\CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Application $application = null)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get application
     *
     * @return \CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set scope
     *
     * @param string $scope
     * @return Token
     */
    public function setScope($scope)
    {
        $this->scope = $scope;

        return $this;
    }

    /**
     * Get scope
     *
     * @return string 
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * Set location
     *
     * @param \CampaignChain\CoreBundle\Entity\Location $location
     * @return Token
     */
    public function setLocation(\CampaignChain\CoreBundle\Entity\Location $location = null)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return \CampaignChain\CoreBundle\Entity\Location
     */
    public function getLocation()
    {
        return $this->location;
    }
}
