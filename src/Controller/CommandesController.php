<?php

namespace App\Controller;

use App\Entity\Commandes;
use App\Entity\DetailsCommandes;
use App\Repository\CommandesRepository;
use App\Repository\OffresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commandes', 'app_commandes_')]
class CommandesController extends AbstractController
{

    #[Route('/ajout', 'ajout')]
    public function index(SessionInterface $session, OffresRepository $offresRepo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $panier = $session->get('panier', []);

        if ($panier === []) {
            $this->addFlash('message', 'Votre panier est vide');
            $this->redirectToRoute('app_main');
        }

        //On crée la commande car le panier contient des articles
        $commande = new Commandes();

        //On renseigne le client
        $commande->setUser($this->getUser());

        //On crée un référence
        $commande->setReference(uniqid());

        //Parcours du panier pour créer les lignes de détail
        //de la commande

        foreach ($panier as $item => $quantity) {
            $detailsCommande = new DetailsCommandes();

            //Recherche de l'offre
            $offre = $offresRepo->find($item);
            $prix = $offre->getPrix();

            //Création de la ligne
            $detailsCommande
                ->setOffres($offre)
                ->setPrix($prix)
                ->setQuantite($quantity);

            //On ajoute les lignes de détails à la commande correspondante
            $commande->addDetailsCommande($detailsCommande);
        }
        //On enregistre la commande en cours
        $em->persist($commande);
        $em->flush();

        //On vide le panier
        $session->remove('panier');

        $this->addFlash('success', 'Votre commande a bien été prise en compte. Merci.');
        return $this->redirectToRoute('app_commandes_liste');
    }

    //Affichage de la liste commandes du client
    #[Route('/liste', 'liste')]
    public function liste(CommandesRepository $commandesRepo): Response
    {
        //Recherche du client connecté
        $user = $this->getUser();

        //Recherche de la liste des commandes filtrée par client
        $commandes = $commandesRepo->findBy(['user' => $user], ['created_at' => 'DESC']);

        return $this->render('commandes/index.html.twig', compact('commandes'));
    }

    //Affichage d'une commande individuelle et paiement
    #[Route('/afficher-commande/{id}', 'show')]
    public function show($id, CommandesRepository $commandesRepo): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $commande = $commandesRepo->find($id);
        
        return $this->render('commandes/show.html.twig', compact('commande'));
    }
}
