<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Vacation search filter options
    |--------------------------------------------------------------------------
    |
    | Single source of truth for the slugs used in search forms/URLs, the
    | internal codes used by DestinationRecommendationService for scoring,
    | and the Slovak labels shown in the UI.
    |
    */

    'holiday_types' => [
        'more-a-plaz' => ['code' => 'beach', 'label' => 'More a pláž'],
        'hory-a-priroda' => ['code' => 'nature', 'label' => 'Hory a príroda'],
        'historicke-mesta' => ['code' => 'history', 'label' => 'Historické mestá'],
        'mestsky-vylet' => ['code' => 'city', 'label' => 'Mestský výlet'],
        'aktivity-a-dobrodruzstvo' => ['code' => 'adventure', 'label' => 'Aktivity a dobrodružstvo'],
    ],

    'temperatures' => [
        'horuco' => ['code' => 'hot', 'label' => 'Horúco (30 °C+)'],
        'teplo' => ['code' => 'warm', 'label' => 'Teplo (20-29 °C)'],
        'prijemne' => ['code' => 'mild', 'label' => 'Príjemne (10-19 °C)'],
        'jedno' => ['code' => 'any', 'label' => 'Jedno mi to'],
    ],

    'distances' => [
        'do-3h' => ['code' => '3h', 'label' => 'Do 3 hodín letu'],
        'do-5h' => ['code' => '5h', 'label' => 'Do 5 hodín letu'],
        'kdekolvek' => ['code' => 'any', 'label' => 'Kdekoľvek'],
    ],

];
