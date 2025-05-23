<?php

namespace App\Services\Common\DataListing;

use App\Enums\DataListing\EntityName;
use App\Services\Common\DataListing\Entities\ClientApplicationCommentListing;
use App\Services\Common\DataListing\Entities\ClientApplicationsListing;
use App\Services\Common\DataListing\Entities\ClientDetailedStatisticsListing;
use App\Services\Common\DataListing\Entities\ClientMyCompanyBalanceTransactionsListing;
use App\Services\Common\DataListing\Entities\ClientOnesignalTemplatesSingleListing;
use App\Services\Common\DataListing\Entities\ManageApplicationCommentListing;
use App\Services\Common\DataListing\Entities\ManageApplicationsListing;
use App\Services\Common\DataListing\Entities\ManageCompanyBalanceTransactionsListing;
use App\Services\Common\DataListing\Entities\ManagePushNotificationStatisticsListing;
use App\Services\Common\DataListing\Entities\ManageDomainsListing;
use App\Services\Common\DataListing\Entities\ClientApplicationStatisticsListing;
use App\Services\Common\DataListing\Entities\ClientNotificationListing;
use App\Services\Common\DataListing\Entities\ManageCompaniesListing;
use App\Services\Common\DataListing\Entities\ManagePermissionListing;
use App\Services\Common\DataListing\Entities\ManageNotificationTemplatesListing;
use App\Services\Common\DataListing\Entities\ManagePushTemplatesListing;
use App\Services\Common\DataListing\Entities\ManageTelegraphBotsListing;
use App\Services\Common\DataListing\Entities\ManageUsersListing;
use App\Services\Common\DataListing\Entities\ClientRegularPushNotificationsListing;
use App\Services\Common\DataListing\Entities\ManageRolesListing;
use App\Services\Common\DataListing\Entities\ClientSinglePushNotificationsListing;
use App\Services\Common\DataListing\Entities\ClientTeamsListing;
use App\Services\Common\DataListing\Entities\ClientUsersListing;

class DataListingFactory
{
    /**
     * @param string $entity
     * @return EntityName
     */
    public static function mapEntityName(string $entity): EntityName
    {
        return match ($entity) {
            'user' => EntityName::User,
            'team' => EntityName::Team,
            'company' => EntityName::Company,
            'application' => EntityName::Application,
            'application-comments' => EntityName::ApplicationComments,
            'push-templates' => EntityName::PushTemplates,
            'bots' => EntityName::Bots,
            EntityName::AllUsers->value => EntityName::AllUsers,
            'onesignal-single-notifications' => EntityName::OnesignalSingleNotifications,
            'push-single-notifications' => EntityName::PushSingleNotifications,
            'push-regular-notifications' => EntityName::PushRegularNotifications,
            'roles' => EntityName::Roles,
            EntityName::TemplateNotifications->value => EntityName::TemplateNotifications,
            EntityName::Notifications->value => EntityName::Notifications,
            EntityName::ApplicationStatistics->value => EntityName::ApplicationStatistics,
            'detailed-statistics' => EntityName::DetailedStatistics,
            'permissions' => EntityName::Permissions,
            'domains' => EntityName::Domains,
            EntityName::PushNotificationsStatistic->value => EntityName::PushNotificationsStatistic,
            EntityName::ManageApplication->value => EntityName::ManageApplication,
            EntityName::ManageApplicationComment->value => EntityName::ManageApplicationComment,
            EntityName::ManageCompanyBalanceTransactions->value => EntityName::ManageCompanyBalanceTransactions,
            EntityName::ClientMyCompanyBalanceTransactions->value => EntityName::ClientMyCompanyBalanceTransactions,
            default => throw new \InvalidArgumentException('Unknown entity'),
        };
    }

    /**
     * @param EntityName $entity
     * @param ListingFilterModel $request
     * @return ListingServiceInterface
     */
    public static function init(EntityName $entity, ListingFilterModel $request): ListingServiceInterface
    {
        return match ($entity) {
            EntityName::User => new ClientUsersListing($request),
            EntityName::Team => new ClientTeamsListing($request),
            EntityName::Company => new ManageCompaniesListing($request),
            EntityName::Application => new ClientApplicationsListing($request),
            EntityName::ApplicationStatistics => new ClientApplicationStatisticsListing($request),
            EntityName::DetailedStatistics => new ClientDetailedStatisticsListing($request),
            EntityName::ApplicationComments => new ClientApplicationCommentListing($request),
            EntityName::Bots => new ManageTelegraphBotsListing($request),
            EntityName::AllUsers => new ManageUsersListing($request),
            EntityName::PushTemplates => new ManagePushTemplatesListing($request),
            EntityName::OnesignalSingleNotifications => new ClientOnesignalTemplatesSingleListing($request),
            EntityName::PushSingleNotifications => new ClientSinglePushNotificationsListing($request),
            EntityName::PushRegularNotifications => new ClientRegularPushNotificationsListing($request),
            EntityName::Permissions => new ManagePermissionListing($request),
            EntityName::TemplateNotifications => new ManageNotificationTemplatesListing($request),
            EntityName::Notifications => new ClientNotificationListing($request),
            EntityName::Roles => new ManageRolesListing($request),
            EntityName::Domains => new ManageDomainsListing($request),
            EntityName::PushNotificationsStatistic => new ManagePushNotificationStatisticsListing($request),
            EntityName::ManageApplication => new ManageApplicationsListing($request),
            EntityName::ManageApplicationComment => new ManageApplicationCommentListing($request),
            EntityName::ManageCompanyBalanceTransactions => new ManageCompanyBalanceTransactionsListing($request),
            EntityName::ClientMyCompanyBalanceTransactions => new ClientMyCompanyBalanceTransactionsListing($request),
        };
    }
}
