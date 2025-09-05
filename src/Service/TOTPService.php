<?php

namespace App\Service;

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;
use Symfony\Component\HttpFoundation\RequestStack;

class TOTPService
{
    public function __construct(private RequestStack $requestStack) {}
    public function startTOTP()
    {
        $session = $this->requestStack->getSession();
        // On initialise Google2FA
        $google2fa = new Google2FA();

        // On génère une clé secrète
        $secretKey = $google2fa->generateSecretKey();

        //On stocke la clé secrète dans un tableau User
        $user = ['google2FA_secret' => $secretKey, 'email' => 'user@example.com'];

        // On stocke la variable $user dans la session
        $session->set('user', $user);
        //On notre application
        $appName = 'reservations-jo-2024';
        // On génère l'URL du QR code
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            $appName,
            $user['email'],
            $user['google2FA_secret']
        );
        //On prépare le QRCode pour l'affichage
        $imageSize = 250;
        $writer = new Writer(new GDLibRenderer($imageSize));

        //On encode l'url en base64 pour l'afficher dans la vue
        $encodeQrCodeData = base64_encode($writer->writeString($qrCodeUrl));

        //On stocke le QRCode dans la session
        $currentOtp = $google2fa->getCurrentOtp($user['google2FA_secret']);
        $session->set('currentOtp', $currentOtp);
        $session->set('qrCode', $encodeQrCodeData);
    }
}
