# clitor

CLI for large objects in small blocks

## drivers

 * [x] [kevacoin](https://github.com/kevacoin-project/kevacoin)

### put

export FS object to blockchain

```
php kevacoin/put.php processor filename [length] [delay]
```

* `processor` - path to `kevacoin-cli`
* `filename`  - local file path to store
* `length`    - optional split size, `3072` bytes [max](https://kevacoin.org/faq.html)
* `delay`     - optional seconds of parts sending delay to prevent `too-long-mempool-chain` reject, default `60`

### get

import from blockchain to FS location

```
php kevacoin/get.php processor namespace [destination]
```

* `processor`   - path to `kevacoin-cli`
* `namespace`   - hash received from the `put` command
* `destination` - optional FS location, `data/import` by default