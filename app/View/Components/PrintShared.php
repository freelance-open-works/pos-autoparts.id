<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\File;
use Illuminate\View\Component;

class PrintShared extends Component
{

    protected $css = '';
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // read hot in public return vite
        $manifests = File::json(public_path('build/manifest.json'));
        foreach ($manifests as $man) {
            if (strpos($man['file'], '.css') !== false) {
                $this->css = File::get(public_path('build/' . $man['file']));
            }
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.print.shared', [
            'css' => $this->css,
        ]);
    }
}
