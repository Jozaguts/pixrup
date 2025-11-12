<?php

namespace App\Domain\Usage\Enums;

enum UsageAction: string
{
    case APPRAISAL = 'appraisal';
    case GLOW_UP = 'glowup';
    case SPY_HUNT = 'spyhunt';
    case REPORT = 'report';
}
