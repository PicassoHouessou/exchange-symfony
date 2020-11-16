<?php

namespace App\Controller;

use App\Entity\ContactUs;
use App\Entity\Conversion;
use App\Entity\Newsletters;
use App\Event\ContactUsEvent;
use App\Event\NewslettersEvent;
use App\EventSubscriber\ConversionEvent;
use App\Form\ContactUsType;
use App\Form\ConversionType;
use App\Form\NewslettersRegisterType;
use App\Form\NewslettersType;
use App\Repository\CurrencyRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class DefaultController extends AbstractController
{
    /**
     * @Route("/_embed_accept_cookies", name="app_embed_accept_cookies")
     */
    public function acceptCookies(Request $request)
    {
        if ($request->query->get('acceptCookie')) {
            $cookie = Cookie::create('ACCEPT_COOKIES')
                ->withValue(true)
                ->withExpires(strtotime('+2 weeks'));

            //$request->cookies->set('ACCEPT_COOKIES', true);
            $response = $this->json([
                'message' => 'Complete success operation'
            ], 200
            );

            $response->headers->setCookie($cookie);

            return $response;
        }

        $acceptCookie = $request->cookies->get('ACCEPT_COOKIES');

        $show = null;
        if (!$acceptCookie) {
            $show = true;
        }

        return $this->render('default/_embed_accept_cookies.html.twig', [
            'show' => $show,
        ]);
    }


    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request, CurrencyRepository $currencyRepository)
    {
        $currency = $currencyRepository->findAll();
        return $this->render('default/index.html.twig', [
            'activeHeaderMenu' => 'activeHome',
            'currency'         => $currency,
        ]);
    }

    /**
     * @Route("/_embed_newsletter/add", name="app_embed_newsletters_add" )
     */
    public function embedNewsletter(Request $request)
    {
        $newsletter = new Newsletters() ;

        $form = $this->get('form.factory')->createNamed('', NewslettersType::class) ;

        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager() ;
            $manager->persist($newsletter);
            $manager->flush();
            $this->addFlash('success', "Vous faites partir maintenant de notre newsletter");

            return $this->redirectToRoute('app_home') ;

        }
        else if ($form->isSubmitted() && !$form->isValid())
        {
            $this->addFlash('error', "Veuillez bien renseigner votre email");
            return $this->redirectToRoute('app_home') ;

        }

        return  $this->render('default/_embed_newsletter.html.twig',[
                'newsletterForm'              => $form->createView(),
        ]) ;
    }

    /**
     * @Route("/currency/all", name="app_currency_all")
     */

    public function allCurrency(SerializerInterface $serializer, CurrencyRepository $currencyRepository)
    {
        $data = $currencyRepository->findAll();

        if (!$data) {
            return $this->json([
                'message' => "Rien n'a été trouvé"
            ], Response::HTTP_NOT_FOUND);

        }

        $currency = $serializer->serialize($data, 'json');

        return $this->json([
            $currency,
        ], Response::HTTP_OK);
    }

    /**
     * @Route("/purchase", name="app_purchase")
     */
    public function purchase (Request $request, ObjectManager $manager, EventDispatcherInterface $eventDispatcher)
    {
        $conversion = new Conversion() ;

        $form = $this->get('form.factory')->createNamed('', ConversionType::class, $conversion) ;

        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid())
        {
            $conversion->setCreatedAt(new \DateTime());
            $manager->persist( $conversion);
            $manager->flush();
            $this->addFlash('success', 'Votre demande a été effectué. Nous allons vous informé');

            $conversionEvent = new ConversionEvent($conversion) ;
            $eventDispatcher->dispatch($conversionEvent, ConversionEvent::CONVERSION_REQUEST) ;

            return  $this->redirectToRoute('app_home') ;
        }
        return $this->render('default/purchase.html.twig', [
            'conversionForm'      => $form->createView() ,
        ]) ;
    }
}


