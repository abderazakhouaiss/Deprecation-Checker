# Deprecation Checker

This module is a simple development tool that allows you to check for deprecated code in your custom and contrib modules. It will scan your modules and show you a list of all deprecated code that it finds, along with the module name and file path.

Please note that this module is for development use only and should not be used in a production environment. Also, the server must allow the execution of shell_exec for the module to work as expected.

## Requirements
- Drupal 9
- This module uses the function shell_exec in its logic, therefore it should be activated on the server.

## Installation
- Copy the module to the `modules` directory of your Drupal installation and enable it in the `Extend` section of your Drupal admin interface.
- Visit the `Deprecation Checker` page in your Drupal admin interface to see the list of deprecated code.

## Usage
- Go to the `Deprecation Checker` (admin/development/deprecated-code) page in your Drupal admin interface to see the list of deprecated code.
- Use the module filter to filter the list by module name.

## Feedback
Please let me know if you have any issues or suggestions for improvements.

