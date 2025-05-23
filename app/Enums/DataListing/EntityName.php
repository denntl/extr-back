<?php

namespace App\Enums\DataListing;

use App\Enums\NotificationTemplate\Event;

enum EntityName: string
{
    case User = 'user';
    case ClientMyCompanyBalanceTransactions = 'company-balance-transactions';
    case Team = 'team';
    case Company = 'company';
    case Application = 'application';
    case ApplicationStatistics = 'statistics';
    case DetailedStatistics = 'detailedStatistics';
    case ApplicationComments = 'applicationComments';
    case Bots = 'bots';
    case AllUsers = 'all-users';
    case PushTemplates = 'pushTemplates';
    case OnesignalSingleNotifications = 'onesignalSingleNotifications';
    case PushSingleNotifications = 'pushSingleNotifications';
    case PushRegularNotifications = 'pushRegularNotifications';
    case Roles = 'roles';
    case Permissions = 'permissions';
    case TemplateNotifications = 'template-notifications';
    case Notifications = 'notifications';
    case Domains = 'domains';
    case PushNotificationsStatistic = 'push-notifications-statistic';
    case ManageApplication = 'manage-application';
    case ManageApplicationComment = 'manage-application-comment';
    case ManageCompanyBalanceTransactions = 'manage-company-balance-transactions';
}
