<?php

namespace App\Http\ViewComposers;

use App\Models\DashboardSection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class DashboardMenuComposer
{
    /**
     * Menu structure containing only authorized links for the current user.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $authorizedMenu;


    /**
     * Create a new Dashboard Menu composer.
     *
     * @return void
     */
    public function __construct()
    {
        $this->authorizedMenu = $this->filterByPermissions($this->fetchMenu());
    }


    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $baseUri = preg_replace('/^admin\/?/', '', request()->route()->uri);
        $cleanUri = preg_replace('/\/[\w-]+\/edit(ar)?$/', '', $baseUri);

        // Define if the user requested a specific URI for an active link to match,
        // otherwise, use the current URI.
        $uri = $view->getData()['dbActiveLink'] ?? $cleanUri;

        $menu = $this->setActivePath($this->authorizedMenu, $uri);

        $view->with('dbMenu', $menu);
    }


    /**
     * Fetch the menu structure from database.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function fetchMenu()
    {
        return DashboardSection::with(['submenus' => function($query) {
            $query->orderBy('order')
                ->with(['links' => function($q) {
                    $q->with('permission')->orderBy('order');
                }]);
        }])
        ->orderBy('order')
        ->get();
    }


    /**
     * Remove links that the user is not authorized to see.
     *
     * @param  \Illuminate\Support\Collection $menu
     * @return \Illuminate\Support\Collection
     */
    protected function filterByPermissions($menu)
    {
        
        $filteredMenu = $menu->map(function($section) {
            // Remove unauthorized links
            $section->submenus->map(function($submenu) {
                $authLinks = $submenu->links->filter(function($link) {
                   
                    if ($link->permission) {
                        return Gate::allows($link->permission->key_name);
                    }

                    return Auth::user()->isSuperAdmin();
                });

                $submenu->setRelation('links', $authLinks);

                return $authLinks->count() ? $submenu : collect([]);
            });

            // Remove submenus without links
            $section->setRelation('submenus',
                $section->submenus->filter(function($submenu) {
                    return $submenu->links->count();
                })
            );

            return $section;
        });

        // Remove sections without submenus
        return $filteredMenu->filter(function($section) {
            return $section->submenus->count();
        });
    }


    /**
     * Set active section, submenu and link if current URI is found.
     * If not, set first section as active.
     *
     * @param \Illuminate\Support\Collection $menu
     * @param string $uri - The URI to be matched by the active link.
     */
    protected function setActivePath($menu, $uri)
    {
        $activeFound = false;

        $menu->map(function($section) use (&$activeFound, $uri) {
            $section->active = false;

            $section->submenus->map(function($submenu) use (&$activeFound, $section, $uri) {
                if ($match = $submenu->links->firstWhere('route', $uri)) {
                    $activeFound = true;
                    $match->active = true;
                    $submenu->active = true;
                    $section->active = true;
                }

                return $submenu;
            });

            return $section;
        });

        if (! $activeFound && $menu->count()) {
            $menu->first()->active = true;
        }

        return $menu;
    }
}
