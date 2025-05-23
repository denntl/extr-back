<?php

namespace App\Services\Client\PushNotification;

use App\Enums\PushNotification\Status;
use App\Enums\PushNotification\Type;
use App\Models\PushNotification;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationArgumentException;
use App\Services\Common\OneSignal\Exceptions\PushNotificationException;
use App\Services\Common\OneSignal\OneSignalNotificationService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class PushNotificationService
{
    public static function getStatusList(): array
    {
        return [
            ['value' => Status::Draft, 'label' => 'Черновик'],
            ['value' => Status::Wait, 'label' => 'Ожидает'],
            ['value' => Status::Sent, 'label' => 'Отправлено'],
        ];
    }

    public static function getTypeList(): array
    {
        return [
            ['value' => Type::Single, 'label' => 'Одноразовый'],
            ['value' => Type::Regular, 'label' => 'Регулярный'],
        ];
    }

    /**
     * @param int $id
     * @return PushNotification
     */
    public function getById(int $id): PushNotification
    {
        return PushNotification::findOrFail($id);
    }

    /**
     * @param array $data
     * @param int $creator_id
     * @return PushNotification
     * @throws InvalidPushNotificationArgumentException
     * @throws PushNotificationException
     * @throws \DateMalformedStringException
     * @throws Throwable
     */
    public function create(array $data, int $creator_id): PushNotification
    {
        $data['created_by'] = $creator_id;
        $data['status'] = Status::Draft->value;
        $pushNotification = PushNotification::query()->create($data);

        if ($pushNotification->isActive() && $pushNotification->isSingle()) {

            /** @var OneSignalNotificationService $oneSignalNotificationService */
            $oneSignalNotificationService = app(OneSignalNotificationService::class);

            try {
                $oneSignalNotificationService->create($pushNotification);
            } catch (Throwable $e) {
                $pushNotification->delete();
                throw $e;
            }

            $pushNotification->update([
                'status' => Status::Wait,
            ]);
        }

        return $pushNotification;
    }


    /**
     * @param int $id
     * @param array $data
     * @return PushNotification
     * @throws InvalidPushNotificationArgumentException
     * @throws PushNotificationException
     * @throws \DateMalformedStringException
     * @throws Throwable
     */
    public function update(int $id, array $data): PushNotification
    {
        try {
            DB::beginTransaction();
            $pushNotification = $this->getById($id);
            if ($pushNotification->isActive() && $pushNotification->isSingle() && !$pushNotification->canBeCanceled()) {
                throw new PushNotificationException(
                    'Error while delete push notification: already added in queue on OneSignal or less than ' .
                    PushNotification::SKIP_SENDING_PUSH_NOTIFICATION_BEFORE_SECONDS .
                    ' seconds before sending'
                );
            }

            $pushNotification->update($data);

            if ($pushNotification->isSingle()) {

                /** @var OneSignalNotificationService $oneSignalNotificationService */
                $oneSignalNotificationService = app(OneSignalNotificationService::class);

                if ($data['is_active']) {
                    $oneSignalNotificationService->deleteAndCreate($pushNotification);
                    $pushNotification->update(['status' => Status::Wait]);
                } elseif ($pushNotification->isWaiting()) {
                    $oneSignalNotificationService->delete($pushNotification);
                    $pushNotification->update(['status' => Status::Draft]);
                }
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return $pushNotification;
    }

    /**
     * @param int $id
     * @return void
     * @throws PushNotificationException
     * @throws Throwable
     */
    public function delete(int $id): void
    {
        try {
            DB::beginTransaction();
            $pushNotification = $this->getById($id);

            if ($pushNotification->isWaiting()) {
                /** @var OneSignalNotificationService $oneSignalNotificationService */
                $oneSignalNotificationService = app(OneSignalNotificationService::class);

                if (!$oneSignalNotificationService->delete($pushNotification)) {
                    throw new PushNotificationException('Error while delete push notification');
                }
            }

            $pushNotification->update(['status' => Status::Draft, 'is_active' => false]);

            $pushNotification->delete();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @param int $id
     * @param int $creator_id
     * @return PushNotification
     * @throws InvalidPushNotificationArgumentException
     * @throws PushNotificationException
     * @throws Throwable
     * @throws \DateMalformedStringException
     */
    public function copy(int $id, int $creator_id): PushNotification
    {
        $notification = $this->getById($id);
        $data = $notification->toArray();
        $data['name'] .= ' (COPY)';
        $data['is_active'] = false;
        unset($data['id']);
        return $this->create($data, $creator_id);
    }

    /**
     * @return Collection
     */
    public function getActiveRegularPushesForCreate(): Collection
    {
        return PushNotification::query()
            ->where('is_active', true)
            ->where('is_delayed', false)
            ->where('deleted_at', null)
            ->where('time', '<=', Carbon::now('UTC')->format('H:i:s'))
            ->where(function ($query) {
                $query->where('last_onesignal_created_at', '<', Carbon::now('UTC')->startOfDay())
                    ->orWhereNull('last_onesignal_created_at');
            })
            ->where('type', Type::Regular->value)
            ->get();
    }
}
