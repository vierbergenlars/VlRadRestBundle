Step 4: The Frontend Manager
============================

### Introduction

The Frontend Manager contains the glue between [the resource manager](./2-resource_manager.md), [the authorization checker](./3-authorization_checker.md) and Symfony forms, so you do not have to write it in your controllers.

The frontend manager is designed to be used only inside a controller, since it may throw  `Symfony\Component\Security\Core\Exception\AccessDeniedException` or `Symfony\Component\HttpKernel\Exception\NotFoundHttpException` when a user is not authorized to execute an action or when a resource does not exist.

All methods return either the requested resource or a form to execute the requested modification, depending on the request.

### `getList() → Array`

1. Throws `AccessDeniedException` when the user is not allowed to list. (`mayList() → false`)
2. Returns an array containing all resources from the resource manager. (`findAll() → Array`)

### `getResource($id) → $object`

1. Returns the resource matching the `$id` from the resource manager (`find($id) → $object`)
2. Throws `NotFoundHttpException` when the resource does not exist. (`$object === null`)
3. Throws `AccessDeniedException` when the user is not allowed to view this resource (`mayView($object) → false`)

### `createResource(Request $request) → $object|Form`

1. Throws `AccessDeniedException` when the user is not allowed to create a resource. (`mayCreate() → false`)
2. Returns the freshly created resource `$object` when the form has been submitted, the form data is valid, and the resource has been created.
3. Returns the `Form` when the form has not yet been submitted, or the submitted data is not valid.

### `editResource($id, Request $request) → $object|Form`

1. Fetches the resource with `getResource($id)`, so it will throw `NotFoundHttpException` and `AccessDeniedException` when appropriate.
2. Throws `AccessDeniedException` when the user is not allowed to edit this resource (`mayEdit($object) → false`)
3. Returns the modified `$object` when the form has been submitted, the form data is valid and the resource has been modified.
4. Returns the `Form` when the form has not yet been submitted, or the submitted data is not valid.

### `deleteResource($id, Request $request) → null|Form`

1. Fetches the resource with `getResource($id)`, so it will throw `NotFoundHttpException` and `AccessDeniedException` when appropriate.
2. Throws `AccessDeniedException` when the user is not allowed to delete this resource (`mayDelete($object) → false`)
3. Returns `null` when the form has been submitted, the form data is valid and the resource has been deleted.
4. Returns the `Form` when the form has not yet been submitted, or the submitted data is not valid.
