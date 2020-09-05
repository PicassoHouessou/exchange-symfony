<?php

namespace App\Controller;

use App\Repository\ConversionRepository;
use App\Repository\CurrencyRepository;
use App\Repository\NewslettersRepository;
use App\Repository\OnlineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
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
