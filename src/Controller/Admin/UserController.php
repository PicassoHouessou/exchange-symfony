<?php


namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/user/all" , name="app_admin_user_all")
     */
    public function all(Request $request, UserRepository $userRepository, SessionInterface $session, PaginatorInterface $paginator)
    {

        $session->set('app_sub_menu_active', "MENU_USER_ALL");
        $session->set('app_menu_open', "MENU_USER");

        $session->set('app_last_route_uri', $request->getUri());
        $page = $request->get('page') ? $request->get('page') : 1 ;
        $limit = $request->get('limit') ? $request->get('limit') : 500 ;

        $data = $userRepository->findBy(['isEnabled'=> true], ['createdAt'=> 'DESC']) ;

        //$users =  $paginator->paginate($data, $page, $limit);
        $users = $data ;

        return $this->render('admin/user/all.html.twig', [
            'users'     => $users
        ]) ;
    }

    /**
     * @Route("/admin/user/disable", name="app_admin_user_disable" )
     *
     */
    public function disable(Request $request, SessionInterface $session,  UserRepository $userRepository, PaginatorInterface $paginator)
    {
        $session->set('app_sub_menu_active', "MENU_USER_DISABLE");
        $session->set('app_menu_open', "MENU_USER");

        $session->set('app_last_route_uri', $request->getUri());

        $data = $userRepository->findBy(['isEnabled' => false], ['createdAt'=> 'DESC']) ;
        $users = $data ;

        return $this->render('admin/user/disable.html.twig', [
            'users'     => $users,
        ]) ;
    }

    /**
     * @Route("/admin/user/delete/{id}", name="app_admin_user_delete")
     */
    public function delete(Request $request, SessionInterface $session, User $user, UserRepository $userRepository , ObjectManager $manager, $id)
    {
        $lastRouteURI = $session->get('app_last_route_uri') ;

        $manager->remove($user);
        $manager->flush();

        $this->addFlash('success', "User  are correctly remove");

        return $this->redirect($lastRouteURI) ;
    }

    /**
     * @Route("/admin/user/info/ajax", name="app_admin_user_info_ajax")
     */
    public function infoAjax(Request $request , UploaderHelper $helper , UserRepository $userRepository)
    {
        $id = $request->get('id');
        if (!$id){
            return $this->json([
                'code'      =>'200',
                'message'   => 'Not Found'
            ], 404) ;
        }

        $user = $userRepository->findOneBy(['id' => $id]) ;

        if ($user && $user->getInfo() !== null)
        {
            $userInfo = $user->getInfo() ;
            $avatarNameFQN = $helper->asset($userInfo, 'avatarFile') ;

            return $this->json([
                'code'          => 200,
                'createdAt'     =>  $user->getCreatedAt()->format('Y-m-d H:i:s'),
                'fullName'      => $userInfo->getFirstName() .' '. $userInfo->getLastName(),
                'isEnabled'     => $user->getIsEnabled(),
                'isComplete'   => $userInfo->isComplete (),
                'userAvatar'    => $avatarNameFQN
            ]) ;

        }

        return $this->json([
            'code'  => 200,
            'message' => 'error. Please retry'
        ]) ;

    }

    /**
     * @Route("/admin/user/disable/ajax", name="app_admin_user_disable_ajax")
     */
    public function disableAjax(Request $request , ObjectManager $manager, UserRepository $userRepository)
    {
        $id = $request->get('id');
        if (!$id){
            return $this->json([
                'code'      =>'200',
                'message'   => 'Not Found'
            ], 404) ;
        }

        $user = $userRepository->findOneBy(['id' => $id]) ;

        if ($user )
        {
            $isEnabled = ( $user->getIsEnabled() === true ) ? false : true ;
            $user->setIsEnabled($isEnabled) ;
            $manager->persist($user);
            $manager->flush();

            return $this->json([
                'code'          => 200,
                'isEnabled'     => $user->getIsEnabled(),
            ]) ;
        }

        return $this->json([
            'code'  => 200,
            'message' => 'error. Please retry'
        ]) ;

    }


}