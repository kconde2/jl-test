<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use App\Manager\UserManager;

class UserController extends AbstractController
{

    /**
     * @Route("/api/users", name="api_create_user", methods={"POST"})
     */
    public function createUser(
        Request $request,
        EntityManagerInterface $em,
        SerializerInterface $serializer,
        UserManager $userManager
    ) {
        $input = json_decode($request->getContent(), true);
        $errors = $userManager->validateInput($input);

        if (count($errors) > 0) {
            return $this->json([
                'message' => 'Validation error',
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = new User();
        $user->setFirstname($input['firstname']);
        $user->setLastname($input['lastname']);

        $em->persist($user);
        $em->flush();

        $userSerialized = $serializer->serialize($user, 'json');
        return $this->json(json_decode($userSerialized), Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/users", name="api_get_users", methods={"GET"})
     */
    public function getAllUsers(
        Request $request,
        UserRepository $userRepository,
        SerializerInterface $serializer
    ) {
        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);
        $users = $userRepository->findAllUsers($page, $limit);
        $data = $serializer->serialize($users, 'json');

        return $this->json(json_decode($data));
    }

    /**
     * @Route("/api/users/{id}", name="api_one_user", methods={"GET"})
     */
    public function getOneUser(int $id, UserRepository $userRepository, SerializerInterface $serializer)
    {
        $user = $userRepository->find($id);

        if (!$user instanceof User) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $userSerialized = $serializer->serialize($user, 'json');
        return $this->json(json_decode($userSerialized));
    }

    /**
     * @Route("/api/users/{id}", name="api_update_user", methods={"PUT"})
     */
    public function updateUser(
        int $id,
        Request $request,
        UserManager $userManager,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        SerializerInterface $serializer
    ) {
        $user = $userRepository->find($id);

        if (!$user instanceof User) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $input = json_decode($request->getContent(), true);
        $errors = $userManager->validateInput($input);

        if (count($errors) > 0) {
            return $this->json([
                'message' => 'Validation error',
                'errors' => $errors
            ], Response::HTTP_BAD_REQUEST);
        }

        $user->setFirstname($input['firstname']);
        $user->setLastname($input['lastname']);
        $em->persist($user);

        $em->flush();

        $userSerialized = $serializer->serialize($user, 'json');
        return $this->json(json_decode($userSerialized), Response::HTTP_OK);
    }

    /**
     * @Route("/api/users/{id}", name="api_delete_user", methods={"DELETE"})
     */
    public function deleteUser(
        int $id,
        UserRepository $userRepository,
        EntityManagerInterface $em
    ) {
        $user = $userRepository->find($id);

        if (!$user instanceof User) {
            return $this->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
