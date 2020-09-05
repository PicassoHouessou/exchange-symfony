<?php


namespace App\Controller\Admin;


use App\Entity\Admin;
use App\Form\AdminRegistrationFormType;
use App\Security\AdminLoginFormAuthenticator;
use App\Security\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/admin/registration", name="app_admin_register")
     */

    public function registration( Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, AdminLoginFormAuthenticator $authenticator , SessionInterface $session, EmailVerifier $emailVerifier)
    {
        $admin = new Admin();

        $form = $this->get('form.factory')->createNamed('',AdminRegistrationFormType::class, $admin);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $plainPassword = $form->get('plainPassword')->getData() ;
            $confirmPlainPassword = $form->get('confirmPlainPassword')->getData() ;

            if ($plainPassword !== $confirmPlainPassword)
            {
                $this->addFlash('error', 'The passwords are not same');
                return $this->render('admin/registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }

            // encode the plain password
            $admin->setPassword(
                $passwordEncoder->encodePassword(
                    $admin,
                    $form->get('plainPassword')->getData()
                )
            );
            $admin->setCreatedAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($admin);
            $entityManager->flush();

            // generate a signed url and email it to the user
//            $emailVerifier->sendEmailConfirmation('app_verify_email', $admin,
//                (new TemplatedEmail())
//                    ->from(new Address('no-reply@dmvinvestmentgroup.com', 'No reply DMV'))
//                    ->to($admin->getEmail())
//                    ->subject('Please Confirm your Email')
//                    ->htmlTemplate('registration/confirmation_email.html.twig')
//            );

            $file = 'registration/confirmation_email.html.twig' ;
            $emailVerifier->sendEmailConfirmationWithSwiftMailer('app_verify_email', $admin,
                (new \Swift_Message('Please Confirm your Email'))
                    ->setFrom('no-reply@dmvinvestmentgroup.com')
                    ->setTo($admin->getEmail()) , $file) ;


            $session->set('registerEmail', $admin->getEmail()) ;

            return $this->redirectToRoute('app_register_confirm') ;
        }

        return $this->render('admin/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}