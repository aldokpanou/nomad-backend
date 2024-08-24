<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\User;
use App\Models\CoworkingSpaces;
use App\Models\Reservations;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run()
    {
        $user1 = User::where('email', 'john.doe@example.com')->first();
        $user2 = User::where('email', 'jane.smith@example.com')->first();

        $coworkingSpace1 = CoworkingSpaces::where('name', 'Coworking Space 1')->first();
        $coworkingSpace2 = CoworkingSpaces::where('name', 'Coworking Space 2')->first();
        $coworkingSpace3 = CoworkingSpaces::where('name', 'Coworking Space 3')->first();

        $this->createReservation($coworkingSpace1, $user1, 2); // 2 hours
        $this->createReservation($coworkingSpace2, $user2, 3); // 3 hours
        $this->createReservation($coworkingSpace3, $user1, 4); // 4 hours
    }

    private function createReservation($coworkingSpace, $user, $durationInHours)
    {
        $startTime = Carbon::now()->addDays(rand(1, 5));
        $endTime = $startTime->copy()->addHours($durationInHours);

        Reservations::create([
            'coworking_space_id' => $coworkingSpace->id,
            'user_id' => $user->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'total_price' => $coworkingSpace->price_per_hour * $durationInHours,
        ]);
    }
}
