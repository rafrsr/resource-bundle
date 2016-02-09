<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2016 Copyright(c) - All rights reserved.
 */

namespace Rafrsr\ResourceBundle\Command;

use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SummaryCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('rafrsr:resource:summary')
            ->setDescription('Get a summary of all resources');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        /** @var EntityRepository $repo */
        $repo = $doctrine->getRepository('RafrsrResourceBundle:ResourceObject');

        $response = $repo->createQueryBuilder('res')
            ->select('count(res.id) total_count', 'sum(res.size) total_size')
            ->getQuery()->getSingleResult();

        $output->writeln(sprintf('Total Files: %s', $response['total_count']));
        $output->writeln(sprintf('Total Size: %s', $this->convertBytes($response['total_size'])));
    }

    /**
     * convertBytes
     *
     * @param int $size
     *
     * @return string
     */
    function convertBytes($size)
    {
        $i = 0;
        $iec = ["B", "Kb", "Mb", "Gb", "Tb"];
        while (($size / 1024) > 1) {
            $size = $size / 1024;
            $i++;
        }

        return (round($size, 1) . " " . $iec[$i]);
    }
}
