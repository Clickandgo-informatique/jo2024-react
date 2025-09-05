<?php

namespace App\Controller;

use App\Service\TOTPService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GoogleAuthenticatorController extends AbstractController
{
    #[Route(path: '/google-authenticator', name: 'app_google_authenticator')]
    public function index(TOTPService $totpService, RequestStack $requestStack): Response
    {
        //On démarre le processus TOTP
        $totpService->startTOTP();
        $currentOtp = $requestStack->getSession()->get('currentOtp');
        $qrCode = $requestStack->getSession()->get('qrCode');
        //On récupère le QRCode dans la session
        //On affiche la page du QRCode

        return $this->render('security/google_authenticator.html.twig', [
            'currentOtp' => $currentOtp,
            'qrCode' => $qrCode
        ]);
    }
}
