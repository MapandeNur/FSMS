<?php

namespace App\Enums;

enum DocumentType: string
{
    case ATTACHMENT_LETTER = 'attachment_letter';
    case INSTITUTION_ID = 'institution_id';
    case NATIONAL_ID = 'national_id';
    case CV = 'cv';
    case OTHER = 'other';
}