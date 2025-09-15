<?php

namespace App\Controller\Admin;

use App\Entity\CategoriesOffres;
use App\Form\CategoriesOffresFormType;
use App\Repository\CategoriesOffresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoriesOffresController extends AbstractController
{
    //Liste des categories d'offres (administration)
    #[Route('admin/offres/categories-offres', name: 'app_categories_offres')]
    public function index(CategoriesOffresRepository $categoriesOffresRepo): Response
    {
        $categoriesOffres = $categoriesOffresRepo->findBy([], ['nom' => 'ASC']);
        return $this->render('admin/offres/categories-offres.html.twig', compact('categoriesOffres'));
    }

    //Créer une nouvelle catégorie d'offre
    #[Route('admin/offres/categories-offres/ajout', name: 'app_categories_offres_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $categorieOffres = new CategoriesOffres();

        $form = $this->createForm(CategoriesOffresFormType::class, $categorieOffres);
        $form->handleRequest($request);
        $title = "Créer une nouvelle catégorie d'offre";
        
        if ($form->isSubmitted() && $form->isValid()) {
            //On sluggifie sur le champ de formulaire "nom"
            $slug = $form->get('nom')->getData();
            
            $categorieOffres->setSlug($slugger->slug($slug));
            $em->persist($categorieOffres);
            $em->flush();

            $this->addFlash('success', 'La nouvelle catégorie d\'offre a bien été enregistrée dans la base.');
            return $this->redirectToRoute('app_categories_offres_index');
        }

        return $this->render('admin/offres/categories-offres-form.html.twig', ['form' => $form->createView(), 'title' => $title]);
    }

    //Modifier une categorie d'offre (administration)
    #[Route('admin/offres/categories-offres/edit{slug}', name: 'app_categories_offres_edit')]
    public function edit(CategoriesOffresRepository $categoriesOffresRepo): Response
    {
        $categoriesOffres = $categoriesOffresRepo->findBy([], ['nom' => 'ASC']);
        $title = "Modifier une catégorie d'offre";
        return $this->render('admin/offres/categories-offres.html.twig', ['categoriesOffres' => $categoriesOffres, 'title' => $title]);
    }
}
