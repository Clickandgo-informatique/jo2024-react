<?php

namespace App\Service;

use BaconQrCode\Renderer\GDLibRenderer;
use BaconQrCode\Writer;
use PragmaRX\Google2FA\Google2FA;
use Symfony\Component\HttpFoundation\RequestStack;

class TOTPService
{
    public function __construct(private Google2FA $google2FA, private RequestStack $requestStack)
    {
        // On initialise Google2FA
        $this->google2FA = new Google2FA();
    }

    public function startTOTP()
    {
        $session = $this->requestStack->getSession();

        // On génère une clé secrète
        $secretKey = $this->google2FA->generateSecretKey();

        //On stocke la clé secrète dans un tableau User
        $user = ['google2FA_secret' => $secretKey, 'email' => 'user@example.com'];

        // On stocke la variable $user dans la session
        $session->set('user', $user);

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
        $encodeQrCodeData = base64_encode($writer->writeString($qrCodeUrl));

        //On stocke le QRCode dans la session
        $currentOtp = $this->google2FA->getCurrentOtp($user['google2FA_secret']);
        $session->set('currentOtp', $currentOtp);
        $session->set('qrCode', $encodeQrCodeData);
    }
    public function getGoogle2FA()
    {
        return $this->google2FA;
    }
}
