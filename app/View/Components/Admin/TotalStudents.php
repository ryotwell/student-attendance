<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TotalStudents extends Component
{
    public int $totalStudents = 0;
    public int $totalClasses = 0;

    /**
     * Create a new component instance.
     */
    public function __construct(int $totalStudents, int $totalClasses)
    {
        $this->totalStudents = $totalStudents;
        $this->totalClasses = $totalClasses;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.admin.total-students');
    }
}
