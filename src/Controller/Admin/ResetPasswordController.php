<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\TooManyPasswordRequestsException;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Twig\Environment;

/**
 * @Route("/reset-admin-password")
 */
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    private $resetPasswordHelper;

    public function __construct(ResetPasswordHelperInterface $resetPasswordHelper,  $noreplyEmail , Environment $twig)
    {
        $this->resetPasswordHelper = $resetPasswordHelper;
        $this->sender = $noreplyEmail ;
        $this->twig = $twig ;
        //$this->resetPasswordHelper->
    }

    /**
     * Display & process form to request a password reset.
     *
     * @Route("", name="app_admin_forgot_password_request")
     */
    public function request(Request $request, MailerInterface $mailer, \Swift_Mailer $swift_Mailer): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->processSendingPasswordResetEmailWithSwiftMailer(
                $form->get('email')->getData(),
                $swift_Mailer
            );
        }

        return $this->render('admin/reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Confirmation page after a user has requested a password reset.
     *
     * @Route("/check-email", name="app_admin_check_email")
     */
    public function checkEmail(): Response
    {
        // We prevent users from directly accessing this page
        if (!$this->canCheckEmail()) {
            return $this->redirectToRoute('app_admin_forgot_password_request');
        }

        return $this->render('admin/reset_password/check_email.html.twig', [
            'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
        ]);
    }

    /**
     * Validates and process the reset URL that the user clicked in their email.
     *
     * @Route("/reset/{token}", name="app_admin_reset_password")
     */
    public function reset(Request $request, UserPasswordEncoderInterface $passwordEncoder, string $token = null): Response
    {
        if ($token) {
            // We store the token in session and remove it from the URL, to avoid the URL being
            // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_admin_reset_password');
        }

        $token = $this->getTokenFromSession();
        if (null === $token) {
            throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        }catch (TooManyPasswordRequestsException $e)
        {
            $this->addFlash('reset_password_error', sprintf(
                '%s Please try after %s', $e->getReason(),
                date ('i ', $e->getRetryAfter() ) .'minutes'
            ));

            return $this->redirectToRoute('app_forgot_password_request');

        }
        catch (ResetPasswordExceptionInterface $e) {
            $this->addFlash('reset_password_error', sprintf(
                'There was a problem validating your reset request - %s',
                $e->getReason()
            ));

            return $this->redirectToRoute('app_admin_forgot_password_request');
        }

        // The token is valid; allow the user to change their password.
        $form = $this->createForm(ResetPasswordType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // A password reset token should be used only once, remove it.
            $this->resetPasswordHelper->removeResetRequest($token);

            // Encode the plain password, and set it.
            $encodedPassword = $passwordEncoder->encodePassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Your password are successfuly changed');

            // The session is cleaned up after the password has been changed.
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): RedirectResponse
    {
        $user = $this->getDoctrine()->getRepository(Admin::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Marks that you are allowed to see the app_check_email page.
        $this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->redirectToRoute('app_admin_check_email');
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            $this->addFlash('reset_password_error', sprintf(
                'There was a problem handling your password reset request - %s',
                $e->getReason()
            ));

            return $this->redirectToRoute('app_admin_forgot_password_request');
        }

        $email = (new TemplatedEmail())
            ->from(new Address('no-reply@dmvinvestmentgroup.com', 'DMV Admin Reset Password'))
            ->to($user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
                'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
            ])
        ;

        $mailer->send($email);

        return $this->redirectToRoute('app_admin_check_email');
    }

    private function processSendingPasswordResetEmailWithSwiftMailer(string $emailFormData, \Swift_Mailer $mailer): RedirectResponse
    {
        $user = $this->getDoctrine()->getRepository(Admin::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Marks that you are allowed to see the app_check_email page.
        $this->setCanCheckEmailInSession();

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            $this->addFlash('error', "This email could not be found. May be you should create account");
            return $this->redirectToRoute('app_admin_forgot_password_request');

        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            $this->addFlash('reset_password_error', sprintf(
                'There was a problem handling your password reset request - %s',
                $e->getReason()
            ));

            return $this->redirectToRoute('app_admin_forgot_password_request');
        }

        $message = (new \Swift_Message('Your password reset request'))
            ->setFrom($this->sender)
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('reset_password/admin_email.html.twig',[
                        'resetToken' => $resetToken,
                        'tokenLifetime' => $this->resetPasswordHelper->getTokenLifetime(),
                    ]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);

        return $this->redirectToRoute('app_admin_check_email');
    }
}
