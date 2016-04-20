<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) CampaignChain, Inc. <info@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Security\Authentication\Client\OAuthBundle\EntityService;

use CampaignChain\CoreBundle\Entity\Location;
use CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Application;
use CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Token;
use Doctrine\ORM\EntityManager;

/**
 * Class TokenService
 * @package CampaignChain\Security\Authentication\Client\OAuthBundle\EntityService
 */
class TokenService
{
    const STATUS_NEW_TOKEN = 'New token and new scope';
    const STATUS_NEW_SCOPE = 'Same token, but its scope updated';
    const STATUS_SAME_SCOPE = 'Scope is the same';
    const STATUS_NO_CHANGE = false;

    /**
     * @var Token|null
     */
    protected $token;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * TokenService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Location|null $location
     *
     * @return Token|null
     *
     * @throws \Exception
     */
    public function getToken(Location $location = null)
    {
        if (!$location) {
            return $this->token;
        }

        $repository = $this->em->getRepository('CampaignChainSecurityAuthenticationClientOAuthBundle:Token');
        $token = $repository->findOneByLocation($location);

        if (!$token) {
            throw new \Exception(
                'No token found for location '.$location->getId()
            );
        }

        return $token;
    }

    /**
     * @param Application $application
     * @return null|Token
     */
    public function getTokenByApplication(Application $application)
    {
        return $this->em
            ->getRepository('CampaignChainSecurityAuthenticationClientOAuthBundle:Token')
            ->findOneByApplication($application);
    }

    /**
     * @param Token $newToken
     * @param bool $sameToken
     * @return bool|string
     */
    public function setToken(Token $newToken, $sameToken = false)
    {
        if ($newToken->getLocation()) {
            $this->em->persist($newToken);
            $this->em->flush();

            return true;
        }

        // Check whether the token already exists in relation to a channel.
        $repository = $this->em
            ->getRepository('CampaignChainSecurityAuthenticationClientOAuthBundle:Token');

        $query = $repository->createQueryBuilder('token')
            ->where('token.application = :application')
            ->andWhere('token.accessToken = :accessToken')
            ->andWhere('token.location IS NOT NULL')
            ->setParameter('application', $newToken->getApplication())
            ->setParameter('accessToken', $newToken->getAccessToken())
            ->setMaxResults(1)
            ->getQuery();

        $oldToken = $query->getOneOrNullResult();

        if (!$oldToken) {
            // So, there's no token related to a specific channel, but perhaps there is one
            // that has been persisted at a previous attempt to connect to the channel?
            // TODO: Implement what to do if token was persisted previously without channel relationship?

            $this->token = $newToken;
            $this->em->persist($this->token);
            $this->em->flush();

            return self::STATUS_NEW_TOKEN;
        }

        // Has a scope been set?
        if (!$newToken->getScope() && !$sameToken) {
            return self::STATUS_NO_CHANGE;
        }

        //With LinkedIn if a user connects multiple times, he will get the same access token
        //And even though they are same, it has to be saved as a new token
        if ($sameToken) {
            $this->token = $newToken;
            $this->em->persist($this->token);
            $this->em->flush();

            return self::STATUS_NEW_TOKEN;
        }

        $newScope = $newToken->getScope();
        $newAccessToken = $newToken->getAccessToken();

        $existingScope = $oldToken->getScope();
        $existingAccessToken = $oldToken->getAccessToken();

        // If the channel has the same access token and the same scope,
        // or no scope has been defined, then we're done.
        if ($existingScope === $newScope) {
            return self::STATUS_SAME_SCOPE;
        }

        // Is the scope different for the same profile?
        if ($existingAccessToken !== $newAccessToken) {
            // If the channel has a different scope and access token,
            // then create a new token entry for the existing profile.
            // This takes care of how Google handles scopes for its APIs.
            $this->token = $newToken;

            $status = self::STATUS_NEW_TOKEN;
        } else {
            // If the channel has the same access token, but a different scope,
            // then just update the scope for the token.
            // This takes care of how Facebook deals with scope changes.
            $this->token = $oldToken;
            $this->token->setScope($newScope);

            $status = self::STATUS_NEW_SCOPE;
        }

        $this->em->persist($this->token);
        $this->em->flush();

        return $status;
    }

    /**
     * Removes the not assigned tokens.
     *
     * @param Token[] $tokens
     */
    public function cleanUpUnassignedTokens(array $tokens)
    {
        $this->em->clear();
        foreach ($tokens as $token) {
            $token = $this->em->merge($token);
            $this->em->remove($token);
        }

        $this->em->flush();
    }
}