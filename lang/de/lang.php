<?php return [
    'plugin' => [
        'name' => 'FacebookEvents',
        'description' => 'Facebook Seiten- oder manuelle Events einfach darstellen.'
    ],
        'manualevents'=> [
        // Fields
        'description_section_label' => 'Beschreibung',
        
        'name_CommentAbove' => 'Titel des Event <b>(benötigt)</b>',
        
        'description_label' => 'Beschreibung',
        'description_CommentAbove' => 'Beschreibung des Events (HTML ist erlaubt)',
        
        'image_label' => 'Bild',
        
        'external_url_label' => 'Externe URL',
        'external_url_commentAbove' => 'Link zu einer externen Event Seite (optional)',
        
        'time_section_label' =>  'Zeit',
        'start_time_label' => 'Start Zeit',
        'start_time_comment' => 'Start Datum/Zeit des Events (<b>benötigt</b>)<br><br><b>Achtung:</b> Bitte wähle ein Datum <b>und</b> eine Zeit sonst wird das aktuelle Datum bzw. die aktuelle Zeit übernommen.',
        
        'end_time_label' => 'Starting time',
        'end_time_comment' => 'Ende Date/Time des Events (<b>optional</b>)<br><br><b>Achtung:</b> Bitte wähle ein Datum <b>und</b> eine Zeit sonst wird das aktuelle Datum bzw. die aktuelle Zeit übernommen.',

        //Columns
        'event_name' => 'Event Name',
        'description' => 'Beschreibung',
        'image' => 'Bild',
        'external_url' => 'Externe URL',
        'start_time' => 'Start Zeit',
        'end_time' => 'End Zeit'

    ]
];