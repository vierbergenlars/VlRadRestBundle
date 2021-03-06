Controller traits
=================

For applications targetting php >=5.4.0, traits are an alternative to extending `RadRestController`.

They are the golden mean between inheriting undesired parts of the `RadRestController`
and writing your own controller from scratch.

Because traits enable you to mix and match features in your controllers without writing much code.

Traits are grouped per type of functionality they add.

1. [Routes](#routes)
2. [Pagination](#pagination)
3. [Routing](#routing)
4. [Redirect](#redirect)
5. [Serialization](#serialization)
6. [ViewHandler](#viewhandler)
7. [`DefaultsTrait`](#defaultstrait)

## Routes

These traits provide routes for your controller.
You can select the actions that can be taken on your resource.

There are 5 primary actions:
 * List
 * View
 * Create
 * Edit
 * Delete

Each action has a corresponding trait available in `vierbergenlars\Bundle\RadRestBundle\Controller\Traits\Routes\<Action>Trait`.
The routes these actions expose should be obvious.

All routes require an implementation of `getFrontendManager()` and [`handleView()`](#viewhandler).
The routes that modify a resource also require an implemenation of [`redirectTo()`](#redirect).
The routes that show a resource also require an implementation of [`getSerializationGroups()`](#serialization).
The `ListTrait` also requires an implementation of [`getPagination()`](#pagination).

> There is a sixth action, `Patch`, accessable only via the REST API that allows
> to only submit the modified fields of a resource.

## Pagination

The pagination traits enable support for different pagination libraries.
They provide the `getPagination()` method.

### Default pagination trait

The `DefaultPaginationTrait` just takes a slice of the results without support for rendering a pager.

### Knp pagination trait

The `KnpPaginationTrait` leverages the power of the [KnpPaginatorBundle](https://github.com/KnpLabs/KnpPaginatorBundle).
It requires an implementation of `getPaginator()`.

## Routing

The routing traits provide the default `getRouteName()` method.

### Default routing traits

> **WARNING:** This method may have bad performance (see [controller:getRouteName](tech-controller.md#getroutename))

There are two flavors of the routing trait, one for [class based controllers](4-services.md#class-based-controller) (`DefaultClassRoutingTrait`),
and one for [service based controllers](4-services.md#controller-as-a-service) (`DefaultServiceRoutingTrait`).

Both require implementations of `getLogger()` and `getRouter()`.
The `DefaultServiceRoutingTrait` also requires an implementation of `getServiceName()`.

These traits are guaranteed to word for all routes that exist on the controller.

### Class Resource routing trait

The `ClassResourceRoutingTrait` is faster than the default routing trait and has no dependency on the router.

It is based on the algorithm that generates the route names in FOSRestBundle, but may not always match the route
the FOSRestBundle generated because the implementation is simplified.

## Redirect

The redirect trait provides a default implementation of the `redirectTo()` method.

There is one implementation available `DefaultRedirectTrait`, which depends on [`getRouteName()`](#routing).

## Serialization

The serialization groups trait provides a default implementation of [`getSerializationGroups()`](tech-controller.md#getserializationgroups).

### Default serialization groups trait

The `DefaultSerializationGroupsTrait` will always select the default serialization group (`'Default'`).

### Class Resource serialization groups trait

The `ClassResourceSerializationGroupsTrait` will select serialization groups based on the action and controller that is used.

For the `cgetAction`, which shows a list of resources, the serialization groups 'list' and `<controller_name>.'_list'` are used.
For the `getAction`, which shows a detail of one resource, the serialization groups 'object' and `<controller_name>.'_object'` are used.

> Example: controller class `UserController`.
>  * cgetAction: 'list', 'user_list'
>  * getAction: 'object', 'user_object'

## ViewHandler

The view handler trait provides a default implementation of [`handleView()`](tech-controller.md#handleview).

There is one implementation available `DefaultViewHandlerTrait`, which does not modify the view.

## `DefaultsTrait`

The `DefaultsTrait` groups a bunch of default traits, so you do not have to import them all in your controller.

The `DefaultsTrait` includes:
 * `DefaultPaginationTrait`
 * `DefaultRedirectTrait`
 * `DefaultSerializationGroupsTrait`
 * `DefaultViewHandlerTrait`

> Tip: Replacing the `DefaultPaginationTrait` is very simple:
> ```php
> <?php
> // ...
> class MyController implements RadRestController, ClassResourceInterface
> {
>   use KnpPaginationTrait;
>   use DefaultsTrait {
>     KnpPaginationTrait::getPagination insteadof DefaultsTrait;
>   }
>   // ...
> }
> ```
> (see also [examples/TraitedController.php](examples/TraitedController.php))

[Index](index.md)
