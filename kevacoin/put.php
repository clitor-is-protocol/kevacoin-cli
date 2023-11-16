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

// Create namespace
$kevaNamespace = _exec(
    $argv[1],
    sprintf(
        '%s %s',
        'keva_namespace',
        basename($argv[2])
    )
);

print_r($kevaNamespace);

// Insert content parts
if (!empty($kevaNamespace->namespaceId))
{
    $parts = str_split(
        base64_encode(
            file_get_contents($argv[2])
        ),
        isset($argv[3]) && $argv[3] <= 3072 ? (int) $argv[3] : 3072 // 3072 bytes limit
    );

    foreach ($parts as $key => $value)
    {
        $kevaPut = _exec(
            $argv[1],
            sprintf(
                '%s %s %s %s',
                'keva_put',
                $kevaNamespace->namespaceId,
                $key,
                $value
            )
        );

        print_r($kevaPut);
    }
}