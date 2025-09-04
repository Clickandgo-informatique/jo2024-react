<?php

namespace App\Controller\Admin;

use App\Form\OffresFormType;
use App\Repository\OffresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/offres', name: 'app_offres')]
final class OffresController extends AbstractController
{
    //Liste des offres dans le backend
    #[Route('/', name: '_index')]
    public function index(OffresRepository $offresRepo): Response
    {
        //On vérifie que l'utilisateur est admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', "Vous n'avez pas le droit d'accéder à cette page sans vous être connecté en tant qu'administrateur.");
            return $this->redirectToRoute('app_login');
        }
        $offres = $offresRepo->findBy([], ['intitule' => 'ASC']);
        return $this->render('admin/offres/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    //Édition d'une offre
    #[Route('/edit/{id}', name: '_edit', requirements: ['id' => '\d+'])]
    public function edit(OffresRepository $offresRepo, int $id, Request $request,EntityManagerInterface $em): Response
    {
        $offre = $offresRepo->find($id);
        if (!$offre) {
            throw $this->createNotFoundException("Offre non trouvée");
        }
        $title = "Modifier une offre";
        $form = $this->createForm(OffresFormType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($offre);
            $em->flush();

            $this->addFlash('success', "L'offre a bien été modifiée.");
            return $this->redirectToRoute('app_offres_index');
        }

        //On vérifie que l'utilisateur est admin
        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', "Vous n'avez pas le droit d'accéder à cette page sans vous être connecté en tant qu'administrateur.");
            return $this->redirectToRoute('app_login');
        }
        return $this->render('admin/offres/edit.html.twig', [
            'offre' => $offre,
            'title' => $title,
            'form' => $form->createView(),
        ]);
    }
}
