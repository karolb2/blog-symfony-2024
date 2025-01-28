<?php

namespace App\Controller;

use App\Entity\Application;
use App\Form\ApplicationType;
use App\Formatter\ApiResponseFormatter;
use App\Repository\ApplicationRepository;
use App\Service\TransformDataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/applications')]
final class ApplicationController extends AbstractController
{
    public function __construct(
        private readonly ApiResponseFormatter $apiResponseFormatter,
        private TransformDataProvider $transformDataProvider)
    {
    }

    #[Route(
        name: 'app_application_index',
        methods: ['GET'])
    ]
    #[IsGranted('ROLE_LIST_APPLICATION')]
    public function index(ApplicationRepository $applicationRepository): Response
    {
        $application = $applicationRepository->findAll();

        $applicationList = $this->transformDataProvider->transformDataForApplications($application);

        return $this->apiResponseFormatter
            ->withData($applicationList)
            ->response();

    }

    #[Route(
        name: 'app_application_new',
        methods: ['POST'])
    ]
    #[IsGranted('ROLE_ADD_APPLICATION')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        if(!$data){
            return $this->apiResponseFormatter
                ->withMessage('Invalid request')
                ->withStatus(Response::HTTP_BAD_REQUEST)
                ->response();
        }

        $application = new Application();
        $application->setName($data['name']);
        $application->setDescription($data['description']);
        $application->setCreated(new \DateTime());
        $entityManager->persist($application);
        $entityManager->flush();

        return $this->apiResponseFormatter
            ->withData($application->toArray())
            ->withStatus(Response::HTTP_CREATED)
            ->response();

    }

    #[Route(
        '/{id}',
        name: 'app_application_show',
        methods: ['GET'])
    ]
    #[IsGranted('ROLE_SHOW_APPLICATION')]
    public function show(Application $application): Response
    {
        return $this->render('application/show.html.twig', [
            'application' => $application,
        ]);
    }

    #[Route(
        '/{id}/edit',
        name: 'app_application_edit',
        methods: ['GET', 'POST'])
    ]
    public function edit(Request $request, Application $application, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        if(!$data){
            return $this->apiResponseFormatter
                ->withMessage('Invalid request')
                ->withStatus(Response::HTTP_BAD_REQUEST)
                ->response();
        }

        (empty($data['name'])) ? : $application->setName($data['name']);
        (empty($data['description'])) ? :  $application->setDescription($data['description']);;
        $entityManager->persist($application);
        $entityManager->flush();

        return $this->apiResponseFormatter
            ->withData($application->toArray())
            ->withStatus(Response::HTTP_OK)
            ->response();
    }

    #[Route(
        '/{id}',
        name: 'app_application_delete',
        methods: ['DELETE'])
    ]
    #[IsGranted('ROLE_DELETE_APPLICATION')]
    public function delete(Request $request, Application $application, EntityManagerInterface $entityManager): Response
    {
        $request->get('id');

        if(!$request->get('id') || (int)$request->get('id') !== $application->getId()){
            return $this->apiResponseFormatter
                ->withMessage('Invalid request')
                ->withStatus(Response::HTTP_BAD_REQUEST)
                ->response();
        }

        $entityManager->remove($application);
        $entityManager->flush();

        return $this->apiResponseFormatter
            ->withStatus(Response::HTTP_OK)
            ->response();

    }
}
