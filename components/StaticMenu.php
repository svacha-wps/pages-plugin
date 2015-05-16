<?php namespace RainLab\Pages\Components;

use Cms\Classes\ComponentBase;
use RainLab\Pages\Classes\Router;
use Cms\Classes\Theme;
use Request;
use RainLab\Pages\Classes\Menu as PagesMenu;

/**
 * The menu component.
 *
 * @package rainlab\pages
 * @author Alexey Bobkov, Samuel Georges
 */
class StaticMenu extends ComponentBase
{
    /**
     * @var array A list of items generated by the menu.
     * Each item is an object of the RainLab\Pages\Classes\MenuItemReference class.
     */
    protected $menuItems;

    public function componentDetails()
    {
        return [
            'name'        => 'Static menu',
            'description' => 'Outputs a menu in a CMS layout.'
        ];
    }

    public function defineProperties()
    {
        return [
            'code' => [
                'title'          => 'Menu',
                'description'    => 'Specify a code of the menu the component should output',
                'type'           => 'dropdown'
            ]
        ];
    }

    public function getCodeOptions()
    {
        $result = [];

        $theme = Theme::getEditTheme();
        $menus = PagesMenu::listInTheme($theme, true);

        foreach ($menus as $menu) {
            $result[$menu->code] = $menu->name;
        }

        return $result;
    }

    public function onRun()
    {
        $this->page['menuItems'] = $this->menuItems();
    }

    public function menuItems()
    {
        if ($this->menuItems !== null)
            return $this->menuItems;

        if (!strlen($this->property('code')))
            return;

        $theme = Theme::getActiveTheme();
        $menu = PagesMenu::loadCached($theme, $this->property('code'));

        if ($menu) {
            $this->menuItems = $menu->generateReferences($this->page);
        }

        return $this->menuItems;
    }

    public function resetMenu($code)
    {
        $this->setProperty('code', $code);
        $this->menuItems = null;

        return $this->page['menuItems'] = $this->menuItems();
    }
}