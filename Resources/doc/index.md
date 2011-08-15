Using menus with MillwrightMenuBundle
=========================

MillwrightMenuBundle extends base functionality of KnpMenuBundle and adds configuration, route, translation and security context support.

Each menu item has setters/getters, and can be configured from array:

Menu item options:
    <key>  - by default used as name, role and label
    domain - translation domain for label translation, if not set, no label translation will be set
    translateParams - translation params
    label  - label text or translation string template
    route  - route name for uri generation, if no uri setted manually
    routeParams - route params
    absolute    - true, for absolute url generation from route
    uri    - if set, route parameter will be ignored
    name   - name of menu item
    role   - access role for menu item
    submenu - sub menu

Root menu options:
    domain - default translation domain for child items
    role   - default role for child items
    submenu - menu items


Creating a menu from configuration
----------------------------------

config.yml:
imports:
    - { resource: menu.yml }

knp_menu:
    twig: true


All options defined in parent menu item used as defaults in submenu items.


menu.yml:
millwright_menu:
    main: #menu id
        domain: 'MillwrightMenuBundle' # use this domain for label translation in all child items by default
        role: ROLE_USER # use this role in all child items by default
        submenu:
            homepage: {role: IS_AUTHENTICATED_ANONYMOUSLY} # redefine default role
            sonata_admin_dashboard: {role: ROLE_SONATA_ADMIN}
            fos_user_registration_register: ~ #name, route and label loaded by this key
            fos_user_profile_show:
                submenu:
                    fos_user_change_password:
                        label: 'change_password.submit' # define custom label for change password link
                        domain: 'FOSUserBundle'

Creating a menu from service
----------------------------

$menuService = $this->get('millwright_menu.factory');

use Millwright\MenuBundle\MenuItem;
$menu = $menuService->create('main');
$menu->addChild('fos_user_registration_register'); // create item with name, label and route = fos_user_registration_register

$child = new MenuItem('fos_user_change_password');
$child->setRole(ROLE_USER); // item allowed only for ROLE_USER
$menu->addChild($child);
