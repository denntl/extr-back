<?php

namespace App\Services\Client\OnesignalTemplate\DTOs\PushRequest;

use App\Enums\PushTemplate\Event;
use App\Services\Common\OneSignal\DTO\Interfaces\ValidatableDTO;
use App\Services\Common\OneSignal\Exceptions\InvalidPushNotificationArgumentException;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SendSingleRequestDTO implements ValidatableDTO
{
    public string $bigPicture;
    public string $smallIcon;

    /**
     * @param string $url
     * @param string $title
     * @param string $contents
     * @param string $bigPicture
     * @param string $smallIcon
     * @param array|null $segments
     * @param array|null $events
     * @param array|null $geos
     * @param string|null $sendAfter
     * @param array|null $filters
     * @throws InvalidPushNotificationArgumentException
     */
    public function __construct(
        public string $url,
        public string $title,
        public string $contents,
        string $bigPicture,
        string $smallIcon,
        public ?array $segments = null,
        public ?array $events = null,
        public ?array $geos = null,
        public ?string $sendAfter = null,
        public ?array $filters = [],
    ) {
        $this->bigPicture = url(Storage::url($bigPicture));
        $this->smallIcon = url(Storage::url($smallIcon));
        $this->validate();
    }

    /**
     * @return void
     * @throws InvalidPushNotificationArgumentException
     */
    public function validate(): void
    {
        $validator = Validator::make([
            'bigPicture' => $this->bigPicture,
            'smallIcon'  => $this->smallIcon,
            'url'        => $this->url,
            'title'      => $this->title,
            'contents'   => $this->contents,
            'events'     => $this->events,
            'segments'   => $this->segments,
            'geos'       => $this->geos,
            'sendAfter'  => $this->sendAfter,
            'filters'    => $this->filters,
        ], [
            'bigPicture' => 'required|string|url',
            'smallIcon' => 'required|string|url',
            'url' => 'required|string',
            'title' => 'required|string',
            'contents' => 'required|string',
            'events' => ['array', 'in:' . implode(',', Event::toArray())],
            'segments' => ['array', 'in:' . implode(',', Event::toArray())],
            'geos' => 'array',
            'geos.*' => 'string',
            'sendAfter' => 'date|nullable',
            'filters' => 'array',
        ]);

        if ($validator->fails()) {
            throw new InvalidPushNotificationArgumentException($validator->errors()->toJson());
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        if (!empty($this->events)) {
            foreach ($this->events as $eventKey => $event) {
                if ($eventKey > 0) {
                    $this->filters[] = ['operator' => 'OR'];
                }
                $eventFilter = ['field' => 'tag'];
                switch ($event) {
                    case Event::INSTALL->value:
                        $eventFilter['key'] = 'status';
                        $eventFilter['relation'] = '=';
                        $eventFilter['value'] = Event::INSTALL->value;
                        break;
                    case Event::REGISTRATION->value:
                        $eventFilter['key'] = 'status';
                        $eventFilter['relation'] = '=';
                        $eventFilter['value'] = Event::REGISTRATION->value;
                        break;
                    case Event::DEPOSIT->value:
                        $eventFilter['key'] = 'status';
                        $eventFilter['relation'] = '=';
                        $eventFilter['value'] = Event::DEPOSIT->value;
                        break;
                }
                $this->filters[] = $eventFilter;
            }
        }

        if ($this->geos) {
            foreach ($this->geos as $key => $geo) {
                if ($key > 0) {
                    $this->filters[] = ['operator' => 'OR'];
                }
                $countryFilter = ['field' => 'country', 'relation' => '=', 'value' => $geo];
                $this->filters[] = $countryFilter;
            }
        }

        return array_filter([
            'big_picture' => $this->bigPicture,
            'chrome_web_image' => $this->bigPicture,
            'chrome_big_picture' => $this->bigPicture,
            'small_icon' => $this->smallIcon,
            'huawei_small_icon' => $this->smallIcon,
            'chrome_web_icon' => $this->smallIcon,
            'firefox_icon' => $this->smallIcon,
            'url' => $this->url,
            'headings' => [
                'en' => $this->title,
            ],
            'contents' => [
                'en' => $this->contents,
            ],
            'filters' => $this->filters,
            'send_after' => Carbon::parse($this->sendAfter)->format('Y-m-d H:i:s TP'),

        ], fn($value) => !empty($value));
    }
}
