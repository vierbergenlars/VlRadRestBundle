Step 3: The authorization checker
=================================

### Introduction

When creating a web application, you need some form of access control.
Usually, this is done in the controller code, scattering around authentication checks everywhere.
This results in duplicated code for create, modification and delete actions, or the addition of a function that does not belong in a controller.
Other people prefer to put authentication checks in their models, with `canBeEditedBy($user)` alike methods. But authentication does not really belong in the model either.

The solution is to put authentication logic in a separate class, whose only responsibility is access control.
This bundle provides a standard interface for authorization checkers, as well as an abstract class that contains convenience methods.

### Custom authorization checker

To write a custom authorization checker, a method needs to be implemented for each action that can be taken on a resource.
The class must implement the [`AuthorizationCheckerInterface`](../../Security/AuthorizationCheckerInterface.php)

 * `mayList()`: The user is allowed to retrieve a list of all resources of a type
 * `mayView($object)`: The user is allowed to view the details of the resource `$object`
 * `mayCreate()`: The user is allowed to create a new resource of this type
 * `mayEdit($object)`: The user is allowed to modify the details of the resource `$object`
 * `mayDelete($object)`: The user is allowed to delete the resource `$object`
 
If you choose to extend [`AbstractAuthorizationChecker`](../../Security/AbstractAuthorizationChecker.php), you can make use of a couple of convenience methods, documented below.

### Convenience methods provided by `AbstractAuthorizationChecker`

* `getUser()`: returns the user that is currently logged in, `null` if not logged in.
* `hasRole($role)`: checks if the currently logged in user has a role. Always returns `false` if the user is not logged in.
* `getRoles()`: return an array of all roles the user has. If the user is not logged in, an empty array is returned.
* `getToken()`: returns the authorization token for the current session.

Less frequently used methods are `getSecurityContext()`, `getTrustResolver()` and `getRoleHierarchy()`.
