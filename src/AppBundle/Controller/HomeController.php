<?php

namespace AppBundle\Controller;

use AppBundle\Service\SoccerWayApi;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @param SoccerWayApi $soccerWayApi
     *
     * @return Response
     */
    public function indexAction(SoccerWayApi $soccerWayApi): Response
    {
        $users = $soccerWayApi->getTestUsers();

        return $this->render('home/index.html.twig', [
            'users' => $users,
        ]);
    }
}
