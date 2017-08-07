# Installation

`composer require jumpgate/admin:~1.0.0`

# Roles

`php artisan db:seed --class=AdminRoles`

# Expectations

1. That you have a BaseController class in `app/Http/Controllers`.
1. This may continue as the package evolves.

# Add the links to the MenuComposer (optional)

## Example

> This would be placed inside the `generateRightMenu()` method.

```
if (auth()->user()->isRole('admin')) {
    $rightMenu->dropdown('admin', 'Admin', function (DropDown $dropDown) {
        $dropDown->link('admin_user', function (Link $link) {
            $link->name = 'Users';
            $link->url  = route('admin.user.index');
        });
    });
}
```

# Create a controller for your resource

1. Extend `JumpGate\Admin\Http\Controllers\AdminBaseController`.
1. Assign the model to the controller.
1. Assign the transformer to the controller.
    - Create the transformer if it does not exist yet.
1. Define your `columns()` method.
    - This should return an array of the columns you want to display.
    - The key if the title for the header, the value is the property on the model.
1. Define your `filters()` method.
    - This should return an array of the filter-able  details.
    - The first index in the array should be the type.  Available types are text and select currently.
    - For text fields, the second index is the default value.
    - For select fields, the second index is the select array.
1. Overload the `routes()` method if needed.
    - By default, the admin controller thinks your routes will be `admin.<singular, lowercase version of the controller class name>.<route>`.
    - If this is not the case, define the `routes()` method and return an array of resource routes.
    
## Examples from users controller

```
public function routes()
{
    return [
        'index'  => 'admin.user.index',
        'show'   => 'admin.user.index',
        'create' => 'admin.user.index',
        'edit'   => 'admin.user.index',
        'delete' => 'admin.user.index',
    ];
}
    
public function columns()
{
    return [
        'Email'  => 'email',
        'Status' => 'status',
    ];
}

public function filters()
{
    return [
        'email'     => [
            'text',
            null,
        ],
        'name'      => [
            'text',
            null,
        ],
        'status_id' => [
            'select',
            Status::pluck('label', 'id')->prepend('All statuses', 0),
        ],
        'role'      => [
            'select',
            Role::pluck('name', 'id')->prepend('All roles', 0),
        ],
    ];
}
```

## Included methods

The admin controller tries to help with the most simple possible ideas for how your controller might work.  To this end 
we have included a few methods out of the box.

Something to note.  We do not supply the methods for `create` and `edit`.  We felt like these would be so unique to the models 
that they would be better handled manually each time.  Also, we do not supply views for anything other than index for the 
same reason.

### Index

The index method will look to your supplied model and search based on any request data sent and sort based on order options 
in the request.  It then transforms the data using the supplied transformer and returns it to the `admin.index` view.

### Show

The show method is very simple.  It takes the supplied ID, finds that model and returns it to the auto resolved view.

### Store

This will simply pass `create(request()->all())` to the supplied model.

### Update

This will find your model based on the ID then simply pass `update(request()->all())` to it.

### Destroy

This will find your model based on the ID then simply pass `delete()` to it.

## The index page

The admin package comes with a built in index page that should do most of what you need.  This view can be published if you want 
or if you want to just change the filters, you can extend this view and use the `admin_filters` section to override it.

```
@extends('admin.index')

@section('admin_filters')
    <!-- Your custom filter sidebar -->
@endsection
```

If you want to just add something to what is there by default, include `@parent` in your section.

```
@extends('admin.index')

@section('admin_filters')
    @parent
    
    <!-- Your custom filter sidebar -->
@endsection
```
