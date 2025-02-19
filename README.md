
![enter image description here](https://github.com/Edrard/bconf/blob/main/images/bconf_string_small.png)

## Preview

Bconf it is a simple device configuration backup software package. You can run it on any Linux system; you’ll just need PHP and a few additional packages. I started developing this product as an alternative to well-known projects, with the main goals being speed, compactness, and flexibility.

This project is still under development, and you're currently seeing only the basic version of its first component, which is responsible for configuration collection. However, if you have any ideas, feel free to reach out to me.
There's also a plan to develop a web interface for managing devices and their configurations.

Currently, only two device types are supported Cisco and Mikrotik and only via SSH, as I currently use devices from these manufacturers only. However, I hope to expand this list over time. If you need a specific device added, please submit a request. In the future, I plan to add support for Telnet and web basic auth.

Supported OS

Any Linux with php 7.4 +

You'll need the php-pecl-xdiff extension package

## Installation

The installation of _bconf_ is very straightforward—just follow the step-by-step instructions.

### Warning
I do not recommend installing _bconf_ in a publicly accessible web folder; at a minimum, restrict access by IP, as the device database is stored in plain text.

### Prerequisites

Setup a server with one of the supported OS's listed above. We recommend using a fresh install of the OS. If you are using a server that has been in production, we recommend you backup the server before proceeding.

You will need to install the following software on your server:
Git 2.25+
PHP 7.4+
Composer 2.4+

### Bconf Setup Steps

1. Login as root
2. Clone the repo

```sh
cd /path/to/install
git clone https://github.com/Edrard/bconf.git
```

3. Change directory to the repo

```sh
cd bconf
```

4. Run install.sh script

```sh
bash install.sh
```
### Warning
Do not remove .git folder, you will need it for future updates

## Updating

All you need to update your _bconf_ version is to run the following command, or add it to cron for automatic updates.
```sh
bash update.sh
```
## Configuration

```php
return array(
    'db' => [
        'type' => 'json',
        'path' => 'db.json'
    ],
    'save' => [
        'path' => 'Dumps',
    ],
    'disable' => [
        'dumping' => 0, #Disable Dumping for testing
        'saving' => 0 #Disable saving, instead show in cli
    ],
    'main' => [
        'retries' => 10,
        'retries_timeout' => 10,
    ],
    'logs' => [
        'file' => [
            'dst' => 'logs',
            'full' => 1, # keep info
            'debug' => 1, # More priority then full, adding debug to logs
            'per_run' => 0 # Create new log per script run
        ],
        'mail' => [
            'user' => '',
            'pass' => '',
            'smtp' => '',
            'port' => '25',
            'from' => '',
            'to' => '',
            'separate' => '1',
            'subject' => 'My Server'
        ]
    ]
);
```

The configuration should begin with the _config.php_ file. There are two parameters: _**db**_ and **_save_**. In the **_db_** section, you can configure the database type where the connection settings for your devices are stored; currently, only _json_ is available, along with the path to the _json_ file. The second parameter, **_save_**, allows you to specify the path where the dumps of your device configuration files should be saved.

The **_disable_** section includes the **_dumping_** parameter. If it is set to 1, the configuration collection system is disabled for testing purposes. By setting the **_saving_** parameter to 1, logs will be output to the console.

In the **_main_** section, you can specify the number of retry attempts **_retries_** as well as the interval between attempts in seconds **_retries_timeout_**.

The **_logs_** section allows us to configure log email notifications. The **_separate_** parameter indicates that logs should be sent separately. In the **_file_** settings, you can specify the folder where logs will be stored and set their level: **_full_** if you want to receive informational logs, or **_debug_**, which automatically includes full logs and debug logs. **_per_run_** creates a new log with each run

### Db

Now, let's take a look at the _db.json_ file (by the way, you can rename it to anything you like).
```json
{
    "test-router01":{
        "ip":"0.0.0.0",
        "port":22,
        "login":"admin",
        "password":"password",
        "group":"Client",
        "type":"router",
        "connect":"ssh",
        "model":"cisco",
        "config":{
            "enable":"1",
            "enable_command":"enable",
            "enable_pass":"enable_pass",
            "enable_pass_str":"Password:",
            "search":"ClientSW1#"
        }
    },
    "test-router02":{
        "ip":"0.0.0.0",
        "port":22,
        "login":"admin+cte",
        "password":"password",
        "group":"Vamark",
        "type":"router",
        "connect":"ssh",
        "model":"mikrotik",
        "config":{
            "enable":"0",
            "search":"[admin@vamarkgw01] >"
        }
    }
}
```
For each device, you need to create its own configuration, specifying access parameters such as `**ip**, **port**, **login**, and **password**.

The **group** parameter allows you to organize your devices into groups, enabling backup for a specific group separately.

The **type** parameter currently has no effect, but in the future, it will allow you to sort devices.

The **connect** parameter can be ssh o telnet for now

The **model** parameter indicates the device manufacturer, which affects additional parameters for processing and access (more details in the _Model_ section).

In the **config** section, you can specify whether the device should be switched to _enable mode_. If **enable** is set to 1, it will be required; if set to 0, it will not. If switching is needed, parameters like **enable_command** (the command to switch to this mode), **enable_pass** (the password to enter enable mode), and **enable_pass_str** (defines the prompt string for entering the password) are required.

Another crucial parameter is **search**, which is the shell prompt the system expects after completing the commands.

### Attention

For Mikrotik devices, add **+cte** to the username in the _login_ parameter, as shown in the example.

### Model
The model description files are located in the **src\Bconf\Config\Devices** folder. You can create your own files for other devices following the same format.

As an example, let's take a look at the Cisco model file.
```php
return [
    "pre_command" => ["terminal length 0"],
    "config_export" => ["more system:running-config"],
    "after_command" => [""],
    "exec_type" => "write",
    "enablePTY" => False,
    "timeout" => 30,
    "command_end" => "\n"
    "config_filtets" => []
    "telnet_user_prompt" => 'Username:',
    "telnet_pass_prompt" => 'Password:',
    "telnet_prompt_reg" => '[>#]',
    "telnet_command_end" => "",
];
```
Here, we have three arrays of parameters:

-   **pre_command**: The commands that will be executed before the export command is run.
-   **config_export**: The commands whose output will be saved in the dump file.
-   **after_command**: The commands that will be executed after the completion of the previous ones.

**exec_type** determines the method of executing the commands (for more details, refer to: [phpseclib commands](https://phpseclib.com/docs/commands)).

**enablePTY**: Indicates whether to enable PTY or not.

**timeout**: The timeout for executing the commands.

**command_end**: The command input character.

**config_filtets**: Leave empty, if you wish to apply all filters to dump

**telnet_user_prompt**: Telnet user prompt

**telnet_pass_prompt**: Telnet password prompt

**telnet_prompt_reg**: Telnet search shell prompt

**telnet_command_end**: The command input character for telnet.
## Tools

To test the availability of a specific device, you can use the _**device_test.php utility**_. Run it with the -n flag to specify the device name or -i to specify the IP address.

In the _**tools folder**_, you'll also find the _**convert.php**_ converter for transforming a JSON database into a MySQL database.

## Run

To start the collection process, you need to run the command:
 ```sh
php run.php
```
This will start the collection of configurations for all devices. However, if you want to collect configurations for a specific group, such as _Client_, you need to specify it as an additional parameter:

 ```sh
php run.php -g Client
```
You can specify multiple groups, separating them with a comma:
```sh
php run.php -g Client,Vamark
```
Additionally, you can specify a subfolder for logging by adding it after a space.
```sh
php run.php -g Client,Vamark -l Special_Folder
```

For automatic collection, simply create a cron job to run these commands.

You can also initiate parallel collection for all groups using _**gen_parallel.php**_. All you need is permission to use the **_exec_** function.

## License

This code base for this repository's code is distributed under GNUv3 License. See `LICENSE.txt` for more information.

## Support

You may open issues in the issue section here at github. I will try to address issues in a timely manner, but without guarantees.
