<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Competition;
use AppBundle\Form\Type\SearchType ;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     *
     * @return Response
     */
    public function indexAction(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createSearchForm();
        $competitionRepo = $em->getRepository(Competition::class);

        if ($this->handleSearchForm($request, $form)) {
            $keyword = $form->get('keyword')->getData();
            $competitions = $competitionRepo->findByTeamNameKeyword($keyword);
        } else {
            $competitions = $competitionRepo->findAllOrderedByDate();
        }

        return $this->render('home/index.html.twig', [
            'competitions' => $competitions,
            'searchForm'   => $form->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     *
     * @return bool
     */
    private function handleSearchForm(Request $request, FormInterface $form): bool
    {
        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            if ($request->isMethod('POST')) {
                $this->addFlash('warning', 'Please, provide min 2 characters!!!');
            }

            return false;
        }

        $keyword = $form->get('keyword')->getData();

        $this->addFlash('success', 'Filtered Matches by Team name with "' . $keyword . '"');

        return true;
    }

    /**
     * @return FormInterface
     */
    private function createSearchForm(): FormInterface
    {
        return $this->createForm(SearchType::class, null, ['method' => 'POST']);
    }
}
