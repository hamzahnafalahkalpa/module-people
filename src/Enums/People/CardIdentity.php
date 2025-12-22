<?php

namespace Hanafalah\ModulePeople\Enums\People;

enum CardIdentity: string
{
    case NIK      = 'nik';
    case SIM      = 'sim';
    case PASSPORT = 'passport';
    case VISA     = 'visa';
    case KK       = 'kk';
    case NPWP     = 'npwp';
}
