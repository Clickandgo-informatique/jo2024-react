<?php

namespace App\Controller\Admin;

use App\Form\OffresFormType;
use App\Repository\OffresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

// use Knp\Component\Pager\PaginatorInterface;


final class OffresController extends AbstractController
{
    //Liste des offres dans le backend
    #[Route('admin/offres', name: 'app_offres_index')]
    public function index(OffresRepository $offresRepo, Request $request): Response
    {
        //     //On vérifie que l'utilisateur est admin
        //     if (!$this->isGranted('ROLE_ADMIN')) {
        //         $this->addFlash('danger', "Vous n'avez pas le droit d'accéder à cette page sans vous être connecté en tant qu'administrateur.");
        //         return $this->redirectToRoute('app_login');
        //     }

        $offres = $offresRepo->findBy([], ['intitule' => 'ASC']);
        //     $offres = $paginator->paginate(
        //         $data,
        //         $request->query->getInt('page', 1),
        //         12
        //     );
        return $this->render('admin/offres/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    //Catalogue des offres de tickets pour les clients 
    #[Route('/catalogue-offres-clients', 'app_offres_catalogue')]
    public function catalogue(OffresRepository $offresRepo): Response
    {
        $offres = $offresRepo->findBy(['isPublished' => true], ['date_debut' => 'ASC']);

        return $this->render('offres/catalogue_offres.html.twig', compact('offres'));
    }

    //Édition d'une offre
    #[Route('admin/edit/{slug}', name: 'app_offres_edit')]
    public function edit(OffresRepository $offresRepo, string $slug, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $offre = $offresRepo->findOneBy(['slug' => $slug]);
        if (!$offre) {
            throw $this->createNotFoundException("Offre non trouvée");
        }
        $title = "Modifier une offre";
        $form = $this->createForm(OffresFormType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //On sluggifie sur le champ de formulaire "intitule"
            $slug = $form->get('intitule')->getData();

            $offre->setSlug($slugger->slug($slug));
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
