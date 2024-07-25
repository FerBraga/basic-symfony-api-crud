<?php

namespace App\Controller;

use App\Entity\Partner;
use App\Repository\CompanyRepository;
use App\Repository\PartnerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PartnerController extends AbstractController
{

    public function __construct(private AuthorizationCheckerInterface $authChecker)
    {
    }

    #[Route('/partners', name: 'list_partners', methods: ['GET'])]
    public function index(PartnerRepository $partnerRepository): JsonResponse
    {

        $partners = $partnerRepository->findAll();

        $partnersData = array_map(function ($partner) {
            return [
                'id' => $partner->getId(),
                'name' => $partner->getName(),
                'lastName' => $partner->getLastName(),
                'email' => $partner->getEmail(),
                'company' => $partner->getCompany() ? [
                    'companyName' => $partner->getCompany()->getName()
                ] : null,
                'createdAt' => $partner->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $partner->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $partners);

        return $this->json([
            'message' => 'Partners list retrieved successfully',
            'data' => $partnersData,
        ], 200);
    }

    #[Route('/partner/show/{id}', name: 'show_partner', methods: ['GET'])]
    public function show(PartnerRepository  $partnerRepository, int $id): JsonResponse
    {

        $partner = $partnerRepository->find($id);

        if (!$partner) {
            throw $this->createNotFoundException(
                'No partner found for id ' . $id
            );
        }

        $companyData = [
            'id' => $partner->getId(),
            'name' => $partner->getName(),
            'lastName' => $partner->getLastName(),
            'email' => $partner->getEmail(),
            'company' => $partner->getCompany() ? [
                'companyName' => $partner->getCompany()->getName()
            ] : null,
            'createdAt' => $partner->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $partner->getUpdatedAt()->format('Y-m-d H:i:s')
        ];


        return $this->json([
            'message' => 'Partner retrieved successfully',
            'partner' => $companyData,
        ], 200);
    }

    #[Route('/partner/create', name: 'create_partner', methods: ['POST'])]
    public function create(Request $request, PartnerRepository $partnerRepository, CompanyRepository $companyRepository, ValidatorInterface $validator, #[CurrentUser] $user): JsonResponse
    {
        try {

            if (!$this->authChecker->isGranted('ROLE_ADMIN', $user)) {
                throw $this->createAccessDeniedException('You do not have permission to access this resource.');
            }

            $data = $request->toArray();

            $partner = new Partner();

            $company = $companyRepository->find($data['companyId']);

            if (!$company) {
                throw $this->createNotFoundException('No company found for id ' . $data['companyId']);
            }

            $partnerAlreadyExists = $partnerRepository->findOneBy(['email' => $data['email']]);

            if ($partnerAlreadyExists) {
                throw new \InvalidArgumentException('Partner already exists with the same email');
            }

            $partner->setName($data['name']);
            $partner->setLastName($data['lastName']);
            $partner->setEmail($data['email']);
            $partner->setRole($data['role']);
            $partner->setCompany($company);
            $partner->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
            $partner->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

            $this->validateEntity($validator, $partner);

            $partnerRepository->add($partner, true);

            $partner->getCompany()->addPartner($partner);

            return $this->json([
                'message' => 'Partner created successfully',
            ], 201);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'message' => 'Bad request',
                'errors' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/partner/edit/{id}', name: 'update_partner', methods: ['PUT'])]
    public function update(Request $request, CompanyRepository $companyRepository, PartnerRepository $partnerRepository, int $id, #[CurrentUser] $user): JsonResponse
    {

        if (!$this->authChecker->isGranted('ROLE_ADMIN', $user)) {
            throw $this->createAccessDeniedException('You do not have permission to access this resource.');
        }

        $data = $request->toArray();

        $partner = $partnerRepository->find($id);

        if (!$partner) {
            throw $this->createNotFoundException('No partner found for id ' . $id);
        }

        if (isset($data['name'])) {
            $partner->setName($data['name']);
        }

        if (isset($data['lastName'])) {
            $partner->setLastName($data['lastName']);
        }

        if (isset($data['email'])) {
            $partner->setEmail($data['email']);
        }

        if (isset($data['companyId'])) {
            $company = $companyRepository->find($data['companyId']);

            if (!$company) {
                throw $this->createNotFoundException('No company found for id ' . $data['companyId']);
            }
            $partner->setCompany($company);
        }


        $partner->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $entityManager = $partnerRepository->getEntityManager();
        $entityManager->flush();

        return $this->json([
            'message' => 'Partner updated successfully',
        ], 204);
    }

    #[Route('/partner/delete/{id}', name: 'delete_partner', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id, #[CurrentUser] $user): JsonResponse
    {

        if (!$this->authChecker->isGranted('ROLE_ADMIN', $user)) {
            throw $this->createAccessDeniedException('You do not have permission to access this resource.');
        }

        $partner = $entityManager->getRepository(Partner::class)->find($id);

        if (!$partner) {
            throw $this->createNotFoundException('No partner found for id ' . $id);
        }

        $entityManager->remove($partner);
        $entityManager->flush();

        return $this->json([
            'message' => 'Partner removed successfully',
        ], 204);
    }

    private function validateEntity($validator, $entity)
    {
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            throw new \InvalidArgumentException($errorsString);
        }
    }
}
