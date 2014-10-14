Step 4: Registering controllers
===============================

1. [Class based controller](#class-based-controller)
2. [Controller as a service](#controller-as-a-service)

### Class based controller

Create the controller and override `getResourceManager()`, `getFormType()` to return the right resource manager and form type for the controller.

```php
<?php
// Controller/UserController.php

namespace Acme\DemoBundle\Controller;

use Acme\DemoBundle\Form\UserType;
use Acme\DemoBundle\Security\UserAuthorizationChecker;
use vierbergenlars\Bundle\RadRestBundle\Controller\RadRestController;
use vierbergenlars\Bundle\RadRestBundle\Manager\SecuredResourceManager;

class UserController extends RadRestController
{
    public function getResourceManager()
    {
        $entityRepository = $this->container->get('doctrine.orm.entity_manager')
            ->getRepository('AcmeDemoBundle:User');
        $authorizationChecker = new UserAuthorizationChecker(
            $this->container->get('security.context'),
            $this->container->get('security.authentication.trust_resolver'),
            $this->container->get('security.role_hierarchy')
        );
        return new SecuredResourceManager($entityRepository, $authorizationChecker);
    }

    public function getFormType()
    {
        return new UserType();
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

### Controller as a Service

You can also expose your controller as a service.
Create a class extending `ControllerServiceController` to make sure FOSRestBundle picks up the right route name & URLs.

```php
<?php
// Controller/UserController.php

namespace Acme\DemoBundle\Controller;

use vierbergenlars\Bundle\RadRestBundle\Controller\ControllerServiceController;

class UserController extends ControllerServiceController
{
    // ...
}
```

Next, register your service.

```xml
<?xml version="1.0" ?>
<container>
    <services>
        <service id="acme.demo.user.controller" class="Acme\DemoBundle\Controller\UserController">
            <!-- Assuming you are using the default constructor -->
            <argument type="service" id="acme.demo.user.resource_manager" />
            <argument type="service">
                <service class="Acme\DemoBundle\Form\UserType" />
            </argument>
            <argument type="service" id="form.factory" />
            <argument type="service" id="logger" />
            <argument type="service" id="router" />
            <argument>acme.demo.user.controller</argument>
        </service>
        <service id="acme.demo.user.resource_manager" class="vierbergenlars\Bundle\RadRestBundle\Manager\SecuredResourceManager">
            <argument type="service">
                <service class="vierbergenlars\Bundle\RadRestBundle\Doctrine\EntityRepository"
                    factory-service="doctrine.orm.entity_manager" factory-method="getRepository">
                    <argument>AcmeDemoBundle:User</argument>
                </service>
            </argument>
            <argument type="service" id="acme.demo.user.authorization_checker" />
        </service>
        <service id="acme.demo.user.authorization_checker" class="Acme\DemoBundle\Security\UserAuthorizationChecker">
            <argument type="service" id="security.context" />
            <argument type="service" id="security.authorization.trust_resolver" />
            <argument type="service" id="security.role_hierarchy" />
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

## Links

[Index](index.md)

1. [Setting Up](1-setting_up.md)
2. [Resource Manager](2-resource_manager.md)
3. [Authorization Checker](3-authorization_checker.md)
4. Controllers
5. **[Templates](5-templates.md)**
