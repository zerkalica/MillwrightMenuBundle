Using menus with MillwrightMenuBundle
=====================================

**Basic Docs**

* [Features](#features)
* [Installation](#installation)
* [Creating menus](#creating-menus)
* [Using annotations](#annotations)
* [Using menu in templates](#context)
* [Menu item options](#options)

<a name="features"></a>
## Features

MillwrightMenuBundle extends base functionality of KnpMenuBundle and adds configuration, route, translation and security context support.
Main idea: 
    Each link on the site is a part of configured menu container, which supports translation, role and acl-based security, route params.

1. It uses `JMSSecurityExtraBundle` annotations for configuring menu items visibility:
   role-based and acl-based security context support

2. Menu options consist of two parts: 
    - `items` describes each menu item: labels, route|uri, translate, role
    - `tree` describes each menu container as hierarchy of menu items  

3. `items` can be configured from config file and annotations in controller class and actions

4. We can juggle any configured menu items in containers 

5. Menu twig helper supports route parameters, needed for changing menu items visibility on demand, based on acl 

6. Menu options merged from multiple sources: `tree` section of config, `item` section, `@Menu` annotations in action method, `@Menu` annotation in controller class  

<a name="installation"></a>
## Installation

### Step 1) Millwright menu bundle uses knp menu bundle, include it in `deps` file

Add the following lines to your  `deps` file and then run `php bin/vendors
install`:

```ini
[KnpMenu]
    git=https://github.com/KnpLabs/KnpMenu.git

[KnpMenuBundle]
    git=https://github.com/KnpLabs/KnpMenuBundle.git
    target=bundles/Knp/Bundle/MenuBundle

[MillwrightMenuBundle]
    git=git://github.com/zerkalica/MillwrightMenuBundle.git
```

### Step 2) Register the namespaces

Add the following two namespace entries to the `registerNamespaces` call
in your autoloader:

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'Knp\Bundle' => __DIR__.'/../vendor/bundles',
    'Knp\Menu'   => __DIR__.'/../vendor/KnpMenu/src',
    'Millwright' => __DIR__ . '/../vendor/bundles',
    // ...
));
```

### Step 3) Register the bundle

To start using the bundle, register it in your Kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Knp\Bundle\MenuBundle\KnpMenuBundle(),
        new Millwright\MenuBundle\MillwrightMenuBundle(),
    );
    // ...
)
```

### Step 4) Configure knp bundle

```yaml
# app/config/config.yml
imports:
    - { resource: menu.yml }
...
knp_menu:
    twig:  # use "twig: false" to disable the Twig extension and the TwigRenderer
        template: MillwrightMenuBundle:Default:knp_menu.html.twig
        
    templating: false # if true, enables the helper for PHP templates
    default_renderer: twig # The renderer to use, list is also available by default
```


<a name="creating-menus"></a>

## Creating menus

All menus configured in `millwright_menu` section of config.

```yaml
# app/config/menu.yml

millwright_menu:
    items: #menu items
        homepage: #menu name, used for route name, if it not set in options
            translateDomain: 'MillwrightMenuBundle'
            roles: IS_AUTHENTICATED_ANONYMOUSLY

        fos_user_registration_register:
            translateDomain: 'MillwrightMenuBundle'
            roles: ROLE_USER

        fos_user_profile_show:
            translateDomain: 'MillwrightMenuBundle'
            showNonAuthorized: true #show link in menu for non-authorized user
            roles: ROLE_USER

        fos_user_change_password:
            translateDomain: 'FOSUserBundle'
            roles: ROLE_USER
            label: 'change_password.submit'
        test:
            translateDomain: 'FOSUserBundle'
            label: 'change_password.submit'
            uri: 'http://www.google.com'

    tree: #menu containers
        user_admin: #user administration links container
            rendererOptions: #menu container render options
                template: '::actions.html.twig'
            children:        
                fos_user_profile_show: ~
                fos_user_change_password: ~

        main: #main container
            rendererOptions: #menu container render options
                template: MillwrightMenuBundle:Default:knp_menu.html.twig
            children:
                homepage: {uri: 'http://www.google.com'} # redefine uri
                test: ~
                fos_user_registration_register: ~
                fos_user_profile_show:
                    children:
                        fos_user_change_password: ~
```

<a name="annotations"></a>
## Using annotations

Items section can be configured from annotations:

``` php
# src/Application/Millwright/CoreBundle/Controller/DefaultController.php
...
/**
 * @Menu(translateDomain="MillwrightMenuBundle")
 */
class DefaultController extends Controller {
    /**
     * @Route("/user/{user}", name="showUser")
     * @Template()
     * @Secure(roles="ROLE_ADMIN")
     * @SecureParam(name="user", permissions="EDIT")
     * @Menu(showNonAuthorized=true, label="User edit")
     */
    public function userAction(User $user) {
        return array('content' => 'hello');
    }
}
```

<a name="context"></a>
## Using menu in templates

`millwright_menu_render` supports additional route parameters, the other options are equivalent to knp_menu_render. 

```jinja
{{ millwright_menu_render('main', routeParams, options, renderer) }}
```

### Simpe usage

```jinja
{{ millwright_menu_render('main') }}
```

### Advanced

We have a user collection, each user has acl permissions

```jinja
{% for user in usersCollection %}
    {{ millwright_menu_render('main', {user: user.id}) }}
{% endfor %}
```

<a name="options"></a>
## Menu item options

```yaml
# app/config/menu.yml

millwright_menu:
    items:
        <key>:
            <item options> 
    ...
    tree:
        rendererOptions:
            template:..
            
        children:
            <items hierarchy>        
```

- <key> - used as default value for name, route and label
- `uri` - uri string, if no route parameter set 
- `label` - label text or translation string template
- `name` - name of menu item, used as default for route
- `attributes` - knp menu item options
- `linkAttributes`- knp menu item options
- `childrenAttributes`- knp menu item options
- `labelAttributes`- knp menu item options
- `display`- knp menu item options
- `displayChildren`- knp menu item options
- `translateDomain` - translation domain
- `translateParameters` - translation parameters
- `secureParams` - copy of `@SecureParam` `JMSSecurityExtraBundle` annotations
- `roles`  - copy of `@Secure` `JMSSecurityExtraBundle` annotation
- `route` - route name for uri generation, if not set and uri not set - loads from key
- `routeAbsolute` - true for absolute url generation
- `showNonAuthorized` - show for non-authorized users
- `showAsText` - if authorized and no access to item, show item as text
- `rendererOptions` - options pass to menu renderer: template, etc

### Annotation options

`@Menu` annotation supports: label, translateDomain, translateParameters, name, showNonAuthorized, showAsText options.
