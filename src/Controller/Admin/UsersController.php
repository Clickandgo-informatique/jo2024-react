<?php

namespace App\Controller\Admin;

use App\Form\UserFormType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/utilisateurs', 'app_utilisateurs')]
class UsersController extends AbstractController
{
    //Liste des utilisateurs par ordre ascendant
    #[Route('/', '_index')]
    public function index(UsersRepository $usersRepo): Response
    {
        $utilisateurs = $usersRepo->findBy([], ['nickname' => 'ASC']);

        return $this->render('admin/utilisateurs/index.html.twig', compact('utilisateurs'));
    }

    //Modification d'un utilisateur
    #[Route('/edit/{id}', name: '_edit', requirements: ['id' => '\d+'])]
    public function edit(UsersRepository $usersRepo, int $id, Request $request, EntityManagerInterface $em): Response
    {
        $utilisateur = $usersRepo->find($id);
   
        $title = "Modifier un utilisateur";

        $form = $this->createForm(UserFormType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($utilisateur);
            $em->flush();
        }

        return $this->render('admin/utilisateurs/user-form.html.twig', [
            'form' => $form->createView(),
            'title' => $title,
            'utilisateur' => $utilisateur
        ]);
    }
}
