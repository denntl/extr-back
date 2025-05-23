<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Создаем партиционированную таблицу
        DB::statement("
            CREATE TABLE onesignal_events (
                id SERIAL NOT NULL,
                event_id SMALLINT NOT NULL,
                notification_id UUID NOT NULL,
                created_at TIMESTAMP(0) DEFAULT CURRENT_TIMESTAMP NOT NULL,
                PRIMARY KEY (id, created_at)
            ) PARTITION BY RANGE (created_at)
        ");

        // Добавляем внешний ключ
        DB::statement("
            ALTER TABLE onesignal_events
            ADD CONSTRAINT fk_notification
            FOREIGN KEY (notification_id)
            REFERENCES onesignal_notifications(onesignal_notification_id)
            ON DELETE CASCADE
        ");

        // Создаем партиции
        DB::statement("
            CREATE TABLE onesignal_events_2024w52
            PARTITION OF onesignal_events
            FOR VALUES FROM ('2024-12-23') TO ('2024-12-30')
        ");

        DB::statement("
            CREATE TABLE onesignal_events_2024w53
            PARTITION OF onesignal_events
            FOR VALUES FROM ('2024-12-30') TO ('2025-01-06')
        ");

        // Генерация партиций для 2025 года
        for ($week = 1; $week <= 52; $week++) {
            $start_date = (new DateTime("2025-01-06 +".(($week - 1) * 7)." days"))->format('Y-m-d');
            $end_date = (new DateTime("2025-01-06 +".($week * 7)." days"))->format('Y-m-d');
            DB::statement("
                CREATE TABLE onesignal_events_2025w{$week}
                PARTITION OF onesignal_events
                FOR VALUES FROM ('{$start_date}') TO ('{$end_date}')
            ");
        }
    }

    public function down()
    {
        // Удаление партиций
        DB::statement("DROP TABLE IF EXISTS onesignal_events_2024w52");
        DB::statement("DROP TABLE IF EXISTS onesignal_events_2024w53");

        for ($week = 1; $week <= 52; $week++) {
            DB::statement("DROP TABLE IF EXISTS onesignal_events_2025w{$week}");
        }

        // Удаление основной таблицы
        DB::statement("DROP TABLE IF EXISTS onesignal_events");
    }
};
