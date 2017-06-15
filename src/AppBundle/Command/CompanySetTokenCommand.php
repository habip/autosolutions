<?php
namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use AppBundle\Entity\User;

class CompanySetTokenCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:company:set-token')
            ->setDescription('Set Company authorization token')
            ->addArgument('username')
            ->addArgument('token')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $token    = $input->getArgument('token');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getContainer()->get('doctrine')->getManager();

        /* @var $user \AppBundle\Entity\User */
        $user = $em->createQuery('
                select u, c
                from AppBundle:User u
                    join u.company c
                where u.type = :type and u.email = :email
                ')
            ->setParameter('type', User::TYPE_COMPANY)
            ->setParameter('email', $username)
            ->getSingleResult();

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Company has already token, rewrite?', false);

        if ($user->getCompany()->getToken() && !$helper->ask($input, $output, $question)) {
            return;
        }

        $user->getCompany()->setToken(crypt($token, sprintf('$2a$07$%s$', $this->getContainer()->getParameter('secret'))));
        $em->persist($user->getCompany());
        $em->flush();

        $output->writeln('<info>Company token successfully set</info>');
    }
}