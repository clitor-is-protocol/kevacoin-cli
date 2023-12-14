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

// Search namespace by md5file
echo 'Get namespaces list...' . PHP_EOL;

$namespaces = _exec(
    $argv[1],
    'keva_list_namespaces'
);

// Get _CLITOR_IS_ meta
foreach ((array) $namespaces as $namespace)
{
    echo sprintf(
        'Search for _CLITOR_IS_ match file MD5 %s ...' . PHP_EOL,
        $md5file
    );

    $meta = _exec(
        $argv[1],
        sprintf(
            "%s '%s' '%s'",
            'keva_get',
            $namespace->namespaceId,
            '_CLITOR_IS_'
        )
    );

    if ($value = @json_decode($meta->value))
    {
        if (
            isset($value->pieces) &&
            isset($value->pieces->total) &&
            isset($value->pieces->size) &&

            isset($value->file) &&
            isset($value->file->md5) &&
            $value->file->md5 === $md5file
        )
        {
            // Meta found
            echo sprintf(
                '_CLITOR_IS_ found for this file with namespace %s' . PHP_EOL,
                $namespace->namespaceId
            );

            // Split content to smaller parts using _CLITOR_IS_ size defined before
            $pieces = str_split(
                base64_encode(
                    file_get_contents(
                        $argv[2]
                    )
                ),
                $value->pieces->size
            );

            // Count total pieces
            $total = count(
                $pieces
            );

            // Validate pieces count
            if ($value->pieces->total !== $total)
            {
                echo '_CLITOR_IS_ have another pieces quantity' . PHP_EOL;

                exit;
            }

            // Begin pieces saving
            foreach ($pieces as $key => $value)
            {
                // Check piece stored is valid
                $piece = _exec(
                    $argv[1],
                    sprintf(
                        "%s '%s' '%s'",
                        'keva_get',
                        $namespace->namespaceId,
                        $key
                    )
                );

                // Piece value not found
                if (empty($piece->value))
                {
                    echo sprintf(
                        'Piece %s/%s value not found, creating...' . PHP_EOL,
                        $key + 1,
                        $total
                    );
                }

                // Piece value invalid, begin blockchain record
                else if ($piece->value !== $value)
                {
                    echo sprintf(
                        'Piece %s/%s value invalid (%s <> %s), rewriting...' . PHP_EOL,
                        $key + 1,
                        $total,
                        md5(
                            $piece->value
                        ),
                        md5(
                            $value
                        ),
                    );
                }

                // Piece valid
                else
                {
                    echo sprintf(
                        'Piece %s/%s - OK' . PHP_EOL,
                        $key + 1,
                        $total
                    );

                    continue;
                }

                print_r(
                    _exec(
                        $argv[1],
                        sprintf(
                            "%s '%s' '%s' '%s'",
                            'keva_put',
                            $namespace->namespaceId,
                            $key,
                            $value
                        )
                    )
                );

                // Apply delays to prevent too-long-mempool-chain reject
                $delay = isset($argv[3]) && $argv[3] > 0 ? (int) $argv[3] : 60;

                echo sprintf(
                    'Piece %s/%s sent, waiting %s seconds...' . PHP_EOL,
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
                $namespace->namespaceId
            );

            break;
        }
    }
}

echo 'Could not recover this file!' . PHP_EOL;