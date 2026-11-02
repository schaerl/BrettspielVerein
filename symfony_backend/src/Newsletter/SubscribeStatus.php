<?php

namespace App\Newsletter;

enum SubscribeStatus
{
    case SUCCESS;
    case ALREADY_REGISTERED;
    case ERROR;
}

