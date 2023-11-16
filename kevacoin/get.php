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

if ($clitoris->version !== '1.0')
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
$pieces = [];
foreach (
    _exec(
        $argv[1],
        sprintf(
            '%s %s "\d+"',
            'keva_filter',
            $argv[2]
        )
    ) as $piece)
{
    $pieces[$piece->key] = $piece->value;

    print_r(
        $piece
    );
}

ksort(
    $pieces
);

// Save merged data to destination
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