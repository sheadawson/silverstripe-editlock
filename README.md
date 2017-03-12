# SilverStripe Edit Lock

## Requirements

* SilverStripe ~3.1

## Maintainers

* shea@silverstripe.com.au

## Description

Prevents a CMS user from editing a record that another CMS user is currently editing, to avoid change conflicts/data loss.  

![Screenshot](https://raw.github.com/sheadawson/silverstripe-editlock/master/images/screenshot.png) 

## Installation

Download this module into the root of your project. The module folder must be named "editlock". Run dev/build.

```
Composer: require "sheadawson/silverstripe-editlock": "1.0.x"
```

## Usage

The module will take effect on any DataObject's standard CMS edit form out of the box. A locked object will display a readonly version of the edit form and a message explaining why it's locked and who is editing it. Editing will be re-enabled a maximum of 15 seconds after the original editor has navigated away from the edit form.

If you need to restrict locking to only specific data classes, you can specify these in your yml config as below.

```
EditLockControllerExtension:
  lockedClasses:
    - 'BlogPost'
    - 'Article'
 ```
 
## Edit anyway override

Users with the "edit anyway" permission are given the option to override the lock and edit the record anyway, after being made aware of the risks. This permission can be applied to user groups in the Security section of the CMS.
