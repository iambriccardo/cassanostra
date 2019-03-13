<?php

$config = parseConfigFileOrLoadDefaults();

function parseConfigFileOrLoadDefaults()
{
    $defaultConfig = [ "marketName" => "CassaNostra", "accentColor" => "#0266d8" ];

    $configFileName = 'config.ini';
    if(!is_file($configFileName))
        file_put_contents($configFileName, "");

    $parsedConfig = parse_ini_file($configFileName);
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

/**
 * Salva su file (mantenendo la sintassi INI) del contenuto attuale dell'array $config
 */
function writeConfigOnFile()
{
    global $config;
    $fileContent = "";

    foreach (array_keys($config) as $key)
        $fileContent .= "{$key} = {$config[$key]}\n";

    file_put_contents("config.ini", $fileContent);
}
