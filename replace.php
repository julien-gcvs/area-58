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
        $items = '';
        foreach ($lines as $line)
        {
            if (str_starts_with($line, 'matchItems'))
            {
                $items = str_replace('matchItems=','',$line);
            }
            if (str_starts_with($line, 'nbt.display.Name'))
            {
                $line2 = str_replace('nbt.display.Name=','',$line);
                if (str_starts_with($line2, 'iregex:('))
                {
                    $line2 = str_replace('iregex:(','',$line2);
                    $line2 = str_replace(')','',$line2);
                }
                $line2 = explode('|', $line2);
                $line2 = $line2[0];
                $names[$pathname] = [
                    str_replace(' ','_',$line2),
                    explode(' ',$items)
                ];
                $line = 'nbt.nbrData.skinName=' . str_replace(' ','_',$line2);
            }
            $file2.=$line.PHP_EOL;
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

