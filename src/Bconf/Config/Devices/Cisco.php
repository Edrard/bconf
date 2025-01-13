<?php

return [
    "pre_command" => ["terminal length 0"],
    "config_export" => ["more system:running-config"],
    "after_command" => [""],
    "exec_type" => "write",
    "enablePTY" => False,
    "timeout" => 30,
    "command_end" => "\n",
    "config_filtets" => [],
    "telnet_user_prompt" => 'Username:',
    "telnet_pass_prompt" => 'Password:',
    "telnet_prompt_reg" => '[>#]',
    "telnet_command_end" => "",

];