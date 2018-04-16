<?php

namespace AppBundle\Controller;

use AppBundle\Service\SoccerWayService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @param SoccerWayService $soccerWayApi
     *
     * @return Response
     * @throws \RuntimeException
     */
    public function indexAction(SoccerWayService $soccerWayApi): Response
    {
        $competitions = $soccerWayApi->fetchCompetitionsData();
        var_dump($competitions);die();

        return $this->render('home/index.html.twig', [
            'competitions' => $competitions,
        ]);
    }
}
