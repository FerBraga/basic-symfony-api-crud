<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Company;
use App\Repository\AddressRepository;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CompanyController extends AbstractController
{
    #[Route('/company', name: 'companies_list', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository): JsonResponse
    {

        $companies = $companyRepository->findAll();

        $companiesData = array_map(function ($company) {
            $address = $company->getAddress();

            $addressData = $address ? [
                'street' => $address->getStreet(),
                'city' => $address->getCity(),
                'state' => $address->getState(),
                'country' => $address->getCountry(),
                'zipCode' => $address->getZipCode(),
            ] : null;

            $partnersData = $company->getPartners() ? array_map(function ($partner) {
                return [
                    'name' => $partner->getName(),
                    'lastName' => $partner->getLastName(),
                ];
            }, $company->getPartners()->toArray()) : null;

            return [
                'id' => $company->getId(),
                'name' => $company->getName(),
                'CNPJ' => $company->getCNPJ(),
                'address' => $addressData,
                'partners' => $partnersData,
                'createdAt' => $company->getCreatedAt()->format('Y-m-d H:i:s'),
                'updatedAt' => $company->getUpdatedAt()->format('Y-m-d H:i:s'),
            ];
        }, $companies);

        return $this->json([
            'message' => 'Companies list retrieved successfully',
            'companies' => $companiesData,
        ], 200);
    }


    #[Route('/company/{id}', name: 'show_company', methods: ['GET'])]
    public function show(CompanyRepository $companyRepository, int $id): JsonResponse
    {

        $company = $companyRepository->find($id);

        if (!$company) {
            throw $this->createNotFoundException(
                'No company found for id ' . $id
            );
        }
        $address = $company->getAddress();

        $addressData = $address ? [
            'street' => $address->getStreet(),
            'city' => $address->getCity(),
            'state' => $address->getState(),
            'country' => $address->getCountry(),
            'zipCode' => $address->getZipCode(),
        ] : null;

        $partnersData = $company->getPartners() ? array_map(function ($partner) {
            return [
                'name' => $partner->getName(),
                'lastName' => $partner->getLastName(),
            ];
        }, $company->getPartners()->toArray()) : null;

        $companyData = [
            'id' => $company->getId(),
            'name' => $company->getName(),
            'CNPJ' => $company->getCNPJ(),
            'address' => $addressData,
            'partners' => $partnersData,
            'createdAt' => $company->getCreatedAt()->format('Y-m-d H:i:s'),
            'updatedAt' => $company->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];

        return $this->json([
            'message' => 'Company retrieved successfully',
            'companies' => $companyData,
        ], 200);
    }

    #[Route('/company', name: 'company_create', methods: ['POST'])]
    public function create(Request $request, CompanyRepository $companyRepository, AddressRepository $addressRepository): JsonResponse
    {

        $data = $request->toArray();

        $address = new Address();

        $address->setStreet($data['street']);
        $address->setCity($data['city']);
        $address->setState($data['state']);
        $address->setCountry($data['country']);
        $address->setZipCode($data['zipCode']);
        $address->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $address->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $addressRepository->add($address, true);

        $company = new Company();
        $company->setName($data['name']);
        $company->setCNPJ($data['cnpj']);
        $company->setAddress($address);
        $company->setCreatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));
        $company->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $companyRepository->add($company, true);

        return $this->json([
            'message' => 'Company created successfully',
        ], 201);
    }

    #[Route('/company/edit/{id}', name: 'update_company', methods: ['PUT'])]
    public function update(Request $request, CompanyRepository $companyRepository, int $id): JsonResponse
    {
        $company = $companyRepository->find($id);

        if (!$company) {
            throw $this->createNotFoundException('No company found for id ' . $id);
        }

        $data = $request->toArray();

        $address = $company->getAddress();

        if (isset($data['street'])) {
            $address->setStreet($data['street']);
        }

        if (isset($data['city'])) {
            $address->setCity($data['city']);
        }

        if (isset($data['state'])) {
            $address->setState($data['state']);
        }

        if (isset($data['country'])) {
            $address->setCountry($data['country']);
        }

        if (isset($data['zipCode'])) {
            $address->setZipCode($data['zipCode']);
        }

        $address->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $company->setAddress($address);

        if (isset($data['name'])) {
            $company->setName($data['name']);
        }

        if (isset($data['cnpj'])) {
            $company->setCNPJ($data['cnpj']);
        }

        $company->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

        $entityManager = $companyRepository->getEntityManager();
        $entityManager->flush();

        return $this->json([
            'message' => 'Company updated successfully',
        ], 204);
    }

    // #[Route('/company/remove/{id}', name: 'update_company', methods: ['PUT'])]
    // public function update(Request $request, CompanyRepository $companyRepository, int $id): JsonResponse
    // {
    //     $company = $companyRepository->find($id);

    //     if (!$company) {
    //         throw $this->createNotFoundException('No company found for id ' . $id);
    //     }

    //     $data = $request->toArray();

    //     foreach($data as $partner) {

    //     }

    //     $address = $company->getAddress();

    //     if (isset($data['street'])) {
    //         $address->setStreet($data['street']);
    //     }

    //     if (isset($data['city'])) {
    //         $address->setCity($data['city']);
    //     }

    //     if (isset($data['state'])) {
    //         $address->setState($data['state']);
    //     }

    //     if (isset($data['country'])) {
    //         $address->setCountry($data['country']);
    //     }

    //     if (isset($data['zipCode'])) {
    //         $address->setZipCode($data['zipCode']);
    //     }

    //     $address->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

    //     $company->setAddress($address);

    //     if (isset($data['name'])) {
    //         $company->setName($data['name']);
    //     }

    //     if (isset($data['cnpj'])) {
    //         $company->setCNPJ($data['cnpj']);
    //     }

    //     $company->setUpdatedAt(new \DateTimeImmutable('now', new \DateTimeZone('America/Sao_Paulo')));

    //     $entityManager = $companyRepository->getEntityManager();
    //     $entityManager->flush();

    //     return $this->json([
    //         'message' => 'Company updated successfully',
    //     ], 204);
    // }

    #[Route('/company/delete/{id}', name: 'delete_company', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id): JsonResponse
    {

        $company = $entityManager->getRepository(Company::class)->find($id);

        if (!$company) {
            throw $this->createNotFoundException('No company found for id ' . $id);
        }

        $entityManager->remove($company);
        $entityManager->flush();

        return $this->json([
            'message' => 'Company removed successfully',
        ], 204);
    }
}
