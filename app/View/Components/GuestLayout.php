<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    public string $logoClass;
    public string $logoWrapperClass;

    public function __construct(string $logoClass = 'w-20 h-20 fill-current text-gray-500', string $logoWrapperClass = '')
    {
        $this->logoClass = $logoClass;
        $this->logoWrapperClass = $logoWrapperClass;
    }

    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
