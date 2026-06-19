<?php

namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

use App\Models\Ticket;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ticket>
 */

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    protected $departments = ['ICT', 'HR', 'Admission', 'Finance', 'Registrar', 'VC', 'Pro-VC'];

    protected $by = [1, 2, 3, 4, 5, 6, 7, 8];

    protected $status = ['Open', 'In Progress', 'Hold'];

    public function definition()
    {
        return [
            'ticket_id' => 'TK-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT),
            'title' => $this->faker->sentence,
            'ticket_by' => $this->faker->randomElement($this->by),
            'ticket_to' => $this->faker->randomElement($this->departments),
            'ticket_from' => $this->faker->randomElement($this->departments),
            'assigned_to' => null,
            'description' => $this->faker->paragraph,
            'priority' => $this->faker->randomElement(['Low', 'Medium', 'High']),
            'status' => $this->faker->randomElement($this->status),
            'due_date' => $this->faker->date,
        ];
    }
}

