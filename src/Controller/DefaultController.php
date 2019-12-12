<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Mailer\SimpleMailer;





class DefaultController extends AbstractController
{

    private SimpleMailer $mailer;

    public function index(string $name): Response{
        return $this->render("index.html.twig", [
            'name' => $name,
        ]);
    }  

    public function __construct(SimpleMailer $mailer){
        $this->mailer =$mailer;
    }
    
    /*public function mail(): Response{
        $mailer = new simpleMailer();
        $mailer->send('john@doe.com', 'Hello John');
        return new Response('Ok');
    }*/

    public function mail(): Response{
        $this->mailer->send('john@doe.com', 'Hello John');

        return new Response('Ok');
    }    
}