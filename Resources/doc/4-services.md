Step 4: Registering services
============================

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
| `radrest.authorization_checker` | `resource=<string>` (required), `factory=<true|false>` (optional, default=`true`) | Defines an authorization checker for the resource set in the resource attribute. A factory that injects the required services is automatically set for the service, unless `factory=false`. Tag is required to be present for each resource. |
| `radrest.form`                  | `resource=<string>` (required)                                                    | Defines a form for the resource set in the resource attribute. Tag is optional.                                                                                                                                                              |
| `radrest.frontend_manager`      | `resource=<string>` (required)                                                    | Defines a frontend manager for the resource set in the resource attribute. Service and tag will be automatically generated.                                                                                                                  |
| `radrest.entity_repository`     | `entity_manager=<service_id>` (optional, default=`doctrine.orm.entity_manager`)   | Sets up the service as an entity repository for the entity given in the class.                                                                                                                                                               |

### Loading in the controller

Controllers should not be registered as services.
Since the container is not yet available when the controller is constructed, the frontend manager cannot be retrieved there.

The best way to make sure the frontend manager is always available is to override `setContainer()` on the controller, pass the call through to `parent::setContainer()` and then retrieve and set the frontend manager on the controller.

```php
<?php
// Controller/UserController.php

namespace Acme\DemoBundle\Controller;

use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestController;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserController extends RadRestController
{
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);
        $this->setFrontendManager($this->get('acme.demo.user.frontend_manager'));
    }
}
```

[Index](index.md)
