<?php
$folderPath = './area-58Pack/assets/minecraft/optifine/optifine';

if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        return (string)$needle !== '' && strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
if (!function_exists('str_ends_with')) {
    function str_ends_with($haystack, $needle) {
        return $needle !== '' && substr($haystack, -strlen($needle)) === (string)$needle;
    }
}
if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

function getDirContents($dir, &$results = []) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path))
        {
            $results[] = $path;
        }
        else if ($value != "." && $value != "..")
        {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}

$count = 0;
$paths = getDirContents($folderPath);
$names = [];
foreach ($paths as $k => $path)
{
    if (!is_dir($path) && str_ends_with($path, '.properties'))
    {
        $e = explode('/',$path);
        $pathname = end($e);
        // read the file
        $file = file_get_contents($path);
        // replace the data
        $lines = explode("\n", $file);
        $file2 = '';
        foreach ($lines as $line)
        {
            if (str_starts_with('nbt.display.Name'))
            {
                $line2 = str_replace('nbt.display.Name=','',$line);
                if (str_starts_with('iregex:('))
                {
                    $line2 = str_replace('iregex:(','',$line2);
                    $line2 = str_replace(')','',$line2);
                }
                $line2 = explode('|', $line2);
                $line2 = $line2[0];
                $names[$pathname] = $line2;
                $line = 'nbt.nbrData.skinName=' . str_replace(' ','_',$line2);
            }
            $file2.=$line;
        }



        if (!$file2 && $file)
        {
            echo "erreur: $path\n";
        }

        // write the file
        if ($file2)
        {
            file_put_contents($path,$file2);
        }
    }
}

echo json_encode($names);

