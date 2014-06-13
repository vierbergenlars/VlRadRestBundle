Step 4: Registering services
============================

1. [Introduction](#introduction)
2. [Depencency injection tags](#dependency-injection-tags)
    1. [Tags summary](#tags-summary)
3. [Loading in the controller](#loading-in-the-controller)
    1. [Class based controller](#class-based-controller)
    2. [Controller as a service](#controller-as-a-service)

### Introduction

The Symfony framework provides a powerful dependency injection container.
You can register the classes you created with the container in your configuration, you probably already knew that.

But writing these service definitions is a boring and tedious job. Especially the definition for the frontend manager is basically always the same. By following some basic conventions, we can skip these boring parts.

### Dependency injection tags

All services can be assigned one or more tags in the service definition.
If you tag your resource manager, authorization checker and form with `radrest.resource_manager`, `radrest.authorization_checker` and `radrest.form`, the frontend manager will be automagically be created for you.
The tags also need a `resource` attribute that defines a unique resource name across the whole application. It will be used to match up the resource manager, authorization checker and form.

The tag `radrest.authorization_checker` will automatically construct the authorization checker with the default factory. To prevent this, add a factory class or factory service yourself, of add a `factory=false` attribute to the tag.

Additionally, the `radrest.entity_repository` tag will create an entity repository for the Doctrine entity.

The resource manager and the authorization checker are required, the form may be omitted (keep in mind POST, PUT and PATCH actions will be disabled if no form is registered).

Here is a simple example in XML format:

```xml
<!-- Resources/config/services.xml -->
<services>
    <service id="acme.demo.user.resource_manager" class="AcmeDemoBundle:User">
        <tag name="radrest.resource_manager" resource="user" />
        <tag name="radrest.entity_repository" />
    </service>

    <service id="acme.demo.user.authorization_checker" class="Acme\DemoBundle\Security\UserAuthorizationChecker">
        <tag name="radrest.authorization_checker" resource="user" />
    </service>

    <service id="acme.demo.user.form" class="Acme\DemoBundle\Form\UserType">
        <tag name="radrest.form" resource="user" />
    </service>
</services>
```

Or if you prefer YAML:

```yaml
# Resources/config/services.yml
---
services:
    acme.demo.user.resource_manager:
        class: AcmeDemoBundle:User
        tags:
            - { name: radrest.resource_manager, resource: user }
            - { name: radrest.entity_repository }
    acme.demo.user.authorization_checker:
        class: Acme\DemoBundle\Security\UserAuthorizationChecker
        tags:
            - { name: radrest.authorization_checker, resource: user }
    acme.demo.user.form:
        class: Acme\DemoBundle\Form\UserType
        tags:
            - { name: radrest.form, resource: user }
```

Please also note the naming of the services: `{bundle}.{resource}.(resource_manager|authorization_checker|form)`.

If this naming pattern is followed, the frontend manager will be registered as `{bundle}.{resource}.frontend_manager`.

The frontend manager will always be registered as `radrest.frontend_manager.compiled.{resource}`, and will be tagged with `radrest.frontend_manager` and a resource attribute the same as the services the frontend manager depends on.

#### Tags summary

| Tag name                        | Attributes                                                                        | Description                                                                                                                                                                                                                                  |
| ------------------------------- | --------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `radrest.resource_manager`      | `resource=<string>` (required)                                                    | Defines a resource manager for the resource set in the resource attribute. Tag is required to be present for each resource.                                                                                                                  |
| `radrest.authorization_checker` | `resource=<string>` (required), `factory=<true:false>` (optional, default=`true`) | Defines an authorization checker for the resource set in the resource attribute. A factory that injects the required services is automatically set for the service, unless `factory=false`. Tag is required to be present for each resource. |
| `radrest.form`                  | `resource=<string>` (required)                                                    | Defines a form for the resource set in the resource attribute. Tag is optional.                                                                                                                                                              |
| `radrest.frontend_manager`      | `resource=<string>` (required)                                                    | Defines a frontend manager for the resource set in the resource attribute. Service and tag will be automatically generated.                                                                                                                  |
| `radrest.controller`            | `resource=<string>` (required)                                                    | Defines a controller as a service, and automatically injects the frontend manager for that resource, the router and a logger.                                                                                                                |
| `radrest.entity_repository`     | `entity_manager=<service_id>` (optional, default=`doctrine.orm.entity_manager`)   | Sets up the service as an entity repository for the entity given in the class.                                                                                                                                                               |

### Loading in the controller

> **WARNING:** You should override `getRouteName()` in your controller to avoid a linear search over all defined routes in the application.
> [Read more](tech-controller.md#getroutename)

#### Class based controller

Create the controller and override `getFrontendManager()` to retrieve the right frontend manager from the container.

```php
<?php
// Controller/UserController.php

namespace Acme\DemoBundle\Controller;

use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestController;

class UserController extends RadRestController
{
    public function getFrontendManager()
    {
        return $this->get('acme.demo.user.frontend_manager');
    }

    // ...
}
```

And register it in the routing configuration:

```yaml
// Resources/routing.yml
---
user:
    resource: "@AcmeDemoBundle/Controller/UserController.php"
    type:     rest
```

#### Controller as a Service

You can also expose your controller as a service.
Create a class extending `ControllerServiceController` to make sure FOSRestBundle picks up the right route name & URLs.

```php
<?php
// Controller/NoteController.php

namespace Acme\DemoBundle\Controller;

use vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController;

class NoteController extends ControllerServiceController
{
    // ...
}
```

Next, register your service and tag it with `radrest.controller`.
When using the default constructor, all dependencies will be injected automatically.

If you have overridden the contructor, you must omit the arguments for the frontend manager, logger and router.
They will be injected in the right place automatically.

```xml
<?xml version="1.0" ?>
<container>
    <services>
        <service id="acme.demo.note.controller" class="Acme\DemoBundle\Controller\NoteController">
            <tag name="radrest.controller" resource="note" />
        </service>
    </services>
</container>
```

Finally register the service in the routing configuration.

```yaml
// Resources/routing.yml
---
note:
    resource: acme.demo.note.controller
    type:     rest
```

> **Note:** The frontend manager to inject is determined automatically based on the `radrest.frontend_manager` tag with the same resource attribute.
> If you wish to register the frontend manager yourself, you will be able to do so.
> No default frontend manager will be created if a `radrest.frontend_manager` tag with that resource attribute already exists.

## Links

[Index](index.md)

1. [Setting Up](1-setting_up.md)
2. [Resource Manager](2-resource_manager.md)
3. [Authorization Checker](3-authorization_checker.md)
4. Services
