<?php
/*
 * Copyright 2016 CampaignChain, Inc. <info@campaignchain.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace CampaignChain\Security\Authentication\Client\OAuthBundle\Entity;

use CampaignChain\CoreBundle\Entity\Meta;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="campaignchain_security_authentication_client_oauth_application")
 */
class Application extends Meta
{
    /**
     * @ORM\OneToMany(targetEntity="Token", mappedBy="application")
     */
    protected $tokens;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $resourceOwner;

    /**
     * @ORM\Column(type="string", length=255, name="`key`")
     */
    protected $key;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $secret;

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
     * Set key
     *
     * @param string $key
     * @return Application
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get key
     *
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set secret
     *
     * @param string $secret
     * @return Application
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get secret
     *
     * @return string 
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set resourceOwner
     *
     * @param string $resourceOwner
     * @return Application
     */
    public function setResourceOwner($resourceOwner)
    {
        $this->resourceOwner = $resourceOwner;

        return $this;
    }

    /**
     * Get resourceOwner
     *
     * @return string 
     */
    public function getResourceOwner()
    {
        return $this->resourceOwner;
    }

    /**
     * Add tokens
     *
     * @param \CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Token $tokens
     * @return Application
     */
    public function addToken(\CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Token $tokens)
    {
        $this->tokens[] = $tokens;

        return $this;
    }

    /**
     * Remove tokens
     *
     * @param \CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Token $tokens
     */
    public function removeToken(\CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Token $tokens)
    {
        $this->tokens->removeElement($tokens);
    }

    /**
     * Get tokens
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTokens()
    {
        return $this->tokens;
    }
}
