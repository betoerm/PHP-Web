<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */

final class Post {

    /**
     * *@ORM\Id()
     * @ORM\Column(type="integer")
     * ORM\GeneratedValue()
     */

     private ?int $id = null;

     /**
      * @ORM\Column()      
      */

      private string $title;

      
     /**
      * @ORM\Column()      
      */
      
      private string $description;

      
     /**
      * @ORM\Column(type="datetime")      
      */

      private \DateTime $createdAt;
      
     public function __construct(string $title, string $description){
         $this->title = $title;
         $this->description = $description;
         $this->createdAt = new \DateTime();
     }

     public function getId(){
         return $this->id;
     }

     public function getCreatedAt(){
         return $this->createdAt;
     }
}