Step 1: Setting up the bundle
=============================

### A) Install VlRadRestBundle

> **Note:**
> This bundle requires the [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle) to be set up first.
> The FOSRestBundle is installed as a dependency, but it should still be set up according to the [instructions](https://github.com/FriendsOfSymfony/FOSRestBundle/blob/master/Resources/doc/index.md).

Assuming you have installed composer, simply run:

```sh
composer require vierbergenlars/rad-rest-bundle @stable
```

> **Protip:** you should browse the [`vierbergenlars/rad-rest-bundle`](https://packagist.org/packages/vierbergenlars/rad-rest-bundle) page to choose a stable version to use, avoid the @stable meta constraint.
>
> The latest stable release is ![Latest Stable Version](https://poser.pugx.org/vierbergenlars/rad-rest-bundle/v/stable.svg)

### B) Enable the bundle in the kernel

Finally, enable the bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new vierbergenlars\Bundle\RadRestBundle\VlRadRestBundle(),

        // Don't forget to also register the FOSRestBundle and the JMSSerializerBundle
        new FOS\RestBundle\FOSRestBundle(),
        new JMS\SerializerBundle\JMSSerializerBundle(),
    );
}
```

## That's it!

Have a look at the docs for information on how to use this bundle.

## Links

[Index](index.md)

1. Setting Up
2. **[Resource Manager](2-resource_manager.md)**
3. [Authorization Checker](3-authorization_checker.md)
4. [Controllers](4-controllers.md)
5. [Templates](5-templates.md)
