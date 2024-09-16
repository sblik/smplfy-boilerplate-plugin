=== Plugin Info ===
This is a boilerplate plugin for creating a custom plugin that utilises the SMPLFY Core Plugin

The word "boilerplate" is used in place of whatever name your plugin is. When ready, you can replace that word with the actual name of your plugin.

This can be done easily by pressing Ctrl + Shift + H to bring up the "Replace in files" window, searching for "boilerplate", clicking "Cc" to match case
and replacing it with your plugin name.
NOTE: Files such as "smplfy_bootstrap.php" and "BoilerPlateDependencyFactory.php" will need to be renamed manually.

=== Example Files ===
There is an example usecase, repository and entity included with this plugin which demonstrates how
code is executed in this framework

Once you are comfortably with creating your own usecase, repository and entity you can delete these files and their inclusion in the DependencyFactory
and GravityFormsAdapter.php:
    - ExampleUsecase.php
    - ExampleRepository.php
    - ExampleEntity.php

There is also an example type file provided, FormIds.php. This is used within the example files mentioned above, but it is recommended to keep this file as it likely
to be used in your developed solution to easily maintain Gravity Form IDs used in the plugin and make the Form IDs be human-readable.



