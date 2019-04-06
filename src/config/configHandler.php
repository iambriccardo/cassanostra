<?php

$configFileName = __DIR__ . '/config.ini';
$config = parseConfigFileOrLoadDefaults($configFileName);

function parseConfigFileOrLoadDefaults($configFileName)
{
    $defaultConfig = [ "marketName" => "CassaNostra", "accentColor" => "0266d8" ];

    if(!is_file($configFileName))
        file_put_contents($configFileName, "");

    $parsedConfig = parse_ini_file($configFileName);
    if ($parsedConfig === false)
        return $defaultConfig;
    else
    {
        foreach (array_keys($defaultConfig) as $key)
        {
            if (!isset($parsedConfig[$key]))
                $parsedConfig[$key] = $defaultConfig[$key];
        }
        return $parsedConfig;
    }
}

/**
 * Salva su file (mantenendo la sintassi INI) del contenuto attuale dell'array $config
 */
function writeConfigOnFile()
{
    global $config, $configFileName;
    $fileContent = "";

    foreach (array_keys($config) as $key)
        $fileContent .= "{$key} = {$config[$key]}\n";

    file_put_contents($configFileName, $fileContent);
}
