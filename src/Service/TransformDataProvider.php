<?php

declare(strict_types=1);

namespace App\Service;

readonly class TransformDataProvider
{
    public function transformData(array $articles) : array
    {
        $transformersArticles = [];
        foreach($articles as $article){
            $transformersArticles['articles'][] = [
                'id' => $article->getId(),
                'title' => $article->getTitle(),
                'content' => substr($article->getContent(), 0,200) . '...',
                'url' => $article->getUrl()
            ];
        }

        return $transformersArticles;
    }

    public function transformDataForApplications(array  $applications): array
    {
        $transformApplications = [];
        foreach ($applications as $key => $application){
            $transformApplications['applications'][] = [
                'ApplicationId' => $application->getId(),
                'ApplicationName' => $application->getName(),
                'ApplicationDescription' => $application->getDescription(),
                'ApplicationCreated' => $application->getCreated(),
                'ApplicationLogo' => $application->getLogo()
            ];
        }

        return $transformApplications;
    }
}
