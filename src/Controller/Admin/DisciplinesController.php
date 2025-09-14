<?php

namespace App\Controller\Admin;

use App\Entity\Disciplines;
use App\Form\DisciplinesFormType;
use App\Repository\DisciplinesRepository;
use BaconQrCode\Renderer\Color\Rgb;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class DisciplinesController extends AbstractController
{
    //liste de toutes les disciplines
    #[Route('/admin/disciplines', name: 'app_disciplines_index')]
    public function index(DisciplinesRepository $disciplinesRepo): Response
    {
        $disciplines = $disciplinesRepo->findBy([], ['intitule' => 'ASC']);
        return $this->render('admin/disciplines/index.html.twig', ['disciplines' => $disciplines]);
    }

    //Ajout d'une nouvelle discipline
    #[Route('/admin/disciplines/ajout', name: 'app_disciplines_new')]
    public function new(EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        $discipline = new Disciplines();
        $title = "Ajouter une discipline";

        $form = $this->createForm(DisciplinesFormType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On sluggifie sur le champ de formulaire "intitule"
            $slug = $form->get('intitule')->getData();
            $discipline->setSlug($slugger->slug($slug));

            $em->persist($discipline);
            $em->flush();

            $this->redirectToRoute('app_disciplines_index');
        }

        $this->addFlash('succcess', 'La discipline a bien été enregistrée dans la base.');
        return $this->render('admin/disciplines/discipline-form.html.twig', ['form' => $form->createView(), 'title' => $title]);
    }

    //Modifier une discipline
    #[Route('/admin/disciplines/edit/{slug}', name: 'app_disciplines_edit')]
    public function edit(DisciplinesRepository $disciplinesRepo, EntityManagerInterface $em, Request $request, string $slug, SluggerInterface $slugger): Response
    {
        $discipline = $disciplinesRepo->findOneBy(['slug' => $slug]);
        $title = "Modifier une discipline";

        $form = $this->createForm(DisciplinesFormType::class, $discipline);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On sluggifie sur le champ de formulaire "intitule"
            $slug = $form->get('intitule')->getData();
            $discipline->setSlug($slugger->slug($slug));

            $em->persist($discipline);
            $em->flush();

            $this->redirectToRoute('app_disciplines_index');
        }

        $this->addFlash('succcess', 'Les modification concernant la discipline ont bien été enregistrées dans la base.');
        return $this->render('admin/disciplines/discipline-form.html.twig', ['discipline' => $discipline, 'form' => $form->createView(), 'title' => $title]);
    }
}
