<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            ['holiday_date' => '2026-01-01', 'name' => 'New Year\'s Day'],
            ['holiday_date' => '2026-01-12', 'name' => 'Zanzibar Revolution Day'],
            ['holiday_date' => '2026-03-20', 'name' => 'Eid al-Fitr'],
            ['holiday_date' => '2026-04-07', 'name' => 'Karume Day'],
            ['holiday_date' => '2026-04-26', 'name' => 'Union Day'],
            ['holiday_date' => '2026-05-01', 'name' => 'International Workers\' Day'],
            ['holiday_date' => '2026-05-27', 'name' => 'Eid al-Adha'],
            ['holiday_date' => '2026-07-07', 'name' => 'Saba Saba'],
            ['holiday_date' => '2026-08-04', 'name' => 'Nane Nane'],
            ['holiday_date' => '2026-08-25', 'name' => 'Maulid Day'],
            ['holiday_date' => '2026-10-14', 'name' => 'Nyerere Day'],
            ['holiday_date' => '2026-12-09', 'name' => 'Independence Day'],
            ['holiday_date' => '2026-12-25', 'name' => 'Christmas Day'],
            ['holiday_date' => '2026-12-26', 'name' => 'Boxing Day'],
        ];

        foreach ($holidays as $holiday) {
            Holiday::updateOrCreate(['holiday_date' => $holiday['holiday_date']], $holiday);
        }
    }
}