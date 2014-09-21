<?php
/*
 * This file is part of the CampaignChain package.
 *
 * (c) Sandro Groganz <sandro@campaignchain.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CampaignChain\Security\Authentication\Client\OAuthBundle\Controller;

use CampaignChain\CoreBundle\CampaignChainCoreBundle,
    CampaignChain\CoreBundle\Entity\Channel,
    CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Application,
    CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Profile,
    CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Token;
use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\HttpFoundation\Request;

class OAuthController extends Controller
{
//    protected $channel;
//
//    protected $profile;
//
//    protected $token;
//
//    const STATUS_NEW_PROFILE = 'New profile and token';
//    const STATUS_NEW_TOKEN = 'New token and new scope';
//    const STATUS_NEW_SCOPE = 'Same token, but its scope updated';
//    const STATUS_SAME_SCOPE = 'Scope is the same';
//    const STATUS_NO_CHANGE = false;
//
//    private $status = self::STATUS_NO_CHANGE;
//
//    public function setChannel(Channel $channel){
//        $this->channel = $channel;
//    }
//
//    public function getChannel(){
//        return $this->channel;
//    }
//
//    public function setProfile(Profile $profile){
//        $this->profile = $profile;
//    }
//
//    public function getProfile(){
//        if(!$this->profile){
//            $repository = $this->getDoctrine()->getRepository('CampaignChainSecurityAuthenticationClientOAuthBundle:Profile');
//            $this->profile = $repository->findOneByChannel($this->channel);
//        }
//        return $this->profile;
//    }
//
//    public function setToken(Token $token){
//        $this->token = $token;
//    }

    public function newAction(Request $request)
    {
        $application = new Application();

        $form = $this->createFormBuilder($application)
            ->add('key', 'text', array('label' => 'App Key'))
            ->add('secret', 'text', array('label' => 'App Secret'))
            ->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $application->setResourceOwner($request->query->get('resource_owner'));

            // perform some action, such as saving the task to the database
            $repository = $this->getDoctrine()->getManager();
            $repository->persist($application);
            $repository->flush();

            return $this->redirect($request->query->get('redirect'));
        }

        return $this->render(
            'CampaignChainCoreBundle:Activity:new.html.twig',
            array(
                'page_title' => 'Configure App Credentials for '.$request->query->get('resource_owner'),
                'form' => $form->createView(),
            ));

    }

//    public function authenticate($resourceOwner, $applicationInfo)
//    {
//        // Get application credentials
//        $oauthApp = $this->get('campaignchain.security.authentication.client.oauth.application');
//        $application = $oauthApp->getApplication($resourceOwner);
//        $bundleParams = $this->container->getParameter('campaignchain_security_authentication_client_oauth');
//
//        if(isset($_SERVER['HTTPS']) && (!empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != 'off')){
//            $hostScheme = 'https://';
//        } else {
//            $hostScheme = 'http://';
//        }
//
//        $config = array(
//            // "base_url" the url that point to HybridAuth Endpoint (where the index.php and config.php are found)
//            "base_url" => $hostScheme.$_SERVER['HTTP_HOST'].$this->generateUrl('campaignchain_security_authentication_client_oauth_login'),
//            "providers" => array (
//                $resourceOwner => array (
//                    "enabled" => true,
//                    "keys"    => array ( $applicationInfo['key_labels'][0] => $application->getKey(), $applicationInfo['secret_labels'][0] => $application->getSecret() ),
//                ),
//            ),
//            "debug_mode" => $bundleParams['debug_mode'],
//            // to enable logging, set 'debug_mode' to true, then provide here a path of a writable file
//            "debug_file" => $bundleParams['debug_file'],
//        );
//
//        $config['providers'][$resourceOwner] = array_merge($config['providers'][$resourceOwner], $applicationInfo['parameters']);
//
//        try{
//            $hybridauth = new \Hybrid_Auth( $config );
//            $resource = $hybridauth->authenticate( $resourceOwner );
//            $userProfile = $resource->getUserProfile();
//            $accessToken = $resource->getAccessToken();
//            $hybridauth->logoutAllProviders();
//
////            // Check if user already exists
////            $profile = $this->getDoctrine()->getRepository('CampaignChainSecurityAuthenticationClientOAuthBundle:Profile')->findOneBy(array(
////                'identifier' => $userProfile->identifier,
////                'resourceOwner' => $resourceOwner
////                    ));
//
//            if (!$profile) {
//                // OAuth profile does not exist yet, so create new profile and token.
//
//                // Twitter fix
//                $obj = new \ReflectionObject($userProfile);
//                if(!$obj->hasProperty("username") && $obj->hasProperty("displayName")){
//                    $userProfileUsername = $userProfile->displayName;
//                } else {
//                    $userProfileUsername = $userProfile->username;
//                }
//                if($resourceOwner == 'Twitter'){
//                    $userProfile->displayName = $userProfile->firstName;
//                    $userProfile->firstName = '';
//                }
//
//                $profile = new Profile();
//                $profile->setApplication($application);
//                $profile->setResourceOwner($resourceOwner);
//                $profile->setIdentifier($userProfile->identifier);
//                $profile->setUsername($userProfileUsername);
//                $profile->setDisplayName($userProfile->displayName);
//                $profile->setFirstName($userProfile->firstName);
//                $profile->setLastName($userProfile->lastName);
//                $profile->setDescription($userProfile->description);
//                $profile->setGender($userProfile->gender);
//                $profile->setLanguage($userProfile->language);
//                $profile->setAge($userProfile->age);
//                if(!empty($userProfile->birthYear)){
//                    $profile->setBirthday(new \DateTime($userProfile->birthYear.'-'.$userProfile->birthMonth.'-'.$userProfile->birthDay));
//                }
//                $profile->setEmail($userProfile->email);
//                $profile->setEmailVerified($userProfile->emailVerified);
//                $profile->setPhone($userProfile->phone);
//                $profile->setAddress($userProfile->address);
//                $profile->setCountry($userProfile->country);
//                $profile->setRegion($userProfile->region);
//                $profile->setCity($userProfile->city);
//                $profile->setZip($userProfile->zip);
//                $profile->setWebsiteUrl($userProfile->webSiteURL);
//                $profile->setProfileUrl($userProfile->profileURL);
//                $profile->setProfileImageUrl($userProfile->photoURL);
//                if($obj->hasProperty("coverInfoURL")){
//                    $profile->setCoverInfoUrl($userProfile->coverInfoURL);
//                }
//
//                $token = new Token();
//                $token->setApplication($application);
//                $token->setProfile($profile);
//                $token->setAccessToken($accessToken["access_token"]);
//                $token->setTokenSecret($accessToken["access_token_secret"]);
//                $token->setRefreshToken($accessToken["refresh_token"]);
//                $token->setExpiresIn($accessToken["expires_in"]);
//                $token->setExpiresAt($accessToken["expires_at"]);
//                if(isset($applicationInfo['parameters']['scope'])){
//                    $token->setScope($applicationInfo['parameters']['scope']);
//                }
//
//                $this->setProfile($profile);
//                $this->setToken($token);
//
//                $this->status = self::STATUS_NEW_PROFILE;
//            } else {
//                // The OAuth profile already exists.
//                $this->setProfile($profile);
//                $this->setChannel($profile->getChannel());
//
//                // Has a scope been set?
//                if(isset($applicationInfo['parameters']['scope'])){
//                    $newScope = $applicationInfo['parameters']['scope'];
//                    $newAccessToken = $accessToken["access_token"];
//
//                    $token = $this->getToken();
//                    $existingScope = $token->getScope();
//                    $existingAccessToken = $token->getAccessToken();
//
//                    // Is the scope different for the same profile?
//                    if($existingScope !== $newScope){
//                        if($existingAccessToken !== $newAccessToken){
//                            // If the channel has a different scope and access token,
//                            // then create a new token entry for the existing profile.
//                            // This takes care of how Google handles scopes for its APIs.
//                            $newToken = new Token();
//                            $newToken->setApplication($application);
//                            $newToken->setProfile($profile);
//                            $newToken->setAccessToken($accessToken["access_token"]);
//                            $newToken->setTokenSecret($accessToken["access_token_secret"]);
//                            $newToken->setRefreshToken($accessToken["refresh_token"]);
//                            $newToken->setExpiresIn($accessToken["expires_in"]);
//                            $newToken->setExpiresAt($accessToken["expires_at"]);
//                            $newToken->setScope($newScope);
//
//                            $this->setToken($newToken);
//
//                            $this->status = self::STATUS_NEW_TOKEN;
//                        } else {
//                            // If the channel has the same access token, but a different scope,
//                            // then just update the scope for the token.
//                            // This takes care of how Facebook deals with scope changes.
//                            $token->setScope($newScope);
//
//                            $this->setToken($token);
//
//                            $this->status = self::STATUS_NEW_SCOPE;
//                        }
//                        return $this->status;
//                    }
//                    // If the channel has the same access token and the same scope,
//                    // or no scope has been defined, then we're done.
//                    $this->status = self::STATUS_SAME_SCOPE;
//
//                    return $this->status;
//                }
//                $this->status = self::STATUS_NO_CHANGE;
//            }
//
//            return $this->status;
//        }
//        catch( Exception $e ){
//            // Display the recived error,
//            // to know more please refer to Exceptions handling section on the userguide
//            switch( $e->getCode() ){
//                case 0 : echo "Unspecified error."; break;
//                case 1 : echo "Hybriauth configuration error."; break;
//                case 2 : echo "Provider not properly configured."; break;
//                case 3 : echo "Unknown or disabled provider."; break;
//                case 4 : echo "Missing provider application credentials."; break;
//                case 5 : echo "Authentification failed. "
//                    . "The user has canceled the authentication or the provider refused the connection.";
//                    break;
//                case 6 : echo "User profile request failed. Most likely the user is not connected "
//                    . "to the provider and he should authenticate again.";
//                    $resource->logout();
//                    break;
//                case 7 : echo "User not connected to the provider.";
//                    $resource->logout();
//                    break;
//                case 8 : echo "Provider does not support this feature."; break;
//            }
//
//            // well, basically your should not display this to the end user, just give him a hint and move on..
//            echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
//        }
//    }

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

    public function loginAction(){
        \Hybrid_Endpoint::process();
    }

//    public function setStatus($status){
//        $this->status = $status;
//    }
}