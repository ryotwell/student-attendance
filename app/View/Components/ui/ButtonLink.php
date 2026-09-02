<?php

namespace App\View\Components\ui;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ButtonLink extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $href = '#',
        public string $size = 'md',
        public string $variant = 'primary',
        public ?string $startIcon = null,
        public ?string $endIcon = null,
        public string $className = '',
        public bool $disabled = false,
        public ?string $target = null,
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.ui.button-link');
    }
}