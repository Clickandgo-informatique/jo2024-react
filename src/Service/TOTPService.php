<?php

namespace App\Service;

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;


class TOTPService
{
    protected $requestStack;
    private $session;
    private $google2FA;

    public function __construct(RequestStack $requestStack)
    {
        //On initialise la session
        $this->requestStack = $requestStack;
        $this->session = $this->requestStack->getSession();
        // On initialise Google2FA
        $this->google2FA = new Google2FA();
    }

    public function startTOTP()
    {
        // On génère une clé secrète
        $secretKey = $this->google2FA->generateSecretKey();
       
        //On stocke la clé secrète dans un tableau User
        $user = ['google2FA_secret' => $secretKey, 'email' => 'user@example.com'];

        // On stocke la variable $user dans la session
        $this->session->set('user', $user);

        //On nomme notre application
        $appName = 'reservations-jo-2024';

        // On génère l'URL du QR code
        $qrCodeUrl = $this->google2FA->getQRCodeUrl(
            $appName,
            $user['email'],
            $user['google2FA_secret']
        );

        //On prépare le QRCode pour l'affichage
        $imageSize = 250;
        $writer = new Writer(new GDLibRenderer($imageSize));

        //On encode l'url en base64 pour l'afficher dans la vue
        $encodedQrCodeData = base64_encode($writer->writeString($qrCodeUrl));

        //On stocke le QRCode dans la session
        // $currentOtp = $this->google2FA->getCurrentOtp($user['google2FA_secret']);      
        // $this->session->set('currentOtp', $currentOtp);
        $this->session->set('qrCode', $encodedQrCodeData);
    }
    public function checkEnteredCode(string $code): JsonResponse
    {
        $google2FASecret = $this->session->get('user')['google2FA_secret'];
   
        $isValid = $this->google2FA->verifyKey($google2FASecret, $code);

        return new JsonResponse([
            'code' => $code,
            'result' => $isValid
        ]);
    }
}
