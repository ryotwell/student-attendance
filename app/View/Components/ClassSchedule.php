<?php

namespace App\View\Components;

use App\Models\Schedule;
use App\Models\Xclass;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ClassSchedule extends Component
{
    public ?Xclass $class;
    public array $schedulesByDay;

    /**
     * Create a new component instance.
     */
    public function __construct(?int $classId = null, ?Xclass $classWithSchedules)
    {
        if($classWithSchedules) {
            $this->class = $classWithSchedules;
            $this->schedulesByDay = $this->groupSchedulesByDay();
        } else {
            $this->class = $classId ? Xclass::with('schedules.subject')->find($classId) : null;
            $this->schedulesByDay = $this->groupSchedulesByDay();
        }
    }

    private function groupSchedulesByDay(): array
    {
        if (!$this->class) {
            return [];
        }

        $days = ['MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY'];
        $grouped = [];

        foreach ($days as $day) {
            $grouped[$day] = $this->class->schedules
                ->where('day', $day)
                ->sortBy('start_time')
                ->values()
                ->all();
        }

        return $grouped;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.class-schedule');
    }
}
