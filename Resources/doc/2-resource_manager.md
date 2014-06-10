Step 2: The resource manager
============================

### Introduction

Using resource managers for each type of resource makes it possible to write storage agnostic Controllers.
It places a layer between the controller and the storage backend. (Doctrine, Propel, files, ...)

VlRadRestBundle ships with one resource manager, which is to be used with the Doctrine ORM.
But it is very easy to write your own custom resource manager, just implement the methods from `ResourceManagerInterface`.

### Doctrine ORM resource manager

The Doctrine ORM resource manager enables you to skip writing boring resource manager implementations for all your entities.
It can be used as an entity repository on any entity, but also as a parent class for more complex entity repositories.

```php
<?php
// User.php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="vierbergenlars\Bundle\RadRestBundle\Doctrine\EntityRepository")
 */
class User
{
    // ...
}
```

When used as a parent class for a specialised entity repository, the class can be used as a drop-in replacement for the Doctrine ORM EntityRepository.
The entity repository from this bundle extends the one provided by Doctrine, and adds a couple of methods to it.

```php
<?php
// User.php

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class User
{
    // ...
}

// UserRepository.php

//use Doctrine\ORM\EntityRepository;
use vierbergenlars\Bundle\RadRestBundle\Doctrine\EntityRepository;

class UserRepository extends EntityRepository
{
   // ...
}
```

### Custom resource manager

An example of a custom resource manager can be found in [examples/FileResourceManager.php](./examples/FileResourceManager.php)

The resource manager has 5 methods:

 * `findAll()`: returns an array with all resources of that type
 * `find($id)`: finds and returns a resource by its primary identifier, or null when there is no resource with that identifier. (The identifier does not have to be numeric, it can be anything as long as it is unique for all resources of that type)
 * `create()`: creates a new, empty object of that type. There are no parameters, modifying the object will happen after it has been created. This does only create a PHP object, it should not save the resource to permanent storage.
 * `update($object)`: Saves the object to permanent storage. Only objects of the type the resource manager is responsible for may be accepted. Passing any other type should result in a LogicException. If the resource cannot be updated, an exception must be thrown.
 * `delete($object)`: Removes the object from permanent storage.  Only objects of the type the resource manager is responsible for may be accepted. Passing any other type should result in a LogicException. If the resource cannot be deleted, an exception must be thrown.

If used together with the default controller, the resource object requires a `getId()` method, that will always return the primary identifier of the resource.

[Next chapter: The Authorization Checker](3-authorization_checker.md)
[Index](index.md)
