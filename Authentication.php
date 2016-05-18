<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) CampaignChain, Inc. <info@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Security\Authentication\Client\OAuthBundle;

use CampaignChain\CoreBundle\Exception\ExternalApiException;
use CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Token;
use CampaignChain\CoreBundle\Entity\Location;
use CampaignChain\Security\Authentication\Client\OAuthBundle\EntityService\TokenService;
use \Hybrid_Auth;
use Symfony\Component\DependencyInjection\Container;

class Authentication
{
    const STATUS_NEW_PROFILE = 'New profile and token';
    const STATUS_NEW_TOKEN = 'New token and new scope';
    const STATUS_NEW_SCOPE = 'Same token, but its scope updated';
    const STATUS_SAME_SCOPE = 'Scope is the same';
    const STATUS_NO_CHANGE = false;

    private $profile;

    /**
     * @var TokenService
     */
    private $oauthToken;

    /**
     * @var Container
     */
    protected $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @param $resourceOwner
     * @param $applicationInfo
     * @param bool $sameToken
     *
     * @throws \Exception
     */
    public function authenticate($resourceOwner, $applicationInfo, $sameToken = false)
    {
        // Get application credentials
        $oauthApp = $this->container->get('campaignchain.security.authentication.client.oauth.application');
        $application = $oauthApp->getApplication($resourceOwner);

        $bundleParams = $this->container->getParameter('campaignchain_security_authentication_client_oauth');

        if(isset($_SERVER['HTTPS']) && (!empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'off')){
            $hostScheme = 'https://';
        } else {
            $hostScheme = 'http://';
        }

        $config = array(
            // "base_url" the url that point to HybridAuth Endpoint (where the index.php and config.php are found)
            "base_url" => $hostScheme.$_SERVER['HTTP_HOST'].$this->container->get('router')->generate('campaignchain_security_authentication_client_oauth_login'),
            "providers" => array (
                $resourceOwner => array (
                    "enabled" => true,
                    "keys"    => array ( $applicationInfo['key_labels'][0] => $application->getKey(), $applicationInfo['secret_labels'][0] => $application->getSecret() ),
                ),
            ),
            "debug_mode" => $bundleParams['debug_mode'],
            // to enable logging, set 'debug_mode' to true, then provide here a path of a writable file
            "debug_file" => $bundleParams['debug_file'],
        );

        $config['providers'][$resourceOwner] = array_merge($config['providers'][$resourceOwner], $applicationInfo['parameters']);

        if(isset($applicationInfo['wrapper'])){
            $config['providers'][$resourceOwner]['wrapper']['class']
                = $applicationInfo['wrapper']['class'];
            $config['providers'][$resourceOwner]['wrapper']['path']
                = $this->container->get('kernel')->getRootDir()
                    .DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR
                    .str_replace('/', DIRECTORY_SEPARATOR, $applicationInfo['wrapper']['path']);
        }

        try {
            $hybridauth = new Hybrid_Auth( $config );
            $resource = $hybridauth->authenticate( $resourceOwner );
            $this->profile = $resource->getUserProfile();
            $accessToken = $resource->getAccessToken();
            $hybridauth->logoutAllProviders();

            $token = new Token();
            $token->setApplication($application);
            $token->setAccessToken($accessToken["access_token"]);
            $token->setTokenSecret($accessToken["access_token_secret"]);
            $token->setRefreshToken($accessToken["refresh_token"]);
            $token->setExpiresIn($accessToken["expires_in"]);
            $token->setExpiresAt($accessToken["expires_at"]);
            if (isset($resource->adapter->api->api_base_url)) {
                $token->setEndpoint($resource->adapter->api->api_base_url);
            }
            if(isset($applicationInfo['parameters']['scope'])){
                $token->setScope($applicationInfo['parameters']['scope']);
            }

            $this->oauthToken = $this->container->get('campaignchain.security.authentication.client.oauth.token');
            return $this->oauthToken->setToken($token, $sameToken);
        } catch( \Exception $e ) {
            throw new ExternalApiException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getProfile(){
        return $this->profile;
    }

    public function setLocation(Location $location){
        $token = $this->oauthToken->getToken();
        $token->setLocation($location);

        return $this->oauthToken->setToken($token);
    }

    public function getToken(){
        return $this->oauthToken->getToken();
    }

//    public function persist(){
//        if($this->status == self::STATUS_NO_CHANGE || $this->status == self::STATUS_SAME_SCOPE){
//            return false;
//        } else {
//            $channel = $this->getChannel();
//            $profile = $this->getProfile();
//
//            $repository = $this->getDoctrine()->getManager();
//
//            // If no profile exists, create the new one
//            if($this->status == self::STATUS_NEW_PROFILE){
//                $profile->setChannel($channel);
//                $repository->persist($profile);
//            }
//
//            $token = $this->getToken();
//            $token->setChannel($channel);
//            $token->setProfile($profile);
//            $repository->persist($token);
//
//            $repository->flush();
//
//            return true;
//        }
//    }
//
//    public function loginAction(){
//        \Hybrid_Endpoint::process();
//    }

//    public function setStatus($status){
//        $this->status = $status;
//    }
}