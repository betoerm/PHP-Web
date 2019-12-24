<?php

namespace App\Controller;

use App\Entity\Post;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class PostController {
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;


    public function __construct(EntityManagerInterface $entityManager, SerializerInterface $serializer, 
    ValidatorInterface  $validator){
        $this->entityManager = $entityManager;  
        $this->serializer = $serializer; 
        $this->validator = $validator;
    }

    //Anoatação do método create - só permite acessar o metódo pelo metódo POST
    /**
     * @Route("/posts", methods={"POST"})
     */
    /*
     public function create(): Response {
        //Doctrine rodando
         //Instancia um novo objeto Post
         $post = new Post("Minha primeira aplicação com Symfony", "Descrição da minha aplicação");
         //persistência do post - 
         $this->entityManager->persist($post);
         //gera os inserts 
         $this->entityManager->flush(); 
                  
         return new Response ("Ok", Response::HTTP_CREATED);
     }*/

     //Recebe os dados da requisição
     //Request - camada de abstração do symfony
     public function create(Request $request): Response {
        /* //json_decode - transforma a string em um array associativo
        $data = json_decode($request->getContent(), true);
        $post = new Post($data['title'], $data['description']);         
        */

        $post = $this->serializer->deserialize($request->getContent(), Post::class, 'json');

        $errors = $this->validator->validate($post);

        if (count($errors)){
            throw new ValidationException($errors);
        }

        //persistência do post - 
        $this->entityManager->persist($post);
        //gera os inserts 
        $this->entityManager->flush(); 
                
        return new Response ("Ok", Response::HTTP_CREATED);
    }

    /**
     * @Route("/posts/{id}", methods={"GET"})
     */
    public function details(int $id): Response {
        /** @var Post $post */        
        
        $post = $this->entityManager->getRepository(Post::class)->find($id);

        /*return JsonResponse::create([
            'id' => $post->getId(),
            'title' => $post->title,
            'decription' => $post->description,
            'createdAt' => $post->getCreatedAt()->format('Y-m-d')
        ]);*/

        if(null === $post) {
           // throw new NotFoundHttpException('Post não encontrado');
           return new Response ("Not found", Response::HTTP_NOT_FOUND);
        }

        return JsonResponse::fromJsonString($this->serializer->serialize($post, 'json'));
    }

    /**
     * @Route("/posts", methods={"GET"})
     */    
    public function index(): Response {    
        /** @var Post[] $posts */   
        $posts = $this->entityManager->getRepository(Post::class)->findAll();

        $data = [];

        foreach ($posts as $post){
            $data[] = [
                'id' => $post->getId(),
                'title' => $post->title,
                'decription' => $post->description,
                'createdAt' => $post->getCreatedAt()->format('Y-m-d')
            ];
        }

        return JsonResponse::create($data);
    }

    /**
     * @Route("/posts/{id}", methods={"PUT"})
     */ 
    public function update(Request $request, int $id): Response{
        /** @var Post $post */   
        $post = $this->entityManager->getRepository(Post::class)->find($id);

        $data = json_decode($request->getContent(), true);

        $post->title = $data['title'];
        $post->description = $data['description'];

        $this->entityManager->persist($post);
        $this->entityManager->flush(); 
        return new Response ("Ok");
    }

    /**
     * @Route("/posts/{id}", methods={"DELETE"})
     */
    public function delete(int $id): Response {
        /** @var Post $post */
        $post = $this->entityManager->getRepository(Post::class)->find($id);
        $this->entityManager->remove($post);
        $this->entityManager->flush(); 
        return new Response ("", Response::HTTP_NO_CONTENT);        
    }
}

