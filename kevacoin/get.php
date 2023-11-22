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

// Get clitoris
$clitoris = _exec(
    $argv[1],
    sprintf(
        '%s %s "_CLITOR_IS_"',
        'keva_get',
        $argv[2]
    )
);

print_r(
    $clitoris
);

if (empty($clitoris->value))
{
   exit(
       sprintf(
           '%s does not contain _CLITOR_IS_' . PHP_EOL,
           $argv[2]
       )
   );
}

if (!$clitoris = @json_decode(
     $clitoris->value
))
{
    exit(
        sprintf(
            'could not decode _CLITOR_IS_ of %s' . PHP_EOL,
            $argv[2]
        )
    );
}

if ($clitoris->version !== '1.0.0')
{
    exit(
        sprintf(
            '_CLITOR_IS_ of %s not compatible!' . PHP_EOL,
            $argv[2]
        )
    );
}

if (empty($clitoris->file->name))
{
    exit(
        sprintf(
            '_CLITOR_IS_ format issue for %s!' . PHP_EOL,
            $argv[2]
        )
    );
}

// Merge content data
$chain = [];
foreach (
    (array)
    _exec(
        $argv[1],
        sprintf(
            '%s %s "\d+"',
            'keva_filter',
            $argv[2]
        )
    ) as $piece)
{
    if (
        !isset($piece->key)   ||
        !isset($piece->value) ||
        !isset($piece->height)
    )
    {
        exit(
            'please wait for all pieces sending complete!'
        );
    }

    // Keep all key versions in memory
    $chain[$piece->key][$piece->height] = $piece->value;

    print_r(
        $piece
    );
}

// Select last piece value by it max block height
//
// piece could have many of versions (with same key)
// this feature related to data reading correction after recovery #1

$pieces = [];

foreach ($chain as $key => $height)
{

    ksort(
        $height
    );

    $pieces[$key] = $height[array_key_last($height)];
}

ksort(
    $pieces
);

// Save file to destination
$filename = isset($argv[3]) ? $argv[3] : sprintf(
    '%s/../data/import/[kevacoin][%s]%s',
    __DIR__,
    $argv[2],
    $clitoris->file->name
);

file_put_contents(
    $filename,
    base64_decode(
        implode('', $pieces)
    )
);

echo sprintf(
    'saved to %s' . PHP_EOL,
    $filename
);