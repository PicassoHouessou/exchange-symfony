<?php

namespace App\EventSubscriber;


use App\Event\NewslettersEvent;
use App\Event\UserEvent;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use Twig\Environment;

class SummaryMailSubscriber implements EventSubscriberInterface
{
    protected $mailer;
    protected $sender;
    protected $twig;
    protected $supportEmail;

    public function __construct(MailerInterface $mailer, $noreplyEmail, $supportEmail, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->sender = $noreplyEmail;
        $this->twig = $twig;
        $this->supportEmail = $supportEmail;
    }

    public function onNewsletterAdd(NewslettersEvent $event)
    {

        $email = (new TemplatedEmail())
            ->from($this->sender)
            ->to($event->getNewsletters()->getEmail())
            ->subject('Newsletters registration success')

            // path of the Twig template to render
            ->htmlTemplate('default/newsletters_register_email.html.twig');
        $this->mailer->send($email);


    }

    public function onUserAdd(UserEvent $event)
    {

        $email = (new TemplatedEmail())
            ->from($this->sender)
            ->to($event->getUser()->getEmail())
            ->subject('Your verification code')

            // path of the Twig template to render
            ->htmlTemplate('registration/confirmation_email.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'activationCode' => 'foo',
            ]);
        $this->mailer->send($email);
    }


    public function onConversionDo(Event $event)
    {
        if (!$event instanceof ConversionEvent) {
            return;
        }
        $conversion = $event->getConversion();
        $email =
            (new TemplatedEmail())
                ->from($this->sender)
                ->to($conversion->getEmail())
                ->subject("Demande d'échange de devises")

                // path of the Twig template to render
                ->htmlTemplate('emails/conversion.html.twig')
                // pass variables (name => value) to the template
                ->context([
                    'conversion' => $conversion
                ]);
        $this->mailer->send($email);

        /*
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
*/
    }


    public static function getSubscribedEvents()
    {
        // TODO: Implement getSubscribedEvents() method.
        return [
            //UserEvent::NEW_USER                 => 'onUserAdd',
            NewslettersEvent::NEWSLETTERS_ADD => 'onNewsletterAdd',
            ConversionEvent::CONVERSION_REQUEST => 'onConversionDo',

        ];
    }
}
