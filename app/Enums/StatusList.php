<?php

namespace App\Enums;

enum StatusList: string
{
    case PlanToWatch = 'plan_to_watch';
    case Watching = 'watching';
    case Completed = 'completed';
    case OnHold = 'on_hold';
    case Dropped = 'dropped';
}
