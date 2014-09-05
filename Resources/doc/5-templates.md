Step 5: Creating templates
============================

1. [Introduction](#introduction)
2. [Template resolution](#template-resolution)
3. [Template variables](#template-variables)

### Introduction

Templates are used to create a user friendly document from the data that is retrieved from the controller.

You are not required to create templates, this bundle ships with very basic default templates.

### Template resolution

To determine which template will be loaded, we look at the controller class and the method.
`Acme\<Bundle>\Controller\<Controller>Controller::<Action>Action()`

1. The initial template name is `<Bundle>:<Controller>:<Action>.html.twig`.
   If this template exists, it will be rendered, else we look at the next step.

2. `<Action>` is set from `post`, `put` and `delete` to `new`, `edit` and `remove`
   If this template exists, it will be rendered, else we look at the final step.

3. The default templates in the namespace `VlRadRestBundle:Default:<Action>.html.twig` will be used.
   These can be overridden by placing templates with the same name in `app/Resources/VlRadRestBundle/views/Default`.

### Template variables

All data returned from the controller, except associative arrays, are in the `data` variable.
Associative arrays are maps of variable names to their values.

If a symfony form is returned from the controller, then `createView()` is called automatically on the form,
and its result stored in the `view` variable.

Additionally, extra data set on the view with `setExtraData()` is treated as a map of variable names to their values.

By default `controller` is set as an extra variable.

#### The `controller` variable

The `controller` object helps you with route generation and access control for the controller that handled the request.

 * `controller.route(action)` Gets the name of the route belonging to an action
 * `controller.may(action [, object])` Checks if the logged in user is allowed to use an action

## Links

[Index](index.md)

1. [Setting Up](1-setting_up.md)
2. [Resource Manager](2-resource_manager.md)
3. [Authorization Checker](3-authorization_checker.md)
4. [Services](4-services.md)
5. Templates
