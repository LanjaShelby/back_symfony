<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Json;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Validation;

class SendMailController extends AbstractController
{
   /* #[Route('/send/mail', name: 'app_send_mail')]
    public function index(): Response
    {
        return $this->render('send_mail/index.html.twig', [
            'controller_name' => 'SendMailController',
        ]);

 MERCURE_PUBLISHER_JWT_KEY='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.vpV9_d4uQeqNczcI2ebcHilEGHu5OxY5rozFIBVEdxw' / 
 MERCURE_SUBSCRIBER_JWT_KEY='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.vpV9_d4uQeqNczcI2ebcHilEGHu5OxY5rozFIBVEdxw'/
./mercure run --config dev.Caddyfile
./mercure run --config /etc/mercure/Caddyfile
./mercure run --config /etc/mercure/ .env
export MERCURE_PUBLISHER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!'/
export MERCURE_SUBSCRIBER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!'


MERCURE_PUBLISHER_JWT_KEY='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.szEBOkhwg1OyIQnz8heYkAbUNzhubuXk3ILgUvmCoAg' \
MERCURE_SUBSCRIBER_JWT_KEY='eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InB1Ymxpc2giOlsiKiJdfX0.szEBOkhwg1OyIQnz8heYkAbUNzhubuXk3ILgUvmCoAg' \
./mercure run --config dev.Caddyfile --listen :4000

MERCURE_PUBLISHER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!' \
MERCURE_SUBSCRIBER_JWT_KEY='!ChangeThisMercureHubJWTSecretKey!' \



    } */
    #[Route('/send/mail', name: 'app_send_mail')]
    public function testMail(MailerInterface $mailer): JsonResponse
    {
        $emailAddress = "randriamanjaryherilanja@gmail.com";
        $validator = Validation::createValidator();
        $violations = $validator->validate(
            $emailAddress,
            new EmailConstraint()
        );
    
        if (count($violations) > 0) {
            return new JsonResponse([
                'status' => 'Error',
                'message' => 'Invalid email format.',
            ], 400);
        }
    
        // Vérification DNS pour le domaine
        $domain = substr(strrchr($emailAddress, "@"), 1);
        if (!checkdnsrr($domain, 'MX')) {
            return new JsonResponse([
                'status' => 'Error',
                'message' => 'The email domain does not exist.',
            ], 400);
        }
        try {
        $email = (new Email())
            ->from('bonasenesa@gmail.com')
            ->to($emailAddress)
            ->subject('Test Mail Symfony Gmail')
            ->text('Ceci est un test d\'envoi de mail via Gmail et Symfony.')
            ->html("
                <p>Bonjour <strong></strong>,</p>
                <p>Voici vos identifiants :</p>
                <ul>
                    <li><strong>Nom d'utilisateur :</strong> </li>
                    <li><strong>Mot de passe :</strong> </li>
                </ul>
                <p>Merci de vous connecter sur notre <a href='https://example.com/login'>site</a>.</p>
            ");

        $mailer->send($email);

        return $this->json(['status' => 'Email sent successfully']);
    } catch (TransportExceptionInterface $e) {
        // Gestion de l'erreur de transport (SMTP, adresse invalide, etc.)
        return $this->json([
            'status' => 'Error',
            'message' => 'Failed to send email: ' . $e->getMessage(),
        ], 500);
    }
    }
}
