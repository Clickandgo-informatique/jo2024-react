<?php

namespace App\Controller;

use App\Entity\Offres;
use App\Repository\OffresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        return $this->render('cart/index.html.twig', compact('data', 'total'));
    }

    //Gestion des ajouts dans le panier
    #[Route('/add/{id}', 'add')]
    public function add(Offres $offre, SessionInterface $session, Request $request): JsonResponse
    {
        //Vérification qu'il s'agît bien d'une requête Ajax
        if ($request->isXmlHttpRequest()) {

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

            //Affichage du partial du panier en Ajax

            return new JsonResponse(['content' => $this->renderView('_partials/_cart-items.html.twig')]);
        } else {
            return new Jsonresponse('La reqûete doit s\'effectuer en Ajax');
        }
    }

    //Gestion des suppressions d'offre dans le panier
    #[Route('/remove/{id}', 'remove')]
    public function remove(Offres $offre, OffresRepository $offresRepo, EntityManagerInterface $em, SessionInterface $session, $id)
    {
        //Récupération de l'id de l'offre
        $id = $offre->getId();

        //Récupération du panier si il existe déjà
        $panier = $session->get('panier', []);

        //Suppression de l'offre dans le panier si un seul exemplaire 
        //ou bien l'on décrémente sa quantité

        if (!empty($panier[$id])) {
            if ($panier[$id] > 1) {
                $panier[$id]--;
            } else {
                unset($panier[$id]);
            }
        }
        $session->set('panier', $panier);

        //Redirection vers la page du panier
        return $this->redirectToRoute('cart_index');
    }
    //Gestion des suppressions d'offre dans le panier
    #[Route('/delete/{id}', 'delete')]
    public function delete(Offres $offre, SessionInterface $session, $id)
    {
        //Récupération de l'id de l'offre
        $id = $offre->getId();

        //Récupération du panier si il existe déjà
        $panier = $session->get('panier', []);

        //Suppression de l'offre dans le panier si un seul exemplaire 
        //ou bien l'on décrémente sa quantité

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        //Redirection vers la page du panier
        return $this->redirectToRoute('cart_index');
    }

    //Gestion de vidage panier
    #[Route('/empty-cart', 'empty_cart')]
    public function emptyCart(SessionInterface $session): Response
    {
        $session->remove('panier');
        //Redirection vers la page du panier
        return $this->redirectToRoute('cart_index');
    }
}
