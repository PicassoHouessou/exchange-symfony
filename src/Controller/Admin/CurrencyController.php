<?php


namespace App\Controller\Admin;

use App\Entity\Conversion;
use App\Entity\Currency;
use App\Form\CurrencyType;
use App\Repository\ConversionRepository;
use App\Repository\CurrencyRepository;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController ;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CurrencyController extends AbstractController
{

    #[Route(path: '/admin/currency/all', name: 'app_admin_currency_all')]
    public function all(Request $request, CurrencyRepository $currencyRepository, PaginatorInterface $paginator, SessionInterface $session)
    {
        $session->set('app_menu_open', "MENU_CURRENCY");
        $session->set('app_sub_menu_active', "MENU_CURRENCY_ALL");

        $page = $request->query->get('page') ;
        if (!$page)
        {
            $page = 1 ;
        }


        $data = $currencyRepository->findAll() ;

        $pagination = $paginator->paginate($data, $page) ;

        return $this->render('admin/currency/all.html.twig', [
            'pagination'        => $pagination,
        ]);
    }


    #[Route(path: '/admin/currency/add', name: 'app_admin_currency_add')]
    public function add (Request $request, SessionInterface $session)
    {
        $session->set('app_menu_open', "MENU_CURRENCY");
        $session->set('app_sub_menu_active', "MENU_CURRENCY_ADD");

        $currency = new Currency() ;
        $form = $this->createForm(CurrencyType::class, $currency);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($currency);
            $manager->flush();
            $this->addFlash('success', 'La devise a été créé');
            return $this->redirectToRoute('app_admin') ;
        }

        return $this->render('admin/currency/add.html.twig',[
            'form'  => $form->createView(),
        ]) ;
    }

    #[Route(path: '/admin/currency/edit/{id}', name: 'app_admin_currency/edit')]
    public function edit (Request $request , Currency $currency)
    {
        $form = $this->createForm(CurrencyType::class, $currency) ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $manager = $this->getDoctrine()->getManager();

            $manager->persist($currency);
            $manager->flush();
            $this->addFlash('success', 'La devise a été modifiée');
            return $this->redirectToRoute('app_admin') ;
        }

        return $this->render('admin/currency/edit.html.twig', [
            'form'  => $form->createView(),
        ]);

    }

    public function getAjax()
    {


    }

    #[Route(path: '/admin/currency/delete/{id}', name: 'app_admin_currency_delete')]
    public function delete (Currency $currency , ObjectManager $manager)
    {
        $manager->remove($currency) ;
        $manager->flush();

        $this->addFlash('success', "La devise ". $currency->getCode() . " a été supprimé");

        return $this->redirectToRoute('app_admin') ;
    }

    #[Route(path: '/admin/exchange', name: 'app_admin_conversion_all')]
    public function conversion(Request $request , ConversionRepository $conversionRepository, PaginatorInterface $paginator)
    {
        $page = $request->query->getInt('page', 1 ) ;
        $data = $conversionRepository->findBy([], ['createdAt' => 'desc']) ;

        $pagination = $paginator->paginate($data, $page) ;
        return $this->render('admin/currency/conversion_all.html.twig', [
            'pagination'   => $pagination,
        ]) ;
    }

    #[Route(path: '/admin/conversion/delete/{id}', name: 'app_admin_conversion_delete')]
    public function deleteConversion(Conversion $conversion , ObjectManager $manager)
    {
        $manager->remove($conversion);
        $manager->flush();
        $this->addFlash('success', "Super l'échange a été supprimé");

        return $this->redirectToRoute('app_admin_conversion_all') ;
    }
}