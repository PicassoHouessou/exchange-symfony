<?php

namespace App\EventSubscriber;

use App\Event\ContactUsEvent;
use App\Event\NewslettersEvent;
use App\Event\UserEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\Event;
use Twig\Environment;

class SummaryMailSubscriber implements EventSubscriberInterface
{
    protected $mailer ;
    protected $sender ;
    protected $swiftMailer ;
    protected $twig ;
    protected $supportEmail;

    public function __construct( MailerInterface $mailer, \Swift_Mailer $swiftMailer,  $noreplyEmail, $supportEmail, Environment $twig)
    {
        $this->mailer = $mailer ;
        $this->sender = $noreplyEmail ;
        $this->swiftMailer= $swiftMailer ;
        $this->twig = $twig ;
        $this->supportEmail = $supportEmail;
    }

    public function onNewsletterAdd(NewslettersEvent $event )
    {
//        $email = (new TemplatedEmail())
//            ->from($this->sender)
//            ->to($event->getNewsletters()->getEmail())
//            ->subject('Newsletters registration success')
//
//            // path of the Twig template to render
//            ->htmlTemplate('default/newsletters_register_email.html.twig')
//        ;
//
//        $this->mailer->send($email);


        $message = (new \Swift_Message('Newsletters registration success'))
            ->setFrom($this->sender)
            ->setTo($event->getNewsletters()->getEmail())
            ->setBody(
                $this->twig->render(
                // templates/emails/registration.html.twig
                    'default/newsletters_register_email.html.twig'
                ),
                'text/html'
            )
        ;

        $this->swiftMailer->send($message);

    }

    public function onUserAdd(UserEvent $event)
    {

//        $email = (new TemplatedEmail())
//            ->from($this->sender)
//            ->to($event->getUser()->getEmail())
//            ->subject('Your verification code')
//
//            // path of the Twig template to render
//            ->htmlTemplate('registration/confirmation_email.html.twig')
//
//            // pass variables (name => value) to the template
//            ->context([
//                'expiration_date' => new \DateTime('+7 days'),
//                'activationCode' => 'foo',
//            ])
//        ;
//        $this->mailer->send($email);

        $message = (new \Swift_Message('Your verification code'))
            ->setFrom($this->sender)
            ->setTo($event->getUser()->getEmail())
            ->setBody(
                $this->twig->render(
                // templates/emails/registration.html.twig
                    'registration/confirmation_email.html.twig',
                    [
                        'expiration_date' => new \DateTime('+7 days'),
                        'activationCode' => 'foo'
                    ]
                ),
                'text/html'
            )
        ;

        $this->swiftMailer->send($message);

    }
    public function onContactUsSend(ContactUsEvent $event)
    {
        if ( ! $event instanceof ContactUsEvent) {
            return ;
        }
        $contactUs = $event->getContactUs() ;
        $message = (new \Swift_Message('You got message'))
            ->setFrom($this->sender)
            ->setTo($this->supportEmail)
            ->setBody(
                $this->twig->render(
                // templates/emails/registration.html.twig
                    'emails/contact_us.html.twig',
                    [
                        'contactUs' => $contactUs
                    ]
                ),
                'text/html'
            )
        ;

        $this->swiftMailer->send($message);

    }

    public function onConversionDo(Event $event)
    {
        if ( ! $event instanceof ConversionEvent) {
            return ;
        }
        $conversion = $event->getConversion() ;
        $message = (new \Swift_Message(" Demande d'échange de devises"))
            ->setFrom($this->sender)
            ->setTo($conversion->getEmail())
            ->setBody(
                $this->twig->render(
                // templates/emails/registration.html.twig
                    'emails/conversion.html.twig',
                    [
                        'conversion' => $conversion
                    ]
                ),
                'text/html'
            )
        ;

        $this->swiftMailer->send($message);

    }



    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            //UserEvent::NEW_USER                 => 'onUserAdd',
            NewslettersEvent::NEWSLETTERS_ADD   => 'onNewsletterAdd',
            ContactUsEvent::SEND                => 'onContactUsSend',
            ConversionEvent::CONVERSION_REQUEST => 'onConversionDo',

        ] ;
    }
}
