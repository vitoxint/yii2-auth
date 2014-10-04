yii2-auth
========

Auth is a module for the [Yii PHP framework](http://www.yiiframework.com) that provides a web user interface for Yii's built-in authorization manager (\yii\rbac\BaseManager).
You can read more about Yii's authorization manager in the framework documentation under [Authorization](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html#role-based-access-control-rbac).

Auth based on original code of [yii-auth](https://github.com/crisu83/yii-auth) extension and fully rewrited for using with Yii 2.
Also fork contain all original releases for Yii 1.x.

**At this moment module supports only DbManager.**

### Demo

Coming soon.

## Usage

### Setup

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Add

```
"binn/yii2-auth": "*"
```

to the require section of your `composer.json` file.

Add module to application config and configure `authManager` component:

```php
return [
    'components' => [
        'authManager' => [
            'class' => 'auth\components\DbManager', // or 'auth\components\PhpManager'
        ],
        // ...
    ],
    'modules' => [
        'auth' => [
            'class' => 'auth\Module',
        ],
    ],
];
```

Please note that while the module doesn't require you to use a database, if you wish to use ***yii\rbac\DbManager*** you need it's schema (it can be found in the framework under `yii\rbac\migrations`).

### Configuration

Configure the module to suit your needs. Here's a list of the available configurations (with default values).

```php
'auth' => array(
  'userClass' => Yii::$app->user->identityClass, // the name of the user model class.
  'userIdColumn' => 'id', // the name of the user id column.
  'userNameColumn' => 'name', // the name of the user name column.
  'applicationControllers' => [], // the path to controllers files that will be using for generates permissions.
  'admin' => [], // users with full access to module.
  'accessFilterBehavior' => [], Configuration for custom access filter.
),
```

### Checking access

When you wish to check if the current user has a certain permission you can use the ***User::can()*** method which can be access from anywhere in your application through ***Yii::$app*** like so:

```php
if (Yii::$app->user->can('itemName')) // itemName = name of the operation
{
  // access is allowed.
}
```

In order to keep your permissions dynamic you should never check for a specific role or task, instead you should always check for an operation. 
For more information on Yii's authorization manager refer to the framework documentation on [Authorization](http://www.yiiframework.com/doc-2.0/guide-security-authorization.html#role-based-access-control-rbac).

#### Checking access using a filter

You can also use a filter to automatically check access before controller actions are called.
Operations used with this filter has to be named as follows ***(moduleId.)controllerId.actionId***, where ***moduleId*** is optional. 

For example

```php
public function behaviors()
{
    return [
        'access' => [
            'class' => AccessControl::className(),
                'rules' => [
        		    [
        			    'allow' => true,
        			    'actions' => ['error', 'login', 'logout'],
                    ],
                    [
                        'allow' => true,
                        'roles' => [$this->getRuleName($this->action->id)],
                    ],
                    [
                        'allow' => true,
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest ? !empty(Yii::$app->user->identity->isAdmin) : false;
                    },
                ],
            ],
        ],
    ];
}
```

For more information on how filters work refer to the framework documentation on [Controllers](http://www.yiiframework.com/doc-2.0/guide-structure-filters.html).

## Versioning

Because Auth contain all versions from original library be careful with versions.

Version 1.x - for Yii 1.x
Version 2.x - for Yii 2.x

## Contributing

Please, send any issues and PR only for 2.x version. For original Yii 1.x module contribute to [yii-auth](https://github.com/crisu83/yii-auth)
