<?php

namespace App\Controller\Admin;

use App\Repository\NewslettersRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class NewslettersController extends AbstractController
{
    #[Route(path: '/admin/newsletters', name: 'app_admin_newsletters')]
    public function index(Request $request, SessionInterface $session, UserRepository $userRepository, NewslettersRepository $newslettersRepository , PaginatorInterface $paginator)
    {
        $session->set('app_menu_open', "MENU_NEWSLETTERS");
        $session->set('app_sub_menu_active', "MENU_NEWSLETTERS_ALL");


        $page = $request->get('page') ?: 1 ;
        $limit = $request->get('limit') ?: 10 ;

        $data = $newslettersRepository->findAll();

        $newsletters = $paginator->paginate($data, $page, $limit) ;
        $newsletters = $paginator->paginate($data);
        return $this->render('admin/newsletters/newsletters.html.twig', [
            'newsletters'   => $newsletters ,
        ]);
    }
}
