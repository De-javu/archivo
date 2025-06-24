<?php

namespace App\View\Components;

use Illuminate\View\Component;

class ArchivoLink extends Component
{
    public $href;

    /**
     * Create a new component instance.
     *
     * @param string $href
     */
    public function __construct($href)
    {
        $this->href = $href;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.archivo-link');
    }
}