<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     *
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function indexAction(EntityManagerInterface $em): Response
    {
        $competitions = $em->getRepository(Competition::class)->findAllOrderedByDate();

        return $this->render('home/index.html.twig', [
            'competitions' => $competitions,
        ]);
    }

    /**
     * @Route("/search-submit", name="search-submit", methods={"POST"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function searchAction(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createSearchForm();

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addFlash('warning', 'Please, provide min 2 characters!!!');
            return $this->redirectToRoute('home');
        }

        $keyword = $form->get('keyword')->getData();

        $filteredCompetitions = $em->getRepository(Competition::class)->findByTeamNameKeyword($keyword);
        //$filteredCompetitions = $em->getRepository(Competition::class)->findAllOrderedByDate(['limit'=>3]);

        $this->addFlash('success', 'Filtered Matches by Team name with "' . $keyword . '"');

        return $this->render('home/index.html.twig', [
            'competitions' => $filteredCompetitions,
            'searchForm'   => $form
        ]);
    }

    /**
     * @return FormInterface
     */
    private function createSearchForm(): FormInterface
    {
        return $this->createForm(SearchType::class);
    }
}
