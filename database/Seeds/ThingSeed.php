<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use App\Domain\Thing\Enum\FaultLevel;
use App\Domain\Thing\Repository\ThingRepository;
use App\Domain\Thing\Thing;
use App\Infrastructure\Persistence\RepositoryInterface;

readonly class ThingSeed implements SeedInterface
{
    public function __construct(private ThingRepository $repository)
    {
        //
    }

    public function getName(): string
    {
        return 'Things';
    }

    public function getRepository(): RepositoryInterface
    {
        return $this->repository;
    }

    public function getData(): array
    {
        return [
            new Thing(
                id: null,
                name: 'Is It George Michael?',
                shortDescription: 'API joke site',
                description: 'Micro-site produced in an afternoon in response to a Geordie friend referring to warm 
                weather (wham weather) as “George Michael”. The site fetched the current weather for your location and 
                then let you know if it was George Michael where you were. Anything above 12 degrees (average 
                temperature in the UK) is George Michael.',
                featured: false,
                faultLevel: FaultLevel::ALL,
                activeFrom: (new \DateTimeImmutable('2016-11-19'))->getTimestamp(),
                activeTo: (new \DateTimeImmutable('2023-01-03'))->getTimestamp(),
            ),
            new Thing(
                id: null,
                name: 'S Wilford Printer',
                shortDescription: 'Business website',
                description: "Basic single page site for my father's Leicester based printing business. I've refreshed 
                the design a few times over the years. Part of me thinks the side scrolling wonder I created in 
                Dreamweaver many many years ago was the best iteration. I also have been responsible for the local 
                SEO work to help place the business more prominently in search.",
                featured: false,
                faultLevel: FaultLevel::ALL,
                activeFrom: (new \DateTimeImmutable('2013-05-18'))->getTimestamp(),
                activeTo: null,
                url: 'https://swilfordprinter.co.uk/'
            ),
            new Thing(
                id: null,
                name: 'Anne Wyman',
                shortDescription: 'Photography portfolio',
                description: "Worked with a designer to realise a portfolio website for a student photographer. I also 
                maintained the website over the years, adapting it for a more commercial focus later on in it's life. 
                The site was built using Datenstrom Yellow to allow the photographer to create new pages/upload more 
                images as and when they needed to. The site also used Instagram's API to fetch their images and display 
                them on one of the pages. ",
                featured: false,
                faultLevel: FaultLevel::MOSTLY,
                activeFrom: (new \DateTimeImmutable('2016-01-04'))->getTimestamp(),
                activeTo: (new \DateTimeImmutable('2023-12-04'))->getTimestamp(),
            ),
            new Thing(
                id: null,
                name: 'Jive Prints',
                shortDescription: 'Print shop',
                description: "Worked with the designers at Jive on the general layout, and then transformed that into a 
                customisable ecommerce solution using Shopify. My favourite aspect was a sticky navigation bar at the 
                bottom of the page when browsing on mobile that I came up with (before it was cool).",
                featured: false,
                faultLevel: FaultLevel::MOSTLY,
                activeFrom: (new \DateTimeImmutable('2017-05-04'))->getTimestamp(),
                activeTo: (new \DateTimeImmutable('2018-06-12'))->getTimestamp(),
            ),
            new Thing(
                id: null,
                name: 'Pink Pig',
                shortDescription: 'Ecommerce shop',
                description: "Web design & development over different sites dedicated to retail, education and trade 
                sectors for several years. Ultimately leading up to a redesign and rebuild of their Shopify site that 
                I planned and executed. Other responsibilities included maintaining product information & prices over 
                the websites, as well as for online marketplaces. Additionally: designed and orchestrated email and 
                social media marketing, took care of all SEO and CPC advertising; and various other roles as and when 
                required. Basically anything they needed doing that involved the internet.",
                featured: false,
                faultLevel: FaultLevel::MOSTLY,
                activeFrom: (new \DateTimeImmutable('2014-10-16'))->getTimestamp(),
                activeTo: (new \DateTimeImmutable('2018-07-31'))->getTimestamp(),
            ),
            new Thing(
                id: null,
                name: 'Equinox',
                shortDescription: 'Software development',
                description: "Software development and software team lead during a rapid growth phase for a start up. 
                I've been involved in lots of exciting projects over the years, and have been instrumental in 
                modernisation of processes and the codebase.",
                featured: true,
                faultLevel: FaultLevel::PARTLY,
                activeFrom: (new \DateTimeImmutable('2018-08-13'))->getTimestamp(),
                url: 'https://equinox-ipms.com/'
            ),
            new Thing(
                id: null,
                name: 'UTF8 2 RTF',
                shortDescription: 'Composer package',
                description: "I put this together over the Christmas break in 2023 to make my life easier 
                (and other peoples lives). To cut a long (thrilling) story short, you can't just stick non-latin or 
                accented UTF8 characters into RTF files and expect them to work. This package sorts that out for you. 
                5469 installs at the time of writing. I can hardly believe that anyone else is still using RTFs.",
                featured: true,
                faultLevel: FaultLevel::ALL,
                activeFrom: (new \DateTimeImmutable('2023-01-01'))->getTimestamp(),
                url: 'https://packagist.org/packages/tomwilford/php-utf8-to-rtf'
            ),
            new Thing(
                id: null,
                name: 'This Website',
                shortDescription: 'Literally this website',
                description: "Over engineering? Never heard of it. I loved the previous iteration of this website 
                (built in 2016) but it looked kind of silly on vertical screens, was a pain to update and didn't 
                represent my abilities anymore. Also no dark mode on that one. Instead I wanted something nice and 
                minimal that I can update easily and expand on.",
                featured: true,
                faultLevel: FaultLevel::ALL,
                activeFrom: (new \DateTimeImmutable('2016-08-16'))->getTimestamp(),
                url: 'https://github.com/TomWilford/jbm-website'
            ),
        ];
    }
}
