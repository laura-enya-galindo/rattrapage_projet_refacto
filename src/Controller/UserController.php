<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use PDO;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class UserController extends AbstractController
{
    #[Route('/users', name: 'liste_des_users', methods:['GET'])]
    public function getUsersList(EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $entityManager->getRepository(User::class)->findAll();
        return $this->json(
            $data,
            headers: ['Content-Type' => 'application/json;charset=UTF-8']
        );
    }

    #[Route('/users', name: 'user_post', methods:['POST'])]
    public function createUser(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        if($request->getMethod() !== 'POST')
            return new JsonResponse('Wrong method', 405);
        {
            $data = json_decode($request->getContent(), true);
            $form = $this->createFormBuilder()
                ->add('nom', TextType::class, [
                    'constraints'=>[
                        new Assert\NotBlank(),
                        new Assert\Length(['min'=>1, 'max'=>255])
                    ]
                ])
                ->add('age', NumberType::class, [
                    'constraints'=>[
                        new Assert\NotBlank()
                    ]
                ])
                ->getForm();

            $form->submit($data);

            if($form->isValid() === false)
                return new JsonResponse('Invalid form', 400);
            {
                if($data['age'] > 21 === false )
                    return new JsonResponse('Wrong age', 400);
                {
                    $user = $entityManager->getRepository(User::class)->findBy(['name'=>$data['nom']]);
                    if(count($user) !== 0)
                        return new JsonResponse('Name already exists', 400);
                    {
                        $player = new User();
                        $player->setName($data['nom']);
                        $player->setAge($data['age']);
                        $entityManager->persist($player);
                        $entityManager->flush();

                        return $this->json(
                                    $player,
                                    201,
                                    ['Content-Type' => 'application/json;charset=UTF-8']
                                );                    
                    }
                }
            }
        }
    }

    #[Route('/user/{id}', name: 'get_user_by_id', methods:['GET'])]
    public function getUserWithIdentifiant($identifiant, EntityManagerInterface $entityManager): JsonResponse
    {
        if(ctype_digit(!$identifiant))
            return new JsonResponse('Wrong id', 404);
        {
            $player = $entityManager->getRepository(User::class)->findBy(['id'=>$identifiant]);
            if(count($player) !== 1)
            return new JsonResponse('Wrong id', 404);
            {
                return new JsonResponse(array('name'=>$player[0]->getName(), "age"=>$player[0]->getAge(), 'id'=>$player[0]->getId()), 200);
            }
        }
       
    }

    #[Route('/user/{id}', name: 'udpate_user', methods:['PATCH'])]
    public function updateUser(EntityManagerInterface $entityManager, $identifiant, Request $request): JsonResponse
    {
        $player = $entityManager->getRepository(User::class)->findBy(['id'=>$identifiant]);


        if(count($player) !== 1)
            return new JsonResponse('Wrong id', 404);
           {
            if($request->getMethod() !== 'PATCH')
                return new JsonResponse('Wrong method', 405);
            {
                $data = json_decode($request->getContent(), true);
                $form = $this->createFormBuilder()
                    ->add('nom', TextType::class, array(
                        'required'=>false
                    ))
                    ->add('age', NumberType::class, [
                        'required' => false
                    ])
                    ->getForm();

                $form->submit($data);
                if($form->isValid() === false)
                    return new JsonResponse('Invalid form', 400);
                 {
                    foreach($data as $key=>$value){
                        switch($key){
                            case 'nom':
                                $user = $entityManager->getRepository(User::class)->findBy(['name'=>$data['nom']]);
                                if(count($user) === 0){
                                    $player[0]->setName($data['nom']);
                                    $entityManager->flush();
                                break;
                                }
                                return new JsonResponse('Name already exists', 400);
                                
                                break;
                            case 'age':
                                if($data['age'] > 21){
                                    $player[0]->setAge($data['age']);
                                    $entityManager->flush();
                                break;
                                }
                                return new JsonResponse('Wrong age', 400);
                                break;
                        }
                    }
                }
            }
            return new JsonResponse(array('name'=>$player[0]->getName(), "age"=>$player[0]->getAge(), 'id'=>$player[0]->getId()), 200);
        } 
    }

    #[Route('/user/{id}', name: 'delete_user_by_identifiant', methods:['DELETE'])]
    public function deleteUser($id, EntityManagerInterface $entityManager): JsonResponse | null
    {
        $player = $entityManager->getRepository(User::class)->findBy(['id'=>$id]);
        if(count($player) !== 1) 
            return new JsonResponse('Wrong id', 404);
           {
            try{
                $entityManager->remove($player[0]);
                $entityManager->flush();

                $userStillExist = $entityManager->getRepository(User::class)->findBy(['id'=>$id]);
    
                if(!empty($userStillExist)){
                    throw new \Exception("User has not been deleted");
                    return null;
                }else{
                    return new JsonResponse('', 204);
                }
            }catch(\Exception $e){
                return new JsonResponse($e->getMessage(), 500);
            }
        } 
    }
}
