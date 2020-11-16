<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Form\AdminType;
use App\Form\ChangePasswordType;
use App\Repository\AdminRepository;
use App\Repository\ConversionRepository;
use App\Repository\CurrencyRepository;
use App\Repository\NewslettersRepository;
use App\Repository\OnlineRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AdminController extends AbstractController
{

    /**
     * @Route("/admin/profile" , name="app_admin_profile")
     */

    public function profile(Request $request, ObjectManager $manager, AdminRepository $adminRepository, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $admin = $this->getUser() ;

        //$avatar = new Admin();

        $form = $this->createForm(AdminType::class, $admin) ;
        $form2 = $this->createFormBuilder($admin)
            ->add('avatarFile', VichImageType::class)
            ->getForm() ;


        $form3 = $this->createForm(ChangePasswordType::class) ;


        $form->handleRequest($request);
        $form2->handleRequest($request);
        $form3->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($admin);
            $manager->flush();
            $this->addFlash('success', "Your profile are updated");
        } elseif ($form2->isSubmitted() && $form2->isValid()) {
            $manager->persist($admin);
            $manager->flush();
            $this->addFlash('success', "Your profile are updated");
        } else if ($form3->isSubmitted() && $form3->isValid())
        {
            $userModel = $form3->getData();

            $checkPassword = $userPasswordEncoder->isPasswordValid($admin, $userModel->oldPlainPassword ) ;

            if ($checkPassword === true) {
                $admin->setPassword($userPasswordEncoder->encodePassword($admin, $userModel->newPlainPassword)) ;
                $manager->persist($admin);
                $manager->flush();
                $this->addFlash('success', 'Your password are changed');
                unset($form3);
                $form3 = $this->createForm(ChangePasswordType::class) ;

            } else {
                $this->addFlash('error', "Please enter a good old password");
            }
        }

        return $this->render('admin/profile/profile.html.twig', [
            'adminForm' => $form->createView(),
            'changePasswordForm'    => $form3->createView(),
            'admin'     => $admin ,
        ]);
    }

    /**
     * @Route("/admin/dashboard", name="app_admin")
     */
    public function index(Request $request, OnlineRepository $onlineRepository, CurrencyRepository $currencyRepository, SessionInterface $session,  NewslettersRepository $newslettersRepository, ConversionRepository $conversionRepository )
    {
        $session->set('app_menu_open', "MENU_DASHBOARD_GENERAL");
        $session->set('app_sub_menu_active', "MENU_DASHBOARD_GENERAL");

        $totalVisitors = $onlineRepository->count([]);
        $totalCurrency = $currencyRepository->count( []);
        $totalNewsletters = $newslettersRepository->count([]);
        $totalConversion = $conversionRepository->count([]);


        return $this->render('admin/index.html.twig', [
            'totalVisitors'         => $totalVisitors,
            'totalCurrency'         => $totalCurrency,
            'totalNewsletters'      => $totalNewsletters,
            'totalConversion'       => $totalConversion,
        ]);
    }
}
