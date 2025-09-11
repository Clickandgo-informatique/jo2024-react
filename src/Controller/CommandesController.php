<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commandes', 'app_commandes_')]
class CommandesController extends AbstractController
{

    #[Route('/ajout', 'ajout')]
    public function index(SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);

        if($panier==[]){
            $this->addFlash('message','Votre panier est vide');
            $this->redirectToRoute('main');
        }
       

        return $this->render('commandes/index.html.twig');
    }
}
