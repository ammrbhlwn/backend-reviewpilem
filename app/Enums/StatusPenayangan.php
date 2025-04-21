<?php

namespace App\Enums;

enum StatusPenayangan: string
{
    case NotYetAired = 'not_yet_aired';
    case Airing = 'airing';
    case FinishedAiring = 'finished_airing';
}
