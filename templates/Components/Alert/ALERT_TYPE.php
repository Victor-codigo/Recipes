<?php

declare(strict_types=1);

namespace App\Templates\Components\Alert;

enum ALERT_TYPE
{
    case DANGER;
    case INFO;
    case SUCCESS;
}
