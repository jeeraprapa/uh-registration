<?php

return [
    // Provisional country/title/consent rules require client sign-off before production.
    'policy_confirmed' => env('REGISTRATION_POLICY_CONFIRMED', false),
    'timezone' => env('EVENT_TIMEZONE', 'Asia/Hong_Kong'),
    'guests_include_registrant' => env('GUESTS_INCLUDE_REGISTRANT', true),
    'mail_mode' => env('REGISTRATION_MAIL_MODE', 'local'),
    'venue' => 'Sky Lounge, 49/F, 88 Queensway, Admiralty, Upper House Hong Kong',
    'titles' => ['Mr.', 'Mrs.', 'Ms.', 'Dr.'],
    'countries' => [
        ['code' => 'HK', 'name' => 'Hong Kong', 'dial_code' => '+852'],
        ['code' => 'TH', 'name' => 'Thailand', 'dial_code' => '+66'],
        ['code' => 'CN', 'name' => 'Chinese Mainland', 'dial_code' => '+86'],
        ['code' => 'SG', 'name' => 'Singapore', 'dial_code' => '+65'],
        ['code' => 'GB', 'name' => 'United Kingdom', 'dial_code' => '+44'],
        ['code' => 'US', 'name' => 'United States', 'dial_code' => '+1'],
    ],
    'referral_sources' => ['eDirect Mail', 'Print Advertising', 'Online Advertising', 'Digital Signage', 'Others'],
    'consents' => [
        'privacy' => ['version' => 'draft-1', 'required' => true, 'text' => 'I acknowledge and accept the privacy policies of City Dynamic, Swire Properties, and Swire Hotels.'],
        'marketing_direct' => ['version' => 'draft-1', 'required' => false, 'text' => 'I agree to the use of my full name and contact information by City Dynamic for direct marketing.'],
        'marketing_sharing' => ['version' => 'draft-1', 'required' => false, 'text' => 'I agree to City Dynamic disclosing my full name and contact information to Swire Properties and Swire Hotels for direct marketing.'],
        'mainland_transfer' => ['version' => 'draft-1', 'required' => false, 'text' => 'For Chinese Mainland customers: I consent to the sharing of my personal information with service providers and partners, and its transfer outside Chinese Mainland.'],
    ],
];
