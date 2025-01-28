<?php

namespace App\Tests;

use App\Entity\Application;
use App\Service\TransformDataProvider;
use PHPUnit\Framework\TestCase;

class TransformDataProviderTest extends TestCase
{
    public function testTransformDataProviderForApplication()
    {
        $application = $this->createMock(Application::class);
        $application->method('getId')->willReturn(14);
        $application->method('getName')->willReturn('test 1');
        $application->method('getDescription')->willReturn('Description 1');
        $application->method('getCreated')->willReturn(new \DateTime('2021-10-01 00:00:00'));

        $application->method('getLogo')->willReturn('logo1.png');


        $application2 = $this->createMock(Application::class);
        $application2->method('getId')->willReturn(15);
        $application2->method('getName')->willReturn('test 2');
        $application2->method('getDescription')->willReturn('Description 2');
        $application2->method('getCreated')->willReturn(new \DateTime('2021-10-01 00:00:00'));
        $application2->method('getLogo')->willReturn('logo2.png');

        $applications = [$application, $application2];

        $expected = [
            "applications" =>[
                [
                    'ApplicationId' => '14',
                    'ApplicationName' => 'test 1',
                    'ApplicationDescription' => 'Description 1',
                    'ApplicationCreated' => new \DateTime("2021-10-01 00:00:00"),
                    'ApplicationLogo' => 'logo1.png'
                ], [
                    'ApplicationId' => '15',
                    'ApplicationName' => 'test 2',
                    'ApplicationDescription' => 'Description 2',
                    'ApplicationCreated' => new \DateTime("2021-10-01 00:00:00"),
                    'ApplicationLogo' => 'logo2.png'
                ]
            ]
        ];

        $transformDataProvider = new TransformDataProvider();

        $this->assertEquals($expected, $transformDataProvider->transformDataForApplications($applications));

    }
}
