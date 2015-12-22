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