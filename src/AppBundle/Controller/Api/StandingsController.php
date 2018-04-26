<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\Team;
use AppBundle\Form\Type\DateIntervalType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StandingsController extends Controller
{
    /**
     * @Route("/api/standings", name="api_standings", methods={"GET"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return JsonResponse
     */
    public function getStandingsAction(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $form = $this->createForm(DateIntervalType::class);

        $form->submit([
            'from' => $request->get('from'),
            'to'   => $request->get('to'),
        ]);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->json([
                'error' => 'Please, provide a valid date format like Y-m-d'
            ], Response::HTTP_BAD_REQUEST);
        }

        $from = $form->get('from')->getData();
        $to = $form->get('to')->getData();

        $teams = $em
            ->getRepository(Team::class)
            ->findByCompetitionsDate([
                'from'  => $from,
                'to'    => $to,
                'assoc' => true,
            ]);

        return $this->json($teams, Response::HTTP_OK);
    }
}
