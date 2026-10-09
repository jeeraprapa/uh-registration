@php
    $policyText=e($text);
    $policyText=str_replace(
        ['City Dynamic','Swire Properties','Swire Hotels'],
        ['<a href="https://www.uhresidencesbangkok.com/privacy-policy/">City Dynamic</a>','<a href="https://www.swireproperties.com/en/privacy-policy/">Swire Properties</a>','<a href="https://www.swirehotels.com/en/Privacy-Policy/">Swire Hotels</a>'],
        $policyText
    );
@endphp
{!! $policyText !!}
