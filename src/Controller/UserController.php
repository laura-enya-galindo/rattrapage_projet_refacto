<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\User;
use PDO;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    #[Route('/users', name: 'liste_des_users', methods:['GET'])]
    public function getListeDesUsers(EntityManagerInterface $entityManager): JsonResponse
    {
        $data = $entityManager->getRepository(User::class)->findAll();
        return new JsonResponse($data);
    }

    #[Route('/users', name: 'user_post', methods:['POST'])]
    public function createUser(Request $request,EntityManagerInterface $entityManager): JsonResponse
    {
        if($request->getMethod() === 'POST'){
            $data = json_decode($request->getContent(), true);
            $form = $this->createFormBuilder()
                ->add('nom', TextType::class)
                ->add('age', NumberType::class)
                ->getForm();

            $form->submit($data);

            if($form->isValid())
            {
                if($data['age'] > 21){
                    $user = $entityManager->getRepository(User::class)->findBy(['name'=>$data['nom']]);
                    if(count($user) === 0){
                        $joueur = new User();
                        $joueur->setName($data['nom']);
                        $joueur->setAge($data['age']);
                        $entityManager->persist($joueur);
                        $entityManager->flush();

                        return new JsonResponse($joueur, 201);
                    }else{
                        return new JsonResponse('Name already exists', 400);
                    }
                }else{
                    return new JsonResponse('Wrong age', 400);
                }
            }else{
                return new JsonResponse('Invalid form', 400);
            }
        }else{
            return new JsonResponse('Wrong method', 405);
        }
    }

    #[Route('/user/{identifiant}', name: 'get_user_by_id', methods:['GET'])]
    public function getUserWithIdentifiant($identifiant, EntityManagerInterface $entityManager): JsonResponse
    {
        if(is_int($identifiant)){
            $joueur = $entityManager->getRepository(User::class)->findBy(['id'=>$identifiant]);
            if(count($joueur) == 1){
                return new JsonResponse(array('name'=>$joueur->getName(), "age"=>$joueur->getAge(), 'id'=>$joueur->getId()), 200);
            }else{
                return new JsonResponse('Wrong id', 404);
            }
        }
        return new JsonResponse('Wrong id', 404);
    }

    #[Route('/user/{identifiant}', name: 'udpate_user', methods:['PATCH'])]
    public function updateUser(EntityManagerInterface $entityManager, $identifiant, Request $request): JsonResponse
    {
        $joueur = $entityManager->getRepository(User::class)->findBy(['id'=>$identifiant]);

        if(count($joueur) == 1){

            // ETO 01/05/2023 on n'utilise plus le put
            // on est passé au patch
            // parce que c'etait plus simple a utiliser
            /*
            if($request->getMethod() == 'PUT'){
                $data = json_decode($request->getContent(), true);

                $form = $this->createFormBuilder()
                    ->add('nom', TextType::class)
                    ->add('age', NumberType::class)
                    ->getForm();

                $form->submit($data);

                if($form->isValid()) {
                    if($data['age'] > 21){
                        $user = $entityManager->getRepository(User::class)->findBy(['name'=>$data['nom']]);
                        if(count($user) === 0){
                            $joueur->setName($data['nom']);
                            $joueur->setAge($data['age']);
                            $entityManager->persist($joueur);
                            $entityManager->flush();

                            return new JsonResponse($joueur, 200);
                        }else{
                            return new JsonResponse('Name already exists', 400);
                        }
                    }else{
                        return new JsonResponse('Wrong age', 400);
                    }
                }else{
                    return new JsonResponse('Invalid form', 400);
                }
            }elseif($request->getMethod() == 'PATCH'){
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
                if($form->isValid()) {
                    if($data['age'] > 21){
                        $user = $entityManager->getRepository(User::class)->findBy(['name'=>$data['nom']]);
                        if(count($user) === 0){
                            $joueur->setName($data['nom']);
                            $joueur->setAge($data['age']);
                            $entityManager->flush();

                            return new JsonResponse($joueur, 200);
                        }else{
                            return new JsonResponse('Name already exists', 400);
                        }
                    }else{
                        return new JsonResponse('Wrong age', 400);
                    }
                }else{
                    return new JsonResponse('Invalid form', 400);
                }
            }else{
                $data = json_decode($request->getContent(), true);
                return new JsonResponse('Wrong method', 405);
            }
            */

            if($request->getMethod() == 'PATCH'){
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
                if($form->isValid()) {
                    if($data['age'] > 21){
                        $user = $entityManager->getRepository(User::class)->findBy(['name'=>$data['nom']]);
                        if(count($user) < 1){
                            if($joueur->getName() === $data['nom']){
                                // pas la peine de remplacer si le nom est le meme
                                // comme ca on gagne des perfs

                            }else{
                                $joueur->setName($data['nom']);
                            }

                            if($joueur->getAge() === $data['age']){
                                // pas la peine de remplacer si le nom est le meme
                                // comme ca on gagne des perfs

                            }else{
                                $joueur->setAge($data['age']);
                            }

                            $entityManager->flush();

                            return new JsonResponse($joueur, 200);
                        }else{
                            return new JsonResponse('Name already exists', 400);
                        }
                    }else{
                        return new JsonResponse('Wrong age', 400);
                    }
                }else{
                    return new JsonResponse('Invalid form', 400);
                }
            }else{
                $data = json_decode($request->getContent(), true);
                return new JsonResponse('Wrong method', 405);
            }

            return new JsonResponse(array('name'=>$joueur->getName(), "age"=>$joueur->getAge(), 'id'=>$joueur->getId()), 200);
        }else{
            return new JsonResponse('Wrong id', 404);
        }    
    }

    #[Route('/user/{id}', name: 'delete_user_by_identifiant', methods:['DELETE'])]
    public function suprUser($id, EntityManagerInterface $entityManager): JsonResponse
    {
        $joueur = $entityManager->getRepository(User::class)->findBy(['id'=>$id]);
        if(count($joueur) == 1){
            try{
                $entityManager->remove($joueur);

                $existeEncore = $entityManager->getRepository(User::class)->findBy(['id'=>$id]);
    
                if(!empty($existeEncore)){
                    throw new \Exception("Le user n'a pas éte délété");
                    return;
                }else{
                    return new JsonResponse('', 204);
                }
            }catch(\Exception $e){
                return new JsonResponse($e->getMessage(), 500);
            }
        }else{
            return new JsonResponse('Wrong id', 404);
        }    
    }
}
