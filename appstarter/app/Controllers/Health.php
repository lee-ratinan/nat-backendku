<?php

namespace App\Controllers;

use App\Models\HealthActivityModel;
use App\Models\HealthAffirmationModel;
use App\Models\HealthMBTIModel;
use App\Models\JourneyHolidayModel;
use App\Models\LogActivityModel;
use App\Models\OocaLogModel;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\ResponseInterface;
use ReflectionException;

class Health extends BaseController
{

    private $fitness_first = [
        'SG' => [
            [
                'club'      => '100 AM (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/100-am-tanjong-pagar',
                'latitude'  => 1.2750018,
                'longitude' => 103.8436614,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7', 'PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'AMK Hub (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/amk-hub',
                'latitude'  => 1.3697323,
                'longitude' => 103.8487065,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7', 'PH'],
                        'time' => ['08:00:00', '21:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'Alexandra (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/alexandra-mapletree-business-city',
                'latitude'  => 1.275215,
                'longitude' => 103.7989239,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['08:00:00', '18:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'Bugis Junction (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/bugis-junction',
                'latitude'  => 1.2991347,
                'longitude' => 103.855363,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7', 'PH'],
                        'time' => ['08:00:00', '22:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'Capital Tower (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/capital-tower',
                'latitude'  => 1.278047,
                'longitude' => 103.8474288,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['08:00:00', '18:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'Fusionopolis (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/fusionopolis',
                'latitude'  => 1.2995647,
                'longitude' => 103.7876738,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '18:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['08:00:00', '17:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'Junction 10 (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/junction-10',
                'latitude'  => 1.3808842,
                'longitude' => 103.759926,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7', 'PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'Market Street (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/marketstreet',
                'latitude'  => 1.2843755,
                'longitude' => 103.8505206,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '18:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['08:00:00', '17:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'The Metropolis (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/the-metropolis',
                'latitude'  => 1.3055177,
                'longitude' => 103.7919789,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['08:00:00', '18:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'One George Street (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/one-george-street',
                'latitude'  => 1.2861074,
                'longitude' => 103.8468682,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['08:00:00', '18:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['09:00:00', '18:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'One Raffles Quay (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/one-raffles-quay',
                'latitude'  => 1.2811658,
                'longitude' => 103.8516453,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['08:00:00', '18:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'Paragon (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/paragon',
                'latitude'  => 1.303408,
                'longitude' => 103.8353432,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['08:00:00', '21:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'Payar Lebar (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/paya-lebar-singpost-centre',
                'latitude'  => 1.318983,
                'longitude' => 103.8948672,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7', 'PH'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Tampines (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/tampines-cpf-building',
                'latitude'  => 1.3530306,
                'longitude' => 103.9437137,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '23:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'Westgate (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/sg/en/clubs/westgate',
                'latitude'  => 1.3343179,
                'longitude' => 103.7429477,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                ]
            ]
        ],
        'TH' => [
            [
                'club'      => 'Central Plaza Bang Na (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/centralplaza-bangna',
                'latitude'  => 13.6688064,
                'longitude' => 100.6340278,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Central Plaza Chaeng Watthana (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/centralplaza-chaengwattana',
                'latitude'  => 13.903389,
                'longitude' => 100.5275424,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Central Plaza Chon Buri (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/centralplaza-chonburi',
                'latitude'  => 13.3365857,
                'longitude' => 100.9694202,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Central Plaza Grand Rama 9 (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/centralplaza-grand-rama-9',
                'latitude'  => 13.7587835,
                'longitude' => 100.5674957,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Central Plaza Khon Kaen (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/centralplaza-khonkaen',
                'latitude'  => 16.432771,
                'longitude' => 102.825513,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Central Plaza Pin Klao (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/centralplaza-pinklao',
                'latitude'  => 13.7777757,
                'longitude' => 100.4763561,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Central Plaza Rama 2 (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/centralplaza-rama-2',
                'latitude'  => 13.6632556,
                'longitude' => 100.4379981,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Central Plaza Rama 3 (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/centralplaza-rama-3',
                'latitude'  => 13.6971269,
                'longitude' => 100.536574,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Central Plaza Ratthanathibet (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/centralplaza-rattanathibet',
                'latitude'  => 13.8670024,
                'longitude' => 100.495552,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Central Plaza Udon Thani (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/centralplaza-udonthani',
                'latitude'  => 17.4061842,
                'longitude' => 102.8000884,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Club 39, Sukhumvit 39 (CLUB CLASS)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/club-39',
                'latitude'  => 13.7371998,
                'longitude' => 100.5714002,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Club Icon, Icon Siam (CLUB CLASS)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/club-icon',
                'latitude'  => 13.7264384,
                'longitude' => 100.5101136,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7', 'PH'],
                        'time' => ['08:00:00', '22:00:00']
                    ]
                ]
            ],
            [
                'club'      => 'The Crystal Ram Inthra (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/the-crystal-ramindra',
                'latitude'  => 13.8120374,
                'longitude' => 100.6197958,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Future Park Rangsit (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/future-park-rangsit',
                'latitude'  => 13.9888614,
                'longitude' => 100.6188638,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Home Pro Phetkasem (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/home-pro-petchkasem',
                'latitude'  => 13.7096314,
                'longitude' => 100.3628711,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Mega Bang Na (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/mega-bangna',
                'latitude'  => 13.6484969,
                'longitude' => 100.6820761,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'AIA Capital Center (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/platinum-aia-capital-center',
                'latitude'  => 13.7647433,
                'longitude' => 100.568383,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Landmark Plaza (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/platinum-landmark-plaza',
                'latitude'  => 13.7412747,
                'longitude' => 100.5540208,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Pearl Bangkok (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/platinum-pearl-bangkok',
                'latitude'  => 13.7780154,
                'longitude' => 100.5435632,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Q House Lumphini (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/platinum-q-house-lumpini',
                'latitude'  => 13.7255793,
                'longitude' => 100.5447215,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5', '6', '7', 'PH'],
                        'time' => ['00:00:00', '23:59:59']
                    ]
                ]
            ],
            [
                'club'      => 'Sathon Square (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/platinum-sathorn-square',
                'latitude'  => 13.7224086,
                'longitude' => 100.529153,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Siam Paragon (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/platinum-siam-paragon',
                'latitude'  => 13.7458903,
                'longitude' => 100.535419,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7', 'PH'],
                        'time' => ['08:00:00', '22:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'T-One Building (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/platinum-t-one-building',
                'latitude'  => 13.7222547,
                'longitude' => 100.5806503,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Seacon Square (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/seacon-square',
                'latitude'  => 13.6919291,
                'longitude' => 100.6486729,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Terminal 21 (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/terminal-21',
                'latitude'  => 13.7382945,
                'longitude' => 100.5607511,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'The Crystal SB Ratchaphruek (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/the-crystal-sb-ratchapruek',
                'latitude'  => 13.8092757,
                'longitude' => 100.4482291,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'The Mall Bang Kapi (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/the-mall-bangkapi',
                'latitude'  => 13.7670629,
                'longitude' => 100.6418982,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'The Mall Bang Khae (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/the-mall-bangkhae',
                'latitude'  => 13.7116757,
                'longitude' => 100.4085049,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'The Mall Khorat (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/the-mall-korat',
                'latitude'  => 14.9806337,
                'longitude' => 102.0762699,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '21:30:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'The Mall Ngamwongwan (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/the-mall-ngamwongwan',
                'latitude'  => 13.8562219,
                'longitude' => 100.5422515,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'The Mall Tha Phra (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/the-mall-thapra',
                'latitude'  => 13.7136819,
                'longitude' => 100.480682,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'The Promenade (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/the-promenade',
                'latitude'  => 13.827021,
                'longitude' => 100.676694,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'CentralWorld (ZONE)',
                'url'       => 'https://www.fitnessfirst.com/th/en/clubs/zone-centalworld',
                'latitude'  => 13.7478501,
                'longitude' => 100.539055,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['08:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
        ],
        'MY' => [
            [
                'club'      => '1 Mont Kiara (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/1-mont-kiara',
                'latitude'  => 3.1658626,
                'longitude' => 101.6520578,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:30:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['07:00:00', '20:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Avenue K (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/avenue-k',
                'latitude'  => 3.1594119,
                'longitude' => 101.7127308,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:30:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Cheras Leisure Mall (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/cheras-leisure-mall',
                'latitude'  => 3.0908294,
                'longitude' => 101.7426686,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:30:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['07:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'IOI Mall Puchong (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/ioi-mall-puchong',
                'latitude'  => 3.0442886,
                'longitude' => 101.617636,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:30:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['07:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Klang Bukit Tinggi (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/klang-bukit-tinggi',
                'latitude'  => 2.9928873,
                'longitude' => 101.444852,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['07:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7', 'PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Melawati Mall (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/melawati-mall',
                'latitude'  => 3.2106722,
                'longitude' => 101.7481247,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['07:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '20:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'NU Empire (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/nu-empire',
                'latitude'  => 3.0819623,
                'longitude' => 101.5824823,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:30:00', '22:00:00']
                    ],
                    [
                        'days' => ['6', '7'],
                        'time' => ['07:00:00', '20:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Paradigm Mall (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/paradigm-mall',
                'latitude'  => 3.1044925,
                'longitude' => 101.5955686,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:30:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '20:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'Setia City Mall (PREMIUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/setia-city-mall',
                'latitude'  => 0,
                'longitude' => 0,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['07:00:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '20:00:00']
                    ],
                    [
                        'days' => ['7'],
                        'time' => ['09:00:00', '20:00:00']
                    ],
                    [
                        'days' => ['PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'The Curve (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/the-curve',
                'latitude'  => 3.1576316,
                'longitude' => 101.6102591,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:30:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
            [
                'club'      => 'The Gardens Mall - Mid Valley (PLATINUM)',
                'url'       => 'https://www.fitnessfirst.com/my/en/clubs/the-gardens-mall-mid-valley',
                'latitude'  => 3.118219,
                'longitude' => 101.6759963,
                'opens'     => [
                    [
                        'days' => ['1', '2', '3', '4', '5'],
                        'time' => ['06:30:00', '22:00:00']
                    ],
                    [
                        'days' => ['6'],
                        'time' => ['07:00:00', '21:00:00']
                    ],
                    [
                        'days' => ['7', 'PH'],
                        'time' => ['08:00:00', '20:00:00']
                    ],
                ]
            ],
        ]
    ];

    /**
     * @return string
     */
    public function gym(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Gym',
            'slug_group'   => 'health',
            'slug'         => '/office/health/gym',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('health_gym', $data);
    }

    /**
     * @return string
     */
    public function gymFinder(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Gym Finder',
            'slug_group'   => 'health',
            'slug'         => '/office/health/gym',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('health_gym_finder', $data);
    }

    /**
     * @return ResponseInterface
     */
    public function gymFinderList(): ResponseInterface
    {
        helper('math');
        $holiday      = new JourneyHolidayModel();
        $latitude     = $this->request->getPost('latitude');
        $longitude    = $this->request->getPost('longitude');
        $city_code    = $this->request->getPost('city_code');
        $clubs        = $this->fitness_first[$city_code] ?? [];
        $country_code = ($city_code == 'SG') ? 'SG' : 'TH';
        $holidays     = $holiday->where('country_code', $country_code)->where('holiday_date >=', date(DATE_FORMAT_DB))->where('holiday_date_to <=', date(DATE_FORMAT_DB))->findAll();
        if (empty($holidays)) {
            $dow = date('N');
        } else {
            $dow = 'PH';
        }
        $result    = [];
        $dows      = [
            '', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'
        ];
        foreach ($clubs as $club) {
            $distance = calculateDistance($latitude, $longitude, $club['latitude'], $club['longitude']);
            $opens    = [];
            foreach ($club['opens'] as $group) {
                if (in_array($dow, $group['days'])) {
                    $opens['open']  = date(TIME_FORMAT_UI, strtotime('2025-01-01 ' . $group['time'][0]));
                    $opens['close'] = date(TIME_FORMAT_UI, strtotime('2025-01-01 ' . $group['time'][1]));
                }
            }
            $distance_index = intval($distance * 1000);
            $day            = $dows[$dow] ?? 'Holiday';
            $result[$distance_index] = [
                'club'     => $club['club'],
                'day'      => $day,
                'distance' => number_format($distance, 2),
                'open'     => $opens['open'] ?? '',
                'close'    => $opens['close'] ?? '',
                'url'      => $club['url']
            ];
        }
        ksort($result);
        return $this->response->setJSON([
            'data' => $result
        ]);
    }

    /**
     * @return string
     */
    public function vaccine(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Vaccine',
            'slug_group'   => 'health',
            'slug'         => '/office/health/vaccine',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('health_vaccine', $data);
    }

    /**
     * @return string
     */
    public function affirmation(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Affirmation',
            'slug_group'   => 'health',
            'slug'         => '/office/health/affirmation',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('health_affirmation', $data);
    }

    /**
     * This API retrieves immigration data and return for DataTables
     * Table: journey_master
     * @return ResponseInterface
     */
    public function affirmationList(): ResponseInterface
    {
        $model              = new HealthAffirmationModel();
        $columns            = [
            'affirmation_message',
            'id',
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    public function affirmationSave (): ResponseInterface
    {
        $session                     = session();
        $model                       = new HealthAffirmationModel();
        $id                          = $this->request->getPost('id');
        $data['affirmation_message'] = $this->request->getPost('affirmation_message');
        try {
            if (0 < $id) {
                $model->update($id, $data);
                return $this->response->setJSON([
                    'status' => 'success',
                    'toast'  => 'Message has been updated'
                ]);
            } else {
                $inserted_id = $model->insert($data);
                if ($inserted_id) {
                    return $this->response->setJSON([
                        'status' => 'success',
                        'toast'  => 'Message has been added',
                        'url'    => base_url($session->locale . '/office/health/activity')
                    ]);
                }
                return $this->response->setJSON([
                    'status' => 'success',
                    'toast'  => 'Failed to insert the message.',
                    'data'   => $data
                ]);
            }
        } catch (DatabaseException|ReflectionException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'ERROR: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @return string
     */
    public function activity(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Activity',
            'slug_group'   => 'health',
            'slug'         => '/office/health/activity',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'record_cate'  => (new HealthActivityModel())->getRecordCategories(),
            'record_types' => (new HealthActivityModel())->getRecordTypes()
        ];
        return view('health_activity', $data);
    }

    /**
     * This API retrieves immigration data and return for DataTables
     * Table: journey_master
     * @return ResponseInterface
     */
    public function activityList(): ResponseInterface
    {
        $model              = new HealthActivityModel();
        $columns            = [
            'id',
            'time_start_utc',
            'event_duration',
            'duration_from_prev_ejac',
            'record_type',
            'is_ejac',
            'spa_name',
            'price_amount',
            'event_notes',
            'event_location'
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $from               = $this->request->getPost('from') ?? '';
        $to                 = $this->request->getPost('to') ?? '';
        $record_type        = $this->request->getPost('record_type') ?? '';
        $is_ejac            = $this->request->getPost('is_ejac') ?? '';
        $result             = $model->getDataTables($start, $length, $order_column, $order_direction, $search_value, $from, $to, $record_type, $is_ejac);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    /**
     * @param string $mode
     * @param string $record_type
     * @return string
     */
    public function activityEdit(string $mode, string $record_type): string
    {
        $session     = session();
        $model       = new HealthActivityModel();
        $types       = $model->getRecordCategories();
        $page_title  = 'New ' . $types[$record_type];
        $record      = [
            'event_duration'          => '0',
            'duration_from_prev_ejac' => '0',
            'price_amount'            => '0.0',
            'price_tip'               => '0.0'
        ];
        if ('chastity' == $record_type || 'enlarge' == $record_type) {
            $open_rec    = $model->where('record_type', $record_type)->where('time_start_utc = time_end_utc')->where('event_duration = 0')->orderBy('id', 'DESC')->first();
            if (!empty($open_rec)) {
                $data        = [
                    'page_title'    => 'Update ' . ('chastity' == $record_type ? 'Chastity' : 'Enlargement Record'),
                    'mode'          => $record_type . '-end',
                    'slug_group'    => 'health',
                    'slug'          => '/office/health/activity',
                    'record'        => $record,
                    'prev'          => $open_rec,
                    'record_type'   => $record_type,
                    'user_session'  => $session->user,
                    'roles'         => $session->roles,
                    'current_role'  => $session->current_role,
                    'configuration' => $model->getConfigurations(),
                    'record_cate'   => $model->getRecordCategories(),
                    'record_types'  => $model->getRecordTypes()
                ];
                return view('health_activity_edit', $data);
            }
        }
        $prev        = $model->where('is_ejac', 'Y')->orderBy('time_start_utc', 'DESC')->first();
        $data        = [
            'page_title'    => $page_title,
            'mode'          => $mode,
            'slug_group'    => 'health',
            'slug'          => '/office/health/activity',
            'record'        => $record,
            'prev'          => $prev,
            'record_type'   => $record_type,
            'user_session'  => $session->user,
            'roles'         => $session->roles,
            'current_role'  => $session->current_role,
            'configuration' => $model->getConfigurations(),
            'record_cate'   => $model->getRecordCategories(),
            'record_types'  => $model->getRecordTypes()
        ];
        return view('health_activity_edit', $data);
    }

    public function activitySave(): ResponseInterface
    {
        $session = session();
        $model   = new HealthActivityModel();
        $mode    = $this->request->getPost('mode');
        $data    = [];
        if ('chastity-end' == $mode || 'enlarge-end' == $mode) {
            $fields  = [
                'id',
                'time_end_utc',
                'event_duration',
                'event_notes',
                'event_location'
            ];
            $id = $this->request->getPost('id');
            foreach ($fields as $field) {
                $data[$field] = htmlspecialchars($this->request->getPost($field));
                $data[$field] = str_replace("'", '’', $data[$field]);
                if (empty($data[$field])) {
                    unset($data[$field]);
                }
            }
            try {
                $model->update($id, $data);
                return $this->response->setJSON([
                    'status' => 'success',
                    'toast'  => 'Activity has been updated',
                    'url'    => base_url($session->locale . '/office/health/activity')
                ]);
            } catch (DatabaseException|ReflectionException $e) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'toast'  => 'ERROR: ' . $e->getMessage()
                ]);
            }
        }
        $fields  = [
            'journey_id',
            'time_start_utc',
            'time_end_utc',
            'event_timezone',
            'event_duration',
            'duration_from_prev_ejac',
            'record_type',
            'event_type',
            'is_ejac',
            'spa_name',
            'spa_type',
            'currency_code',
            'price_amount',
            'price_tip',
            'event_notes',
            'event_location'
        ];
        foreach ($fields as $field) {
            $data[$field] = htmlspecialchars($this->request->getPost($field));
            $data[$field] = str_replace("'", '’', $data[$field]);
            if (empty($data[$field])) {
                unset($data[$field]);
            }
        }
        try {
            $inserted_id = $model->insert($data);
            if ($inserted_id) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'toast'  => 'Activity has been added',
                    'url'    => base_url($session->locale . '/office/health/activity')
                ]);
            }
            return $this->response->setJSON([
                'status' => 'success',
                'toast'  => 'Failed to insert the activity.',
                'data'   => $data
            ]);
        } catch (DatabaseException|ReflectionException $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'toast'  => 'ERROR: ' . $e->getMessage()
            ]);
        }
    }

    public function measurement(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'Measurement',
            'slug_group'   => 'health',
            'slug'         => '/office/health/measurement',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('health_measurement', $data);
    }

    public function measurementList(): ResponseInterface
    {
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => 0,
            'recordsFiltered' => 0,
            'data'            => []
        ]);
    }

    public function mbti(): string
    {
        $session = session();
        $model   = new HealthMbtiModel();
        $recs    = $model->findAll();
        $data    = [
            'page_title'   => 'MBTI',
            'slug_group'   => 'health-forms',
            'slug'         => '/office/health/mbti',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role,
            'records'      => $recs
        ];
        return view('health_mbti', $data);
    }

    public function phq9(): string
    {
        $session = session();
        $data    = [
            'page_title'   => 'PHQ-9',
            'slug_group'   => 'health-forms',
            'slug'         => '/office/health/phq9',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('health_phq9', $data);
    }

    /**
     * View Ooca Visit Log List
     * @return string
     */
    public function ooca(): string
    {
        $session    = session();
        $data       = [
            'page_title'   => 'OOCA Visit Log',
            'slug_group'   => 'health-forms',
            'slug'         => '/office/health/ooca',
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('health_ooca', $data);
    }

    /**
     * List the Visit Log
     * @return ResponseInterface
     */
    public function oocaList(): ResponseInterface
    {
        $ooca_model = new OocaLogModel();
        $columns    = [
            '',
            '',
            'visit_date',
            'psychologist_name'
        ];
        $order              = $this->request->getPost('order');
        $search             = $this->request->getPost('search');
        $start              = $this->request->getPost('start');
        $length             = $this->request->getPost('length');
        $order_column_index = $order[0]['column'] ?? 0;
        $order_column       = $columns[$order_column_index];
        $order_direction    = $order[0]['dir'] ?? 'desc';
        $search_value       = $search['value'];
        $year               = $this->request->getPost('year');
        $result             = $ooca_model->getDataTables($start, $length, $order_column, $order_direction, $search_value, $year);
        return $this->response->setJSON([
            'draw'            => $this->request->getPost('draw'),
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data']
        ]);
    }

    public function oocaEdit(int $id = 0): string
    {
        $session = session();
        $model   = new OocaLogModel();
        $title   = 'New OOCA Visit Log';
        $record  = [];
        if ($id > 0) {
            $title  = 'Edit OOCA Visit Log';
            $id     = $id / $model::ID_NONCE;
            $record = $model->find($id);
            if (empty($record)) {
                throw new PageNotFoundException('OOCA Visit Log not found');
            }
        }
        $data = [
            'page_title'    => $title,
            'slug_group'    => 'health-forms',
            'slug'          => '/office/health/ooca',
            'record'        => $record,
            'nonce'         => $model::ID_NONCE,
            'configuration' => $model->getConfigurations(),
            'user_session'  => $session->user,
            'roles'         => $session->roles,
            'current_role'  => $session->current_role
        ];
        return view('health_ooca_edit', $data);
    }

    /**
     * @throws ReflectionException
     */
    public function oocaSave(): ResponseInterface
    {
        $ooca_model    = new OocaLogModel();
        $log_model     = new LogActivityModel();
        $session       = session();
        $id            = $this->request->getPost('id');
        $data          = [];
        $fields        = [
            'visit_date',
            'psychologist_name',
            'note_what_happened',
            'note_what_i_said',
            'note_what_suggested',
        ];
        foreach ($fields as $field) {
            $data[$field] = $this->request->getPost($field);
            $data[$field] = str_replace("'", '’', $data[$field]);
            if (empty($data[$field])) {
                unset($data[$field]);
            }
        }
        if (0 < $id) {
            if ($ooca_model->update($id, $data)) {
                $log_model->insertTableUpdate('ooca_log', $id, $data, $session->user_id);
                $new_id = $id * $ooca_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'  => 'success',
                    'toast'   => 'Successfully updated the Ooca log.',
                    'redirect' => base_url($session->locale . '/office/health/ooca/view/' . $new_id)
                ]);
            }
        } else {
            $data['created_by'] = $session->user_id;
            // INSERT
            if ($id = $ooca_model->insert($data)) {
                $log_model->insertTableUpdate('ooca_log', $id, $data, $session->user_id);
                $new_id = $id * $ooca_model::ID_NONCE;
                return $this->response->setJSON([
                    'status'   => 'success',
                    'toast'    => 'Successfully created new Ooca log.',
                    'redirect' => base_url($session->locale . '/office/health/ooca/view/' . $new_id)
                ]);
            }
        }
        return $this->response->setJSON([
            'status'  => 'error',
            'toast'   => lang('System.status_message.generic_error')
        ])->setStatusCode(HTTP_STATUS_SOMETHING_WRONG);
    }

    /**
     * Retrieve OOCA record
     * @param int $id ID of the OOCA record to be retrieved.
     * @return string
     */
    public function oocaView(int $id): string
    {
        $session    = session();
        $ooca_model = new OocaLogModel();
        $id         = $id / $ooca_model::ID_NONCE;
        $record     = $ooca_model->find($id);
        if (empty($record)) {
            throw new PageNotFoundException('Ooca record not found');
        }
        $data       = [
            'page_title'   => 'OOCA Record - ' . date(DATE_FORMAT_UI, strtotime($record['visit_date'])),
            'slug_group'   => 'health-forms',
            'slug'         => '/office/health/ooca',
            'record'       => $record,
            'nonce'        => $ooca_model::ID_NONCE,
            'user_session' => $session->user,
            'roles'        => $session->roles,
            'current_role' => $session->current_role
        ];
        return view('health_ooca_view', $data);
    }

    /**
     * Export the counseling data
     * @param string $year
     * @return string
     */
    public function oocaExport(string $year): string
    {
        $session    = session();
        $ooca_model = new OocaLogModel();
        $from       = $year . '-01-01';
        $to         = $year . '-12-31';
        $records    = $ooca_model->where('visit_date >=', $from)->where('visit_date <=', $to)->findAll();
        $data       = [
            'records' => $records,
            'year'    => $year
        ];
        return view('health_ooca_export', $data);
    }

    /**
     * Generate the statistics
     * @return string
     */
    public function oocaStatistics(): string
    {
        $session    = session();
        $ooca_model = new OocaLogModel();
        $raw        = $ooca_model->orderBy('visit_date')->findAll();
        $freq_data  = [];
        $yr_total   = [];
        foreach ($raw as $item) {
            $date  = explode('-', $item['visit_date']);
            $month = intval($date[1]);
            $year  = $date[0];
            $freq_data[$year][$month] = (isset($freq_data[$year][$month]) ? $freq_data[$year][$month] + 1 : 1);
            $yr_total[$year]          = (isset($yr_total[$year]) ? $yr_total[$year] + 1 : 1);
        }
        $data       = [
            'page_title'    => 'OOCA Statistics',
            'slug_group'    => 'health-forms',
            'slug'          => '/office/health/ooca/statistics',
            'freq_data'     => $freq_data,
            'yr_total'      => $yr_total,
            'total_records' => count($raw),
            'user_session'  => $session->user,
            'roles'         => $session->roles,
            'current_role'  => $session->current_role
        ];
        return view('health_ooca_statistics', $data);
    }
}
