<?php

namespace App\Controller;

use App\Entity\Offres;
use App\Repository\OffresRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart', 'cart_')]
class CartController extends AbstractController
{
    //Affichage des détails du panier
    #[Route('/', 'index')]
    public function index(SessionInterface $session, OffresRepository $offresRepo): Response
    {
        $panier = $session->get('panier', []);

        //Initialisation des variables
        $data = [];
        $total = 0;

        foreach ($panier as $id => $quantite) {
            $offre = $offresRepo->find($id);

            $data[] = [
                'offre' => $offre,
                'quantite' => $quantite
            ];

            $total += $offre->getPrix() * $quantite;
        }
        return $this->render('cart/index.html.twig', compact('data','total'));
    }

    //Gestion des ajouts dans le panier
    #[Route('/add/{id}', 'add')]
    public function add(Offres $offre, SessionInterface $session)
    {
        //Récupération de l'id de l'offre
        $id = $offre->getId();

        //Récupération du panier si il existe déjà
        $panier = $session->get('panier', []);

        //Ajout de l'offre dans le panier si non existante 
        //ou bien l'on incrémente sa quantité

        if (empty($panier[$id])) {
            $panier[$id] = 1;
        } else {
            $panier[$id]++;
        }

        $session->set('panier', $panier);

        //Redirection vers la page du panier

        return $this->redirectToRoute('cart_index');
    }
}
