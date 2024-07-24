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

class PartnerController extends AbstractController
{
    #[Route('/partner', name: 'list_partners', methods: ['GET'])]
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

    #[Route('/partner/{id}', name: 'show_partner', methods: ['GET'])]
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

    #[Route('/partner', name: 'partner_create', methods: ['POST'])]
    public function create(Request $request, PartnerRepository $partnerRepository, CompanyRepository $companyRepository): JsonResponse
    {

        $data = $request->toArray();

        $partner = new Partner();

        $company = $companyRepository->find($data['companyId']);

        if (!$company) {
            throw $this->createNotFoundException('No company found for id ' . $data['companyId']);
        }

        $partner->setName($data['name']);
        $partner->setLastName($data['lastName']);
        $partner->setEmail($data['email']);
        $partner->setRole($data['role']);
        $partner->setCompany($company);
        $partner->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $partner->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $partnerRepository->add($partner, true);

        $partner->getCompany()->addPartner($partner);

        // $companyRepository->addPartner($partner);

        return $this->json([
            'message' => 'Partner created successfully',
        ], 201);
    }

    #[Route('/partner/edit/{id}', name: 'update_partner', methods: ['PUT'])]
    public function update(Request $request, CompanyRepository $companyRepository, PartnerRepository $partnerRepository, int $id): JsonResponse
    {

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
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {

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
}
