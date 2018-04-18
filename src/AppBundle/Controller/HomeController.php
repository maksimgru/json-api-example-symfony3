<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     * @throws \RuntimeException
     */
    public function indexAction(Request $request, EntityManagerInterface $em): Response
    {
       $competitions = $em->getRepository(Competition::class)->findAllOrderedByDate();

        return $this->render('home/index.html.twig', [
            'competitions' => $competitions,
        ]);
    }
}
