<?php

namespace App\Enums\Authorization;

enum PermissionName: string
{
    /**
     * Listing permissions
     * rule: <who><EntitySingular><Action>
     * example: manageApplicationRead
     */
    case ClientApplicationRead = 'clientApplicationRead';
    case ClientApplicationStatisticRead = 'clientApplicationStatisticRead';
    case ClientNotificationRead = 'clientNotificationRead';
    case ClientTeamRead = 'clientTeamRead';
    case ClientUserRead = 'clientUserRead';
    case ClientCompanyBalanceTransactionsRead = 'clientCompanyBalanceTransactionsRead';
    case ManageCompanyRead = 'manageCompanyRead';
    case ManageDomainRead = 'manageDomainRead';
    case ManageNotificationTemplateRead = 'manageNotificationTemplateRead';
    case ManagePermissionRead = 'managePermissionRead';
    case ManagePushTemplateRead = 'managePushTemplateRead';
    case ClientRegularPushNotificationRead = 'clientRegularPushNotificationRead';
    case ManageRoleRead = 'manageRoleRead';
    case ClientSinglePushNotificationRead = 'clientSinglePushNotificationRead';
    case ManageTelegraphBotRead = 'manageTelegraphBotRead';
    case ManageUserRead = 'manageUserRead';
    case ManageTariffRead = 'manageTariffRead';
    case ManageApplicationRead = 'manageApplicationRead';

    /**
     * Section Manage actions
     * rule: manage<EntitySingular><Action>
     */
    case ManageCompanyUpdate = 'manageCompanyUpdate';
    case ManageCompanyManualBalanceDeposit = 'manageCompanyManualBalanceDeposit';
    case ManageCompanyBalanceTransactionsRead = 'manageCompanyBalanceTransactionsRead';
    case ManageTariffUpdate = 'manageTariffUpdate';
    case ManageNotificationTemplateCreate = 'manageNotificationTemplateCreate';
    case ManageNotificationTemplateSendMessage = 'manageNotificationTemplateSendMessage';
    case ManageNotificationTemplateDelete = 'manageNotificationTemplateDelete';
    case ManageNotificationTemplateUpdate = 'manageNotificationTemplateUpdate';
    case ManagePushTemplateCreate = 'managePushTemplateCreate';
    case ManagePushTemplateUpdate = 'managePushTemplateUpdate';
    case ManageRoleCreate = 'manageRoleCreate';
    case ManageRoleDelete = 'manageRoleDelete';
    case ManageRoleUpdate = 'manageRoleUpdate';
    case ManageTelegramBotUpdate = 'manageTelegramBotUpdate';
    case ManageUserUpdate = 'manageUserUpdate';
    case ManageUserLoginAsUser = 'manageUserLoginAsUser';
    case ManageDomainCreate = 'manageDomainCreate';
    case ManageDomainUpdate = 'manageDomainUpdate';
    case ManagePushNotificationStatisticRead = 'managePushNotificationStatisticRead';
    case ManageApplicationSave = 'manageApplicationSave';
    case ManageApplicationDelete = 'manageApplicationDelete';
    case ManageApplicationClone = 'manageApplicationClone';

    /**
     * Section Client actions
     * rule: client<EntitySingular><Action>
     */
    case ClientApplicationSave = 'clientApplicationSave';
    case ClientApplicationDelete = 'clientApplicationDelete';
    case ClientApplicationClone = 'clientApplicationClone';
    case ClientCompanyUpdate = 'clientCompanyUpdate';
    case ClientCompanyInvite = 'clientCompanyInvite';
    case ClientCompanyRead = 'clientCompanyRead';
    case ClientNotificationTelegramBotUpdate = 'clientNotificationTelegramBotUpdate';
    case ClientNotificationActivate = 'clientNotificationActivate';
    case ClientTeamCreate = 'clientTeamCreate';
    case ClientTeamUpdate = 'clientTeamUpdate';
    case ClientTeamDelete = 'clientTeamDelete';
    case ClientTariffRead = 'clientTariffRead';
    case ClientApplicationOwnerUpdate = 'clientApplicationOwnerUpdate';
    case ClientTeamInvite = 'clientTeamInvite';
    case ClientUserDeactivate = 'clientUserDeactivate';
    case ClientPushNotificationCreate = 'clientPushNotificationCreate';
    case ClientPushNotificationClone = 'clientPushNotificationClone';
    case ClientPushNotificationDelete = 'clientPushNotificationDelete';
    case ClientPushNotificationUpdate = 'clientPushNotificationUpdate';
    case ClientPushNotificationRead = 'clientPushNotificationRead';
}
