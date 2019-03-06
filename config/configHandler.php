<?php

$config = parseConfigFileOrLoadDefaults();

function parseConfigFileOrLoadDefaults()
{
    $defaultConfig = [ "marketName" => "CassaNostra", "accentColor" => "#0266d8" ];

    $parsedConfig = parse_ini_file("config.ini");
    if ($parsedConfig === false)
        return $defaultConfig;
    else
    {
        if (!isset($parsedConfig["marketName"]))
            $parsedConfig["marketName"] = $defaultConfig["marketName"];

        if (!isset($parsedConfig["accentColor"]))
            $parsedConfig["accentColor"] = $defaultConfig["accentColor"];

        return $parsedConfig;
    }
}

function writeConfigOnFile()
{
    global $config;
    $fileContent = "";

    foreach (array_keys($config) as $key)
        $fileContent .= "{$key} = {$config[$key]}\n";

    file_put_contents("config.ini", $fileContent);
}
