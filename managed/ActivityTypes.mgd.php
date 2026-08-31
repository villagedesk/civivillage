<?php

/**
 * Village service activity types.
 *
 * These are the thing the migration loader fails on if they are absent, and the thing that
 * currently only exists inside a dev fixture. Declaring them here is what makes the domain
 * model an installable artifact rather than a local accident.
 *
 * cleanup 'unused' — on uninstall, remove the type only if no activity references it.
 * A village that has logged 400 rides keeps its rides.
 *
 * update 'unmodified' — if an administrator renames a label in the UI, an upgrade leaves it
 * alone. Their words beat ours.
 */

declare(strict_types=1);

$types = [
    [
        'name' => 'village_ride',
        'label' => 'Ride Request',
        'icon' => 'fa-car',
        'description' => 'Transport to an appointment, errand or community activity. Roughly half of all village service volume.',
    ],
    [
        'name' => 'village_loan_closet',
        'label' => 'Loan Closet Request',
        'icon' => 'fa-wheelchair',
        'description' => 'Borrowing mobility or home-care equipment.',
    ],
    [
        'name' => 'village_service_request',
        'label' => 'Service Request',
        'icon' => 'fa-hands-helping',
        'description' => 'Home help, tech help, yard work, friendly visiting — anything that is not a ride or an equipment loan.',
    ],
    [
        'name' => 'village_office_time',
        'label' => 'Office Time',
        'icon' => 'fa-clock-o',
        'description' => 'A volunteer shift. Deliberately a plain activity rather than a CiviVolunteer dependency: no tenant has asked for rosters yet.',
    ],
    [
        'name' => 'village_intake',
        'label' => 'Member Intake',
        'icon' => 'fa-user-plus',
        'description' => 'The conversation that starts a membership.',
    ],
];

$managed = [];
foreach ($types as $type) {
    $managed[] = [
        'name' => 'OptionValue_'.$type['name'],
        'entity' => 'OptionValue',
        'cleanup' => 'unused',
        'update' => 'unmodified',
        'params' => [
            'version' => 4,
            'values' => [
                'option_group_id.name' => 'activity_type',
                'name' => $type['name'],
                'label' => $type['label'],
                'description' => $type['description'],
                'icon' => $type['icon'],
                'is_active' => true,
                'is_reserved' => false,
            ],
            'match' => ['option_group_id', 'name'],
        ],
    ];
}

return $managed;
