<?php

namespace App\Enums\Authorization;

enum RoleName: string
{
    case Admin = 'Администратор';
    case Manager = 'Саппорт';

    case CompanyLead = 'Руководитель компании';
    case TeamLead = 'Руководитель команды';
    case Affiliate = 'Аффилиат';
    case Buyer = 'Байер';
    case ClientManager = 'Менеджер';
}
