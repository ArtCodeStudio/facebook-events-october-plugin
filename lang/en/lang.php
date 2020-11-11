<?php return [
    'plugin' => [
        'name' => 'FacebookEvents',
        'description' => 'Easily display Facebook Page or manual created events'
    ],
    'manualevents'=> [
        // Fields
        'description_section_label' => 'Description',
        
        'name_CommentAbove' => 'title of the Event <b>(required)</b>',
        
        'description_label' => 'Description',
        'description_CommentAbove' => 'Description of the event (HTML is allowed)',
        
        'image_label' => 'Image',
        
        'external_url_label' => 'External URL',
        'external_url_commentAbove' => 'Link to an external Event page (optional)',
        
        'time_section_label' =>  'Time',
        'start_time_label' => 'Starting time',
        'start_time_comment' => 'Date/Time for the start of the event (<b>required</b>)<br><br><b>Attention:</b> Please choose a date <b>and</b> a time. Otherwise, the current date, or the current time will be filled in.',
        
        'end_time_label' => 'Starting time',
        'end_time_comment' => 'Date/Time for the end of the event (<b>optional</b>)<br><br><b>Attention:</b> Please choose a date <b>and</b> a time. Otherwise, the current date, or the current time will be filled in.',

        //Columns
        'event_name' => 'Event Name',
        'description' => 'Description',
        'image' => 'Image',
        'external_url' => 'External URL',
        'start_time' => 'Starting time',
        'end_time' => 'Ending time'

    ]
];