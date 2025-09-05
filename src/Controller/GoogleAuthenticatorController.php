<?php

namespace App\Controller;

use App\Service\TOTPService;
use PragmaRX\Google2FA\Google2FA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleAuthenticatorController extends AbstractController
{
    #[Route(path: '/google-authenticator', name: 'app_google_authenticator')]
    public function index(TOTPService $totpService, RequestStack $requestStack, Request $request): Response
    {
        //On démarre le processus TOTP
        $totpService->startTOTP();
        $currentOtp = $requestStack->getSession()->get('currentOtp');
        $qrCode = $requestStack->getSession()->get('qrCode');
        //On récupère le QRCode dans la session
        //On affiche la page du QRCode

        //Traitement du formulaire de validation du code OTP
        $form = $this->createFormBuilder()
            ->add('code', TextType::class, [
                'label' => 'Code Google Authenticator :',
                'attr' => ['placeholder' => 'Entrez ici le code']
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Vérification de la validité du format de code  entré dans l'input         
            if (empty($data['code']) || !is_string($data['code']) || strlen($data['code']) !== 6 || !ctype_digit($data['code'])) {
                $this->addFlash('error', 'Le code doit être un nombre à 6 chiffres.');
                return new JsonResponse(['result' => false]);
                //Vérification de l'existence du code en session
            } elseif (!$requestStack->getSession()->get('currentOtp')) {
                $this->addFlash('error', 'Aucun utilisateur n\'a été trouvé en session.');
                return new JsonResponse(['result' => false]);
            }
            //Si tous les tests sont passés, on compare le code entré avec celui en session
            $google2FA = new Google2FA();
            $code = $data['code'];
            $isValid = $google2FA->verifyKey($requestStack->getSession()->get('user')['google2FA_secret'], $code);
            dd(json_encode(['code' => $code, 'result' => $isValid]));
        }


        return $this->render('security/google_authenticator.html.twig', [
            'currentOtp' => $currentOtp,
            'qrCode' => $qrCode,
            'form' => $form->createView()
        ]);
    }
}
