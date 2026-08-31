<?php

/**
 * Relationship types the village profile owns.
 *
 * Head of Household for / Household Member of / Spouse of are CiviCRM core and are
 * deliberately NOT declared here — declaring a stock type risks creating a duplicate that
 * shadows the real one.
 *
 * Emergency Contact is the one the migration genuinely needs: the RappAtHome ClubExpress
 * export produces 419 of these relationships and the load fails without it.
 *
 * Village Coordinator and Service Volunteer exist for CiviCase roles. Note the direction
 * convention: CaseType XML references the name a role has AS SEEN FROM THE CLIENT, which is
 * name_b_a. Declaring these backwards yields case roles that silently never populate.
 */

declare(strict_types=1);

$types = [
    [
        'key' => 'emergency_contact',
        'name_a_b' => 'Emergency Contact for',
        'name_b_a' => 'Emergency Contact is',
        'description' => 'Next of kin or nominated contact. Granting record access is a separate, deliberate decision — never set is_permission_a_b by default.',
    ],
    [
        'key' => 'village_coordinator',
        'name_a_b' => 'Village Coordinator is',
        'name_b_a' => 'Village Coordinator',
        'description' => 'Staff member who owns a service case for this member. Case creator and manager.',
    ],
    [
        'key' => 'service_volunteer',
        'name_a_b' => 'Service Volunteer is',
        'name_b_a' => 'Service Volunteer',
        'description' => 'Volunteer delivering a service on a case. Deliberately not the case manager: a volunteer answers for their part, never for the case.',
    ],
];

$managed = [];
foreach ($types as $type) {
    $managed[] = [
        'name' => 'RelationshipType_'.$type['key'],
        'entity' => 'RelationshipType',
        'cleanup' => 'unused',
        'update' => 'unmodified',
        'params' => [
            'version' => 4,
            'values' => [
                'name_a_b' => $type['name_a_b'],
                'label_a_b' => $type['name_a_b'],
                'name_b_a' => $type['name_b_a'],
                'label_b_a' => $type['name_b_a'],
                'contact_type_a' => 'Individual',
                'contact_type_b' => 'Individual',
                'description' => $type['description'],
                'is_active' => true,
                'is_reserved' => false,
            ],
            'match' => ['name_a_b', 'name_b_a'],
        ],
    ];
}

return $managed;
