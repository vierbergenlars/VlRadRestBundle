Step 1: Setting up the bundle
=============================

### A) Instal VlRadRestBundle

**Note:**

> This bundle requires the [FOSRestBundle](https://github.com/FriendsOfSymfony/FOSRestBundle) to be set up first.
> The FOSRestBundle is installed as a dependency, but it should still be set up according to the [instructions](https://github.com/FriendsOfSymfony/FOSRestBundle/blob/master/Resources/doc/index.md).

Assumint you have installed composer, simply run:

```bash
composer require vierbergenlars/rad-rest-bundle ~0.1
```

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

        // Don't forget to also register the FOSRestBundle
        new FOS\RestBundle\FOSRestBundle(),
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
4. [Services](4-services.md)

