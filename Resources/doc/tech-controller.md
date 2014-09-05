Controller
==========

There are a number of methods defined on the base controllers that are meant to be overridden.
The default implementations provide default values for their settings.

These methods are described in this file.

1. [`getFrontendManager`](#getfrontendmanager)
2. [`getRouteName`](#getroutename)
3. [`getSerializationGroups`](#getserializationgroups)
4. [`*Action`](#action)
5. [`handleView`](#handleview)

## `getFrontendManager`

This method MUST be defined when using the `RadRestController`, it probably should not be overridden when using the `ControllerServiceController`.

It will retrieve the frontend manager for this controller, and is documented in the [services documentation](4-services.md#loading-in-the-controller)

## `getRouteName`

This method does not really provide a default value.
It will look up the route names of the actions defined on this controller.

This method is called before each response that modifies a resource. That is, `postAction`, `putAction`, `patchAction` and `deleteAction`.
It is called by `redirectTo` and by the default templates to find out the right route to create a redirect response for.

By default the lookup is executed with a linear search over all routes defined for the application.
Depending on the size of your route collection this may result in a significant slowdown.

This performance can be improved by hard coding the routes in your controller, as demonstrated by this example.

```php
<?php
// Controller/UserController.php

namespace Acme\DemoBundle\Controller;

use vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController;

class UserController extends ControllerServiceController
{
    public function getRouteName($action) {
        switch($action) {
            case 'cget':
                return 'get_users';
            case 'get':
            case 'new':
            case 'post':
            case 'edit':
            case 'put':
            case 'remove':
            case 'delete':
                return $action.'_user';
            default:
                return parent::getRouteName($action);
        }
    }
    // ...
}
```

## `getSerializationGroups`

This method provides the values for the [serialization group feature of the JMS serializer](http://jmsyst.com/libs/serializer/master/cookbook/exclusion_strategies#creating-different-views-of-your-objects).

By default, or when the function returns a falsy value (`array()`, `null` or `false`), the default serialization group is set.

> You will only see lookups for `cget` and `get`.

This function is also called by the APIDoc generator to determine the serialization groups, so it MUST NOT rely on the authentication system.

```php
<?php
// Controller/UserController.php

namespace Acme\DemoBundle\Controller;

use vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController;

class UserController extends ControllerServiceController
{
    protected function getSerializationGroups($action) {
        switch($action) {
            case 'cget':
                return array('list', 'list_users');
            case 'get':
                return array();
        }
    }
    // ...
}
```

## `*Action`

All methods ending in `Action` correspond to a route in the application.

Each method may be overridden individually to modify the action.

APIDoc annotations for overridden methods are not automatically filled with inferred information, because it may be incorrect.
You have to add an `@ApiDoc` annotation with the desired information yourself.

## `handleView`

Handles the return value from each action.
The view that was generated is passed to this action, the return value is anything the kernel can handle `onKernelView`.
You may use this method to alter data stored in the view, e.g. add extra variables, or change a redirect target.

If you are using the [ViewResponseListener](https://github.com/FriendsOfSymfony/FOSRestBundle/blob/master/Resources/doc/3-listener-support.md), this method does not have to be overridden.

If you are not using the ViewResponseListener, you will want to use the `fos_rest.view_handler` service to convert the view to a response object.

[Index](index.md)
