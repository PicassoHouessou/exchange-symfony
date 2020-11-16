<?php


namespace App\Controller\Admin;


use App\Entity\ContactUs;
use App\Repository\ContactUsRepository;
use Doctrine\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ContactUsController extends AbstractController
{

    /**
     * @Route("/admin/contact-us/all" , name="app_admin_contact_us_all")
     */
    public function contactUs(Request $request, SessionInterface $session, ContactUsRepository $contactUsRepository)
    {
        $session->set('app_menu_open', "MENU_CONTACT_US");
        $session->set('app_sub_menu_active', "MENU_CONTACT_US_ALL");

        $data = $contactUsRepository->findAll();

        $contacts = $data ;

        return $this->render('admin/contactUs/contact_us.html.twig', [
            'contacts'      => $contacts
        ]) ;
    }

    /**
     * @Route("/admin/contact-us/unread" , name="app_admin_contact_us_unread")
     */
    public function unread(Request $request, SessionInterface $session, ContactUsRepository $contactUsRepository)
    {
        $session->set('app_menu_open', "MENU_CONTACT_US");
        $session->set('app_sub_menu_active', "MENU_CONTACT_US_UNREAD");

        $data = $contactUsRepository->findAll();

        $contacts = $data ;

        return $this->render('admin/contactUs/contact_us.html.twig', [
            'contacts'      => $contacts
        ]) ;
    }

    /**
     * @Route("/admin/contact-us/{id}", name="app_admin_contact_us_show")
     */

    public function show(Request $request, ContactUs $contactUs , SessionInterface $session,  $id)
    {
        $session->set('app_menu_open', "MENU_CONTACT_US");
        $session->set('app_sub_menu_active', "");

        return $this->render('admin/contactUs/contact_us_read.html.twig', [
            'contactUs'     => $contactUs
        ]) ;
    }

    /**
     * Pour supprimer les messages de contact us
     * @Route("/admin/contact-us/delete/{id}" , name="app_admin_contact_us_delete")
     */

    public function delete( ContactUs $contactUs, Request $request,SessionInterface $session, ObjectManager $manager)
    {
        $contactUsSender = $contactUs->getSender() ;
        $manager->remove($contactUs);
        $manager->flush();
        $this->addFlash('success', "The message of ". $contactUsSender. " was removed");

        return $this->redirectToRoute('app_admin_contact_us_all') ;

    }
}