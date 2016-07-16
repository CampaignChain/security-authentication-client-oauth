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

namespace CampaignChain\Security\Authentication\Client\OAuthBundle\EntityService;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class ApplicationService
{
    protected $em;
    protected $container;


    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param $resourceOwner
     * @return \CampaignChain\Security\Authentication\Client\OAuthBundle\Entity\Application
     */
    public function getApplication($resourceOwner){
        return $this->em->getRepository('CampaignChainSecurityAuthenticationClientOAuthBundle:Application')->findOneBy(array(
            'resourceOwner' => $resourceOwner,
        ));
    }

    public function newApplicationTpl($resourceOwner, $applicationInfo){
        return new Response($this->container->get('templating')->render(
            'CampaignChainSecurityAuthenticationClientOAuthBundle:Application:new.html.twig',
            array(
                'page_title' => 'Provide Application Credentials',
                'resource_owner' => $resourceOwner,
                'key_label' => $applicationInfo['key_labels'][1],
                'secret_label' => $applicationInfo['secret_labels'][1],
                'config_url' => $applicationInfo['config_url'],
            )
        ));
    }
}