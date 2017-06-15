<?php
namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Agent;

class AgentClientRepository extends EntityRepository
{
    public function findForAgentByStatus(Agent $agent, $status = AgentClient::STATUS_INWORK)
    {
        return
            $this->_em->createQuery('
                select agentClient
                from AppBundle:AgentClient agentClient
                where identity(agentClient.agent) = :agent and agentClient.status = :status')
            ->setParameter('agent', $agent)
            ->setParameter('status', $status);
    }
    
    public function findForAgent(Agent $agent)
    {
        return
            $this->_em->createQuery('
                select agentClient
                from AppBundle:AgentClient agentClient
                where identity(agentClient.agent) = :agent')
            ->setParameter('agent', $agent);
    }
}