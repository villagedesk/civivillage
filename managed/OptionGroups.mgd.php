<?php

/**
 * Option values the profile adds to CiviCRM's own option groups.
 *
 * Custom-field options are declared inline on their fields (see CustomFields.mgd.php) --
 * a separately-managed OptionGroup referenced by a CustomField creates an ordering
 * dependency that does not reconcile.
 *
 * What is NOT here: service areas and the service catalog. Every village's geography and
 * service mix differ, so those are tenant manifest values. The rule: if a second village
 * would plausibly want it different, it is tenant config, not profile.
 */

declare(strict_types=1);

$managed = [];

/*
 * Case status: a request received and not met.
 *
 * This is the metric funders ask about — "how many requests came in, how many did you fill"
 * — and recording it as Resolved makes that question unanswerable. It is the one case status
 * a village needs beyond CiviCRM's stock Ongoing / Resolved / Urgent.
 */
$managed[] = [
    'name' => 'OptionValue_case_status_could_not_fill',
    'entity' => 'OptionValue',
    'cleanup' => 'unused',
    'update' => 'unmodified',
    'params' => [
        'version' => 4,
        'values' => [
            'option_group_id.name' => 'case_status',
            'name' => 'village_could_not_fill',
            'label' => 'Could not fill',
            'description' => 'The village received this request and was unable to meet it. Distinct from Resolved.',
            'grouping' => 'Closed',
            'is_active' => true,
            'is_reserved' => false,
        ],
        'match' => ['option_group_id', 'name'],
    ],
];

return $managed;
