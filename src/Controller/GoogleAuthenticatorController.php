<?php

namespace App\Controller;

use App\Service\TOTPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleAuthenticatorController extends AbstractController
{
    #[Route(path: '/google-authenticator', name: 'app_google_authenticator')]
    public function index(TOTPService $totpService, RequestStack $requestStack, Request $request): Response
    {
        $session = $requestStack->getSession();

        $currentOtp = $session->get('currentOtp');
        //On récupère le QRCode dans la session
        //On affiche la page du QRCode
        $qrCode = $session->get('qrCode');

        //Création et traitement du formulaire de validation du code OTP
        $form = $this->createFormBuilder()
            ->add('code', TextType::class, [
                'label' => 'Code :'
            ])
            ->getForm();
        $form->handleRequest($request);

        //Si l'appel vient d'une reqûete Ajax :
        if ($request->isXmlHttpRequest()) {
            //On récupère le code introduit par l'utilisateur
            $code = $request->request->get('code');
            //On démarre le processus TOTP
            $totpService->startTOTP();
            //On interroge le service pour savoir si le code entré est valide
            $verifyCodeResult = $totpService->checkEnteredCode($code);

            //Si tous les tests sont passés, on compare le code entré avec celui en session
            //On renvoie le résultat de la vérification en JSON pour traitement en Ajax
            return $verifyCodeResult;


            // Vérification de la validité du format de code  entré dans l'input         
            // if (empty($data['code']) || !is_string($data['code']) || strlen($data['code']) !== 6 || !ctype_digit($data['code'])) {
            //     $this->addFlash('error', 'Le code doit être un nombre à 6 chiffres.');
            //     return new JsonResponse(['result' => false]);
            //     //Vérification de l'existence du code en session
            // } elseif (!$requestStack->getSession()->get('currentOtp')) {
            //     $this->addFlash('error', 'Aucun utilisateur n\'a été trouvé en session.');
            //     return new JsonResponse(['result' => false]);
            // }
        }

        return $this->render('security/google_authenticator.html.twig', [
            'currentOtp' => $currentOtp,
            'qrCode' => $qrCode,
            'form' => $form->createView()
        ]);
    }
}
// 