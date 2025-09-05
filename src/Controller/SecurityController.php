<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UsersRepository;
use App\Service\JWTService;
use App\Service\SendEmailService;
use App\Service\TOTPService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, TOTPService $totpService, SessionInterface $session): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // Obtenir l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Obtenir le dernier nom d'utilisateur saisi par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }
    //Route permettant la déconnexion de l'utilisateur
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/mot-de-passe-oublie', name: 'forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UsersRepository $usersRepo,
        JWTService $jwt,
        UrlGeneratorInterface $urlGenerator,
        SendEmailService $mailer

    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $usersRepo->findOneByEmail($form->get('email')->getData());

            //On vérifie que l'utilisateur existe
            if ($user) {

                //On génère un nouveau token
                //Header
                $header = [
                    'typ' => 'JWT',
                    'alg' => 'HS256'
                ];

                //Payload
                $payload = [
                    'user_id' => $user->getId(),
                ];

                //On génère le token
                $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'), 10800); // 3h de validité

                // Envoyer un email avec un lien de réinitialisation

                $url = $this->generateUrl('reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                $mailer->send(
                    'no-reply-reservations-jo2024@jo2024.fr',
                    $user->getEmail(),
                    'Réinitialisation de votre mot de passe',
                    'password_reset',
                    compact('url', 'user')
                );

                $this->addFlash('success', 'Un email de réinitialisation de mot de passe vous a été envoyé.');
                return $this->redirectToRoute('app_login');
            } else {
                //L'utilisateur est introuvable
                $this->addFlash('danger', 'Cette adresse email est inconnue.');

                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/mot-de-passe-oublie/{token}', name: 'reset_password')]
    public function resetPassword(string $token, JWTService $jwt, UsersRepository $usersRepo, EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Vérifier le token et permettre à l'utilisateur de réinitialiser son mot de passe

        // On vérifie si le token est valide (cohérent, pas expiré et signature correcte)
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {
            // Le token est valide
            // On récupère les données (payload)
            $payload = $jwt->getPayload($token);

            // On récupère le user
            $user = $usersRepo->find($payload['user_id']);


            // On vérifie qu'on a bien un user
            if ($user) {
                $form = $this->createForm(ResetPasswordFormType::class);
                $form->handleRequest($request);

                //On crée et envoie le formulaire de réinitialisation
                if ($form->isSubmitted() && $form->isValid()) {
                    //On crée et encrypte le nouveau mot de passe
                    $user->setPassword(
                        $passwordHasher->hashPassword($user, $form->get('password')->getData())
                    );
                    //On enregistre en bdd le mot de passe
                    $em->flush();
                    //On affiche un message de succès
                    $this->addFlash('success', 'Mot de passe mis à jour avec succès ! Vous pouvez maintenant vous connecter.');
                    return $this->redirectToRoute('app_login');
                }
                //On affiche le formulaire de réinitialisation
                return $this->render('security/reset_password.html.twig', [
                    'resetPassForm' => $form->createView(),
                ]);
            }
        }
        //On affiche un message d'erreur si le token est invalide
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }
}
