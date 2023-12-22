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

// Check file exits
if (!file_exists($argv[2]))
{
    exit('filename does not exist!' . PHP_EOL);
}

// Get file hash sum
$md5file = md5_file(
    $argv[2]
);

// Get file name
$name = basename(
    $argv[2]
);

// Check filename not longer of protocol
if (mb_strlen($name) > 255)
{
    $name = $md5file;
}

// Split content to smaller parts, according to the protocol limits
$size = isset($argv[3]) && $argv[3] <= 3072 ? (int) $argv[3] : 3072;

$pieces = str_split(
    base64_encode(
        file_get_contents(
            $argv[2]
        )
    ),
    $size
);

// Count total pieces
$total = count(
    $pieces
);

// Get software protocol details
$software = _exec(
    $argv[1],
    '-getinfo'
);

print_r($software);

// Create namespace to collect there data pieces
$ns = _exec(
    $argv[1],
    sprintf(
        "%s '%s'",
        'keva_namespace',
        $name
    )
);

print_r($ns);

// Create meta description for the future generations
print_r(
    _exec(
        $argv[1],
        sprintf(
            "%s %s '%s' '%s'",
            'keva_put',
            $ns->namespaceId,
            '_CLITOR_IS_',
            json_encode(
                [
                    'version' => '1.0.0',
                    'model' =>
                    [
                        'name' => 'kevacoin',
                        'software' =>
                        [
                            'version'  => $software->version,
                            'protocol' => $software->protocolversion
                        ]
                    ],
                    'pieces'  =>
                    [
                        'total' => $total,
                        'size'  => $size,
                    ],
                    'file' =>
                    [
                        'name' => basename(
                            $argv[2]
                        ),
                        'mime' => mime_content_type(
                            $argv[2]
                        ),
                        'size' => filesize(
                            $argv[2]
                        ),
                        'md5'  => $md5file
                    ]
                ]
            )
        )
    )
);

// Begin pieces saving
foreach ($pieces as $key => $value)
{
    print_r(
        _exec(
            $argv[1],
            sprintf(
                "%s '%s' '%s' '%s'",
                'keva_put',
                $ns->namespaceId,
                $key,
                $value
            )
        )
    );

    // Apply delays to prevent too-long-mempool-chain reject
    $delay = isset($argv[4]) && $argv[4] > 0 ? (int) $argv[4] : 60;

    echo sprintf(
        '%s/%s sent, waiting %s seconds...' . PHP_EOL,
        $key + 1,
        $total,
        $delay
    );

    sleep($delay);
}

// Print result
echo sprintf(
    'done! run to extract: php %s/get.php %s %s' . PHP_EOL,
    __DIR__,
    $argv[1],
    $ns->namespaceId
);