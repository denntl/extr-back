<?php

namespace App\Services\Common\ApplicationStatistic;

use App\Enums\PwaEvents\Event;
use App\Models\ApplicationStatistic;
use App\Models\PwaClient;
use App\Models\PwaClientClick;
use App\Models\PwaClientEvent;
use Carbon\Carbon;

readonly class UpdateStatisticService
{
    private ApplicationStatistic $applicationStatistic;
    private Carbon $date;

    public function __construct(private int $applicationId, string $date)
    {
        $this->date = Carbon::parse($date);
        $applicationStatistic = ApplicationStatistic::query()
            ->where('application_id', $applicationId)
            ->where('date', $this->date->toDateString())
            ->first();

        if (!$applicationStatistic) {
            $applicationStatistic = ApplicationStatistic::query()->create([
                'application_id' => $applicationId,
                'date' => $this->date->toDateString(),
            ]);
        }

        $this->applicationStatistic = $applicationStatistic;
    }

    public function setEvent(PwaClientEvent $pwaClientEvent): self
    {
        $event = Event::mapEvent($pwaClientEvent->event);

        $newCount = PwaClientEvent::getCountByDate(
            $this->applicationId,
            $event,
            $this->date,
            in_array($event->value, [Event::Registration->value, Event::Deposit->value]),
        );

        $field = match ($event) {
            Event::Install => 'installs',
            Event::Subscribe => 'push_subscriptions',
            Event::Registration => 'registrations',
            Event::Deposit => 'deposits',
            Event::Open => 'opens',
            default => null,
        };

        if ($field) {
            $this->applicationStatistic->{$field} = $newCount;
            switch ($event->value) {
                case Event::Install->value:
                    $this->applicationStatistic->first_installs =
                        PwaClientEvent::getCountByDate($this->applicationId, $event, $this->date, true);
                    break;
                case Event::Open->value:
                    $this->applicationStatistic->first_opens =
                        PwaClientEvent::getCountByDate($this->applicationId, $event, $this->date, true);
                    break;
                default:
                    break;
            }
        }

        return $this;
    }

    public function setClick(): self
    {
        $this->applicationStatistic->clicks = PwaClientClick::getCountByDate($this->applicationId, $this->date);
        $this->applicationStatistic->unique_clicks = PwaClient::getCountOfUniqueByDate($this->applicationId, $this->date);

        return $this;
    }

    public function save(): bool
    {
        $this->applicationStatistic->ins_to_uc = $this->applicationStatistic->unique_clicks
            ? $this->applicationStatistic->installs / $this->applicationStatistic->unique_clicks
            : 0;
        $this->applicationStatistic->reg_to_ins = $this->applicationStatistic->installs
         ? $this->applicationStatistic->registrations / $this->applicationStatistic->installs
            : 0;
        $this->applicationStatistic->dep_to_ins = $this->applicationStatistic->installs
         ? $this->applicationStatistic->deposits / $this->applicationStatistic->installs
            : 0;
        $this->applicationStatistic->dep_to_reg = $this->applicationStatistic->registrations
         ? $this->applicationStatistic->deposits / $this->applicationStatistic->registrations
            : 0;
        $this->applicationStatistic->repeated_installs = $this->applicationStatistic->installs
            - $this->applicationStatistic->first_installs;
        $this->applicationStatistic->repeated_opens = $this->applicationStatistic->opens
            - $this->applicationStatistic->first_opens;

        return $this->applicationStatistic->save();
    }
}
