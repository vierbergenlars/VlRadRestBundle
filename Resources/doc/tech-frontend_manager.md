The Frontend Manager
====================

1. [Introduction](#introduction)
2. [Integration in a custom controller](#integration-in-a-custom-controller)
3. [Methods](#methods)
    1. [`getList`](#getlist--array)
    2. [`getResource`](#getresourceid--object)
    3. [`createResource`](#createresourcerequest-request--null--objectform)
    4. [`editResource`](#editresourceid-request-request--null--objectform)
    5. [`deleteResource`](#deleteresourceid-request-request--null--nullform)

### Introduction

The Frontend Manager contains the glue between [the resource manager](./2-resource_manager.md), [the authorization checker](./3-authorization_checker.md) and Symfony forms, so you do not have to write it in your controllers.

The frontend manager is designed to be used only inside a controller, since it may throw  `Symfony\Component\Security\Core\Exception\AccessDeniedException` or `Symfony\Component\HttpKernel\Exception\NotFoundHttpException` when a user is not authorized to execute an action or when a resource does not exist.

All methods return either the requested resource or a form to execute the requested modification, depending on the request.

### Integration in a custom controller

The Frontend Manager can be used in the packaged controller, but if you wish to change the defaults, you can create your own controller class from scratch.

If you want to create a controller managed by the FOSRestBundle, have a look at [their documentation on controllers]().

> The rest of this document assumes your class implements `FOS\RestBundle\Routing\ClassResourceInterface`, a very useful tool when creating one controller per resource type.

1. Define the actions you want to expose, and use the matching FrontendManager method

  | Controller action                     | `FrontendManager` method               | Description                                      |
  | ------------------------------------- | -------------------------------------- | ------------------------------------------------ |
  | `cgetAction()`                        | `getList()`                            | Shows a list of all resources                    |
  | `getAction($id)`                      | `getResource($id)`                     | Shows the resource by id                         |
  | `newAction()`                         | `createResource()`                     | Shows a form to create a new resource            |
  | `postAction(Request $request)`        | `createResource($request)`             | Creates a new resource or shows form with errors |
  | `editAction($id)`                     | `editResource($id)`                    | Shows a form to edit a resource                  |
  | `putAction($id, Request $request)`    | `editResource($id, $request)`          | Modifies the resource or shows form with errors  |
  | `removeAction($id)`                   | `deleteResource($id)`                  | Shows a form to delete a resource                |
  | `deleteAction($id, Request $request)` | `deleteResource($id, $request)`        | Deletes the resource or shows form with errors   |

2. Handle return values from the Frontend Manager
   1. If the only possible outcome is showing resources or a form, pass the return value on to the template immediately.
      These actions will not accept user input from forms, and will never show form errors.
   2. The other actions do accept form input that may not be valid.
      - If the return value is an instance of `Symfony\Component\Form\Form`, the submitted form was not valid, you'll want to show the form template so the user can view and correct the errors. When designing a REST API, you'll also want to set an appropriate response code indicating the error (presumably 400 Bad Request).
      - Else the return value will be the object that was created or modified. It is up to you how you handle this case. Redirecting to the resource `getAction()` is common for webbrowsers, setting a status code of 200 OK or 201 Created is recommended for API clients.
        In the case of object deletion, you may want to redirect webbrowsers to the list of resources.

### Methods

#### `getList() → Array`

1. Throws `AccessDeniedException` when the user is not allowed to list. (`mayList() → false`)
2. Returns an array containing all resources from the resource manager. (`findAll() → Array`)

#### `getResource($id) → $object`

1. Returns the resource matching the `$id` from the resource manager (`find($id) → $object`)
2. Throws `NotFoundHttpException` when the resource does not exist. (`$object === null`)
3. Throws `AccessDeniedException` when the user is not allowed to view this resource (`mayView($object) → false`)

#### `createResource(Request $request = null) → $object|Form`

1. Throws `AccessDeniedException` when the user is not allowed to create a resource. (`mayCreate() → false`)
2. If the request is not `null`, and the form is valid: save the created resource, and return it `$object`.
3. Else: return the `Form`

#### `editResource($id, Request $request = null) → $object|Form`

1. Fetches the resource with `getResource($id)`, so it will throw `NotFoundHttpException` and `AccessDeniedException` when appropriate.
2. Throws `AccessDeniedException` when the user is not allowed to edit this resource (`mayEdit($object) → false`)
3. If the request is not `null`, and the form is valid: save the modified resource, and return `$object`.
4. Else: return the `Form`

#### `deleteResource($id, Request $request = null) → null|Form`

1. Fetches the resource with `getResource($id)`, so it will throw `NotFoundHttpException` and `AccessDeniedException` when appropriate.
2. Throws `AccessDeniedException` when the user is not allowed to delete this resource (`mayDelete($object) → false`)
3. If the request is not `null`, and the form is valid: delete the resource, and return `null`.
4. Else: return the `Form`

[Index](index.md)
