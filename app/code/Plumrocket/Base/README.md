Base Plumrocket extension for M2

### Abstract Classes:

* `Model\OptionSource\AbstractSource` - for creating system config sources
* `Setup\AbstractUninstall` - for creating uninstall scripts;

### Utils

* `DeviceDetectInterface` - provides isMobile and isTabled methods
* `ConfigUtilsInterface` - all what you need to read configs

### Frontend

#### Themes

We add classes to `<body>` and layout handles depend on a current theme.

It simplifies adding support of different themes, add separate styles or event separate layouts for each theme. 


**Themes support**

First, extension adding two layouts handles of current theme, eg:
1. `pl_thm_smartwave_default`
2. `pl_thm_smartwave_porto_default`

Second, two classes to body:
1. `pl-thm-smartwave`
2. `pl-thm-smartwave-porto`

#### Catalog

`ViewModel\Catalog\CurrentProductRetriever` - alternative of using deprecated registry

### System config

#### Layout handles

Extension adding two handles to each plumrocket configuration.
1. `pr_system_config_edit`
2. `pr_system_config_edit + section_id` e.g - pr_system_config_edit_pr_cookie
