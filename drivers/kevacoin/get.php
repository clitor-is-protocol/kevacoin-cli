<?php

// Init helper
function _exec(
    string $processor,
    string $command
): mixed
{
    if (false !== exec(sprintf('%s %s', $processor, $command), $output))
    {
        $rows = [];

        foreach($output as $row)
        {
            $rows[] = $row;
        }

        if ($result = @json_decode(implode(PHP_EOL, $rows)))
        {
            return $result;
        }
    }

    return false;
}

// Get last namespace value
$kevaNS = _exec(
    $argv[1],
    sprintf(
        '%s %s "_KEVA_NS_"',
        'keva_filter',
        $argv[2]
    )
);

print_r($kevaNS);

$names = [];

foreach ($kevaNS as $ns)
{
    $names[$ns->height] = $ns->value;
}

krsort($names);

// Get namespace content
$parts = _exec(
    $argv[1],
    sprintf(
        '%s %s "\d+"',
        'keva_filter',
        $argv[2]
    )
);

print_r($parts);

// Merge content data
$data = [];
foreach ($parts as $part)
{
    $data[$part->key] = $part->value;
}

ksort($data);

// Save merged data to destination
$filename = isset($argv[3]) ? $argv[3] : sprintf(
    '%s/../../data/import/kevacoin.%s.%s',
    __DIR__,
    $argv[2],
    $names[array_key_first($names)]
);

file_put_contents(
    $filename,
    base64_decode(
        implode('', $data)
    )
);

echo sprintf(
    'saved to %s' . PHP_EOL,
    $filename
);