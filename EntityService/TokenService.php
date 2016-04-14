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

use CampaignChain\CoreBundle\Entity\Channel;
use CampaignChain\CoreBundle\Entity\Location;
use CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Application;
use CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Token;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TokenService
{
    const STATUS_NEW_TOKEN = 'New token and new scope';
    const STATUS_NEW_SCOPE = 'Same token, but its scope updated';
    const STATUS_SAME_SCOPE = 'Scope is the same';
    const STATUS_NO_CHANGE = false;

    protected $token;
    protected $em;
    protected $container;


    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function getToken(Location $location = null){
        if($location){
            $repository = $this->em->getRepository('CampaignChainSecurityAuthenticationClientOAuthBundle:Token');
            $token = $repository->findOneByLocation($location);
            if (!$token) {
                throw new \Exception(
                    'No token found for location '.$location->getId()
                );
            }
            return $token;
        } else {
            return $this->token;
        }
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

    public function setToken(Token $newToken){
        if(!$newToken->getLocation()){
            // Check whether the token already exists in relation to a channel.
            $repository = $this->em
                ->getRepository('CampaignChainSecurityAuthenticationClientOAuthBundle:Token');

            $query = $repository->createQueryBuilder('token')
                ->where('token.application = :application')
                ->andWhere('token.accessToken = :accessToken')
                ->andWhere('token.location IS NOT NULL')
                ->setParameter('application', $newToken->getApplication())
                ->setParameter('accessToken', $newToken->getAccessToken())
                ->getQuery();

            $oldToken = $query->getResult();

            if(!$oldToken){
                // So, there's no token related to a specific channel, but perhaps there is one
                // that has been persisted at a previous attempt to connect to the channel?
                // TODO: Implement what to do if token was persisted previously without channel relationship?

                $this->token = $newToken;
                $this->em->persist($this->token);
                $this->em->flush();

                return self::STATUS_NEW_TOKEN;
            } else {
                // Has a scope been set?
                if($newToken->getScope()){
                    $newScope = $newToken->getScope();
                    $newAccessToken = $newToken->getAccessToken();

                    $existingScope = $oldToken->getScope();
                    $existingAccessToken = $oldToken->getAccessToken();

                    // Is the scope different for the same profile?
                    if($existingScope !== $newScope){
                        if($existingAccessToken !== $newAccessToken){
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

                        $repository->persist($this->token);
                        $repository->flush();

                        return $status;
                    }
                    // If the channel has the same access token and the same scope,
                    // or no scope has been defined, then we're done.
                    $status = self::STATUS_SAME_SCOPE;

                    return $status;
                }
                $status = self::STATUS_NO_CHANGE;
            }
        } else {
            $this->em->persist($newToken);
            $this->em->flush();

            return true;
        }
    }
}