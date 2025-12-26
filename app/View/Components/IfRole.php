<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;

class IfRole extends Component
{
    /**
     * The required roles.
     *
     * @var array
     */
    protected $roles;

    /**
     * Create a new component instance.
     *
     * @param string|array $roles
     * @return void
     */
    public function __construct($roles)
    {
        $this->roles = is_array($roles) ? $roles : explode(',', $roles);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return function ($data) {
            if (!Auth::check()) {
                return '';
            }

            $user = Auth::user();
            
            if ($user->hasRole($this->roles)) {
                return $data['componentHtml'];
            }
            
            return '';
        };
    }
}
