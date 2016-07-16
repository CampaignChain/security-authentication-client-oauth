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
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $application->setResourceOwner($request->query->get('resource_owner'));

            // perform some action, such as saving the task to the database
            $em = $this->getDoctrine()->getManager();
            $em->persist($application);
            $em->flush();

            return $this->redirect($request->query->get('redirect'));
        }

        return $this->render(
            'CampaignChainCoreBundle:Base:new.html.twig',
            array(
                'page_title' => 'Configure App Credentials for '.$request->query->get('resource_owner'),
                'form' => $form->createView(),
                'form_submit_label' => 'Save',
            ));

    }

    public function appsIndexAction(Request $request)
    {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->select('a')
            ->from('CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Application', 'a')
            ->orderBy('a.resourceOwner');
        $query = $qb->getQuery();
        $apps = $query->getResult();

        return $this->render(
            'CampaignChainSecurityAuthenticationClientOAuthBundle:Application:index.html.twig',
            array(
                'page_title' => 'OAuth Client Apps',
                'apps' => $apps
            ));
    }

    public function appsEditAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $app = $em->getRepository('CampaignChainSecurityAuthenticationClientOAuthBundle:Application')
            ->find($id);

        if (!$app) {
            throw new \Exception(
                'No OAuth app found for id '.$id
            );
        }

        $form = $this->createFormBuilder($app)
            ->add('key', 'text', array('label' => 'App Key'))
            ->add('secret', 'text', array('label' => 'App Secret'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            // perform some action, such as saving the task to the database
            $em->persist($app);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'The '.$app->getResourceOwner().' app credentials have been changed.'
            );

            return $this->redirect($this->generateUrl('campaignchain_security_authentication_client_oauth_apps'));
        }

        return $this->render(
            'CampaignChainCoreBundle:Base:new.html.twig',
            array(
                'page_title' => 'Configure App Credentials for '.$app->getResourceOwner(),
                'form' => $form->createView(),
                'form_submit_label' => 'Save',
                'form_cancel_route' => 'campaignchain_security_authentication_client_oauth_apps',
            ));
    }

    public function loginAction(){
        \Hybrid_Endpoint::process();
    }
}