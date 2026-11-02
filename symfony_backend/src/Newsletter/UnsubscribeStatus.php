<?php

namespace App\Newsletter;

enum UnsubscribeStatus
{
    case SUCCESSFULLY_DELETED;
    case ALREADY_DELETED;
    case TOKEN_WRONG;
}

