<?php

/**
 * Custom field groups.
 *
 * SAFETY NOTE ON cleanup: every group here is 'never'.
 *
 * A CustomGroup owns a database table. Setting cleanup to 'always' or 'unused' means an
 * uninstall can DROP that table — and with it 730 members' consent answers, every recorded
 * mileage figure, and the audit trail of what the old system said. Uninstalling an extension
 * must never be able to destroy constituent data. If a group genuinely needs removing, that
 * is a deliberate, reviewed migration, not a side effect.
 */

declare(strict_types=1);

/** @return array<int, array<string, mixed>> */
$field = static function (string $group, string $name, string $label, string $dataType, string $htmlType, array $extra = []): array {
    return [
        'name' => 'CustomField_'.$group.'_'.$name,
        'entity' => 'CustomField',
        'cleanup' => 'never',
        'update' => 'unmodified',
        'params' => [
            'version' => 4,
            'values' => [
                'custom_group_id.name' => $group,
                'name' => $name,
                'label' => $label,
                'data_type' => $dataType,
                'html_type' => $htmlType,
                'is_active' => true,
                'is_searchable' => true,
                'is_required' => false,
            ] + $extra,
            'match' => ['custom_group_id', 'name'],
        ],
    ];
};

return [
    // -----------------------------------------------------------------------------------
    // village_member — what a village asks about a person.
    // -----------------------------------------------------------------------------------
    [
        'name' => 'CustomGroup_village_member',
        'entity' => 'CustomGroup',
        'cleanup' => 'never',
        'update' => 'unmodified',
        'params' => [
            'version' => 4,
            'values' => [
                'name' => 'village_member',
                'title' => 'Village Member',
                'extends' => 'Individual',
                'style' => 'Inline',
                'collapse_display' => false,
                'is_active' => true,
                'is_public' => false,
                'weight' => 10,
            ],
            'match' => ['name'],
        ],
    ],

    /*
     * Consent, and it is tri-state on purpose.
     *
     * true = asked and agreed. false = asked and declined. NULL = never asked, which is NOT
     * the same as declined and must never be rendered as one. In the RappAtHome import, 363
     * of 730 people were never asked about the directory and 426 were never asked about
     * donor listing — so a directory that publishes anything other than explicit `true`
     * publishes people who never agreed.
     *
     * Hence: no default_value, and is_required false. A Boolean custom field stores
     * 0 / 1 / NULL, and the NULL is the whole point.
     */
    $field('village_member', 'mmv_directory_listing', 'May we list you in the member directory?', 'Boolean', 'Radio', [
        'help_post' => 'Leave unanswered until the member has actually been asked. Unanswered is treated as “do not publish”.',
    ]),
    $field('village_member', 'mmv_donor_listing', 'May we name you as a donor?', 'Boolean', 'Radio', [
        'help_post' => 'Leave unanswered until the member has actually been asked.',
    ]),
    $field('village_member', 'mmv_pronouns', 'Pronouns', 'String', 'Text', ['text_length' => 64]),
    $field('village_member', 'village_sponsor', 'Referred by', 'String', 'Text', ['text_length' => 128]),

    // -----------------------------------------------------------------------------------
    // village_service — extends Activity, scoped to the service types.
    //
    // This group exists because RappAtHome went to QuickBase to record mileage. Note what is
    // absent: there is no hours field. activity.duration is core, is in minutes, and is
    // already reportable — a custom hours field would shadow it and split the reporting.
    //
    // VERIFY ON A RUNNING INSTALL: extends_entity_column_value scopes the group to specific
    // activity types. The :name pseudoconstant suffix below should resolve the option values
    // to their IDs; confirm the group actually appears on a Ride Request and nowhere else.
    // -----------------------------------------------------------------------------------
    [
        'name' => 'CustomGroup_village_service',
        'entity' => 'CustomGroup',
        'cleanup' => 'never',
        'update' => 'unmodified',
        'params' => [
            'version' => 4,
            'values' => [
                'name' => 'village_service',
                'title' => 'Service Details',
                'extends' => 'Activity',
                'extends_entity_column_value:name' => [
                    'village_ride',
                    'village_loan_closet',
                    'village_service_request',
                ],
                'style' => 'Inline',
                'collapse_display' => false,
                'is_active' => true,
                'is_public' => false,
                'weight' => 20,
            ],
            'match' => ['name'],
        ],
    ],

    $field('village_service', 'village_mileage', 'Miles driven', 'Float', 'Text', [
        'help_post' => 'Round trip unless noted. Volunteer time is recorded in the activity’s own Duration field, not here.',
    ]),
    $field('village_service', 'village_reimbursed', 'Mileage reimbursed?', 'Boolean', 'Radio', [
        'default_value' => '0',
    ]),
    /*
     * Options are declared inline rather than pointed at a separately-managed OptionGroup.
     *
     * Referencing an option group that another managed record creates in the same run
     * introduces an ordering dependency that does not reconcile: the field gets created, the
     * Managed row does not, and the rest of the file is abandoned. Inline option_values let
     * CiviCRM create and own the group, which removes the dependency entirely.
     *
     * These values are profile-level constants. Anything a village would genuinely want
     * different -- service areas, the service catalog -- is tenant manifest config and does
     * not belong in an option group here at all.
     */
    $field('village_service', 'village_destination_type', 'Destination type', 'String', 'Select', [
        'option_values' => [
            'medical' => 'Medical',
            'grocery' => 'Grocery / shopping',
            'social' => 'Social / community',
            'errand' => 'Errand',
            'other' => 'Other',
        ],
        'help_post' => 'Funder reporting keys off this. Keep it filled in.',
    ]),
    $field('village_service', 'village_request_channel', 'How was this requested?', 'String', 'Select', [
        'option_values' => [
            'phone' => 'Phone',
            'email' => 'Email',
            'in_person' => 'In person',
            'web_form' => 'Web form',
        ],
    ]),

    // -----------------------------------------------------------------------------------
    // legacy_import — what the old system said. Collapsed, and read by nothing.
    //
    // Rule: no application code may read these. They exist so that a question six months
    // from now has an answer, and for nothing else.
    // -----------------------------------------------------------------------------------
    [
        'name' => 'CustomGroup_legacy_import',
        'entity' => 'CustomGroup',
        'cleanup' => 'never',
        'update' => 'unmodified',
        'params' => [
            'version' => 4,
            'values' => [
                'name' => 'legacy_import',
                'title' => 'Legacy Import',
                'extends' => 'Individual',
                'style' => 'Inline',
                'collapse_display' => true,
                'is_active' => true,
                'is_public' => false,
                'weight' => 90,
                'help_pre' => 'Values carried over from the system this village migrated from. Kept for audit; not used by the application.',
            ],
            'match' => ['name'],
        ],
    ],

    $field('legacy_import', 'legacy_login_name', 'Legacy login name', 'String', 'Text', ['text_length' => 128]),
    $field('legacy_import', 'legacy_member_level', 'Legacy member level', 'String', 'Text', ['text_length' => 64]),
    $field('legacy_import', 'legacy_chapter', 'Legacy chapter', 'String', 'Text', ['text_length' => 128]),
    /*
     * The expiry date the old system carried. Recorded, deliberately not acted on: in the
     * RappAtHome export 520 of 730 sat exactly ten years past the join date and 113 were
     * already in the past while the source still reported every one of them as active.
     * Importing those as real membership end dates would have expired 113 people on day one.
     */
    $field('legacy_import', 'legacy_expiry_date', 'Legacy expiry date', 'Date', 'Select Date', [
        'help_post' => 'Informational only. Not used to calculate membership status.',
    ]),
];
