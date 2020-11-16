<?php

namespace App\Controller;

use App\Entity\Newsletters;
use App\Entity\Notification;
use App\Entity\User;
use App\Entity\UserInfo;
use App\Event\NewslettersEvent;
use App\Event\NotificationEvent;
use App\Form\RegistrationFormType;
use App\Form\UserInfoAvatarType;
use App\Form\UserInfoType;
use App\Repository\UserInfoRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use App\Security\LoginFormAuthenticator;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator , SessionInterface $session, EventDispatcherInterface $eventDispatcher, UrlGeneratorInterface $urlGenerator ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_user');
        }

        $user = new User();
        $userInfo = new userInfo();
        $user->setInfo($userInfo);

        $form = $this->get('form.factory')->createNamed('',RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setIsEnabled(true) ;
            $user->setCreatedAt(new \DateTime());

            $manager = $this->getDoctrine()->getManager();
            $manager->persist($user);

            if ($request->request->get('agreeNewsletters')){
                $email = $user->getEmail() ;
                $exist = $manager->getRepository(Newsletters::class)->findOneBy(['email'=> $email]) ;
                if (!$exist){
                    $newsletters = new Newsletters();
                    $newsletters->setEmail($email) ;
                    $newsletters->setCreatedAt(new \DateTime());
                    $manager->persist($newsletters);

                    $newslettersEvent = new NewslettersEvent($newsletters);
                    $eventDispatcher->dispatch($newslettersEvent, NewslettersEvent::NEWSLETTERS_ADD) ;

                    //Envoi de la premiere notification

                    $notification = new Notification() ;
                    $notification
                        ->setUser($user)
                        ->setIsRead(false)
                        ->setTitle("Finalisez votre inscription")
                        ->setCreatedAt(new \DateTime())
                        ->setContent("Finalisez votre inscription pour profiter pleinement de toutes les fonctionnalités")
                        ->setLink($urlGenerator->generate('app_register_step_account_one',[], UrlGeneratorInterface::ABSOLUTE_URL));

                    $notificationEvent = new NotificationEvent($notification);

                    $eventDispatcher->dispatch($notificationEvent, NotificationEvent::NOTIFICATION_ADD);

                }
            }
            $manager->flush();

            $file = 'registration/confirmation_email.html.twig' ;
            $this->emailVerifier->sendEmailConfirmationWithSwiftMailer('app_verify_email', $user,
                (new \Swift_Message('Please Confirm your Email'))
                    ->setFrom('no-reply@dmvinvestmentgroup.com')
                    ->setTo($user->getEmail()) , $file) ;

            //$this->addFlash('success', 'You have account now. Please check your email. We send you validation code');

            $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );

            $session->set('registerEmail', $user->getEmail()) ;

            return $this->json([
                'message'   => 'You have account now. Please check your email. We send you validation code'
            ], 200) ;

            return $this->redirectToRoute('app_register_confirm') ;
        }
        else if ($form->isSubmitted() && !$form->isValid())
        {
            $message = $form->getErrors(true, false) ;
            return $this->json([
                'message'=> 'They have some errors in your form '. $message
            ], 401);
        }


        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'user'             => $user,
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register_step_account_one');
    }


    /**
     * @Route("/register/confirm", name="app_register_confirm")
     */

    public function registerConfirm(Request $request)
    {
        return $this->render('registration/confirm.html.twig');
    }

    /**
     * @Route("/register/resend-email", name="app_register_resend_email")
     */
    public function resendEmail()
    {
        $user =$this->getUser() ;
        if ($user === null){
            $this->denyAccessUnlessGranted('ROLE_USER');
        }

        /*
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
            (new TemplatedEmail())
                ->from(new Address('no-reply@dmvinvestmentgroup.com', 'No reply DMV'))
                ->to($user->getEmail())
                ->subject('Please Confirm your Email')
                ->htmlTemplate('registration/confirmation_email.html.twig')
        );
        */

        $file = 'registration/confirmation_email.html.twig' ;
        $this->emailVerifier->sendEmailConfirmationWithSwiftMailer('app_verify_email', $user,
            (new \Swift_Message('Please Confirm your Email'))
                ->setFrom('no-reply@dmvinvestmentgroup.com')
                ->setTo($user->getEmail()) , $file) ;

        $this->addFlash('success', 'Please check your email.');
        return $this->redirectToRoute('app_register_confirm');
    }


    /**
     *@Route("/register/step-account/one", name="app_register_step_account_one")
     */
    public  function registerStepAccountOne(Request $request, ObjectManager $manager)
    {
        //return $this->redirectToRoute('app_user');
        $user = $this->getUser();

        if ($user === null){
            $this->denyAccessUnlessGranted("ROLE_USER");
        }
        $userInfo = $user->getInfo();

        $form = $this->get('form.factory')->createNamed('',UserInfoType::class, $userInfo);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() && $request->isMethod('POST'))
        {
            $user->setInfo($userInfo);

            $manager->persist($user);
            $manager->flush();

            return $this->json([
                "code"      => 200,
                "message"   => "Success your information are saved. "
            ], 200 );
            //return $this->redirectToRoute("app_register_step_account_two");
        } else if ($form->isSubmitted() && !$form->isValid())
        {
            $error = $form->getErrors(true, false);
            return $this->json([
                "code"  => 401,
                "message"   => "Please check your form field correctly ".$error
            ], 401) ;
        }

        return $this->render('registration/register_step_account_1.html.twig',[
            'userInfoForm'      => $form->createView()
        ]) ;
    }

    /**
     *@Route("/register/step-account/two", name="app_register_step_account_two")
     */
    public  function registerStepAccountTwo(Request $request, ObjectManager $manager, UserInfoRepository $userInfoRepository)
    {
        return $this->redirectToRoute('app_user');
        $user = $this->getUser();
        if ($user === NULL)
        {
            $this->addFlash('flash', 'Please login');
            $this->denyAccessUnlessGranted("ROLE_USER");
        }

        $userInfo =$user->getInfo();
        if (is_null($userInfo) )
        {
            //$this->addFlash('flash', 'Please login');
            return $this->redirectToRoute('app_register_step_account_one');
        }
        if ($userInfo->getAvatarName()!==null)
        {
            return $this->redirectToRoute('app_user');
        }
        $id = $userInfo->getId() ;
        $userInfo = $userInfoRepository->findOneBy(['id'=>$id]) ;


        $form = $this->createForm(UserInfoAvatarType::class, $userInfo);
        //$form = $this->get('form.factory')->createNamed('',UserInfoAvatarType::class, $userInfo);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($userInfo);
            $manager->flush();
            return $this->redirectToRoute("app_user");
        }

        return $this->render('registration/register_step_account_2.html.twig',[
            'userInfoForm'      => $form->createView()
        ]) ;
    }

    /**
     *@Route("/register/step-account/three", name="app_register_step_account_three")
     */
    public  function registerStepAccountThree(Request $request, ObjectManager $manager)
    {
        $userInfo = new UserInfo();
        $form = $this->createForm(UserInfoType::class, $userInfo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            return $this->redirectToRoute("app_user");
        }

        return $this->render('registration/register_step_account_3.html.twig',[
            "form"  => $form->createView()
        ]) ;
    }
    /*
     * @Route("/register", name="app_register")
     *
    public function register (Request $request, ObjectManager $manager, EventDispatcherInterface $eventDispatcher, UserRepository $userRepository, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $userForm = $this->createForm(UserType::class, $user);

        $userForm->handleRequest($request) ;
        if ($userForm->isSubmitted() && $userForm->isValid())
        {
            $userRepository->findOneBy([
                "email"     => $user->getEmail()
            ]) ;

            if ($user){
                /*
               return $this->json([
                   "code"  => 200,
                   "message"   => "This user exist"
               ]);

                $this->addFlash('error', "This user exists");
            } else {
                $user->setPassword($encoder->encodePassword($user, $user->getPassword())) ;
                $user->setCreatedAt(new \DateTime()) ;
                $manager->persist($user);
                $manager->flush();


                $event = new UserEvent($user);
                $eventDispatcher->dispatch($event, UserEvent::NEW_USER);
                return $this->redirectToRoute('app_register_confirm');
            }

        } /* else if ($userForm->isSubmitted() && ! $userForm->isValid() )
        {
            $this->addFlash('error', "This user exists");
        }

        return $this->render('security/register.html.twig', [
            'userForm'      => $userForm->createView()
        ]) ;
    }
    */



}
