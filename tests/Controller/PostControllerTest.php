<?php

namespace App\Tests\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\TollsException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

final class PostControllerTest extends WebTestCase {
    private EntityManagerInterface $em;
    private KernelBrowser $client;

    public function setUp(): void {  

        $this->client = self::createClient();

        $this->em = self::$kernel->getContainer()->get('doctrine')->getManager();
        $tool = new SchemaTool($this->em);

        $metadata = $this->em->getClassMetadata(Post::class);

        $tool->dropSchema([$metadata]);

        try{
            $tool->createSchema([$metadata]);
        } catch (ToolException $e){
            $this->fail('Impossível criar tabela Post: ', $e->getMessage());
        }        
    }

    public function test_create_post(): void {
        //$client = static::createClient();
        $this->client->request('POST', '/posts', [], [], [], json_encode([
            'title' => 'Primeio Teste Funcional',
            'description' => 'Alguma Descrição'
        ]));        

        $this->assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    public function test_delete_post():void {
        //$client = static::createClient();
        $post = new Post("Minha primeira aplicação com Symfony", "Descrição da minha aplicação");        
        $this->em->persist($post);
        $this->em->flush(); 

        $this->client->request('DELETE','/posts/1');
        $this->assertEquals(Response::HTTP_NO_CONTENT, $this->client->getResponse()->getStatusCode());
    }

    public function test_create_post_invalid_title(): void {
        $this->client->request('POST', '/posts', [], [], [], json_encode([
            'title' => null,
            'description' => 'Alguma Descrição'
        ]));        

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_get_post_not_exist(): void {
        $this->client->request('GET', '/posts/11');
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function test_create_post_invalid_description(): void {
        $this->client->request('POST', '/posts', [], [], [], json_encode([
            'title' => 'Title',
            'description' => null
        ]));        
    
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_update_post(): void {
        $post = new Post("Minha primeira aplicação com Symfony", "Descrição da minha aplicação");        
        $this->em->persist($post);
        $this->em->flush(); 

        $this->client->request('PUT', '/posts/1', [], [], [], json_encode([
            'title' => 'Atualizando post',
            'description' => 'Alguma Descrição'
        ]));        

        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
    
    public function test_get_post_exist(): void {
        $post = new Post("Minha primeira aplicação com Symfony", "Descrição da minha aplicação");        
        $this->em->persist($post);
        $this->em->flush(); 

        $this->client->request('GET', '/posts/1');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_get_all_post(): void {
        $post = new Post("Minha primeira aplicação com Symfony", "Descrição da minha aplicação");        
        $this->em->persist($post);
        $this->em->flush(); 

        $this->client->request('GET', '/posts');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_get_all_post_not_exist(): void {
        $this->client->request('GET', '/posts');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }    
}

