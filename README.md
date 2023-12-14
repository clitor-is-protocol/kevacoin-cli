# kevacoin-cli

CLI tools for KevaCoin blockchain

### put

export FS object to blockchain

```
php cli/put.php processor filename [length] [delay]
```

* `processor` - path to `kevacoin-cli`
* `filename`  - local file path to store
* `length`    - optional split size, `3072` bytes [max](https://kevacoin.org/faq.html)
* `delay`     - optional seconds of parts sending delay to prevent `too-long-mempool-chain` reject, default `60`

### fix

check and fix FS object in blockchain

```
php cli/put.php processor filename [delay]
```

* `processor` - path to `kevacoin-cli`
* `filename`  - local file path to store
* `delay`     - optional seconds of parts sending delay to prevent `too-long-mempool-chain` reject, default `60`

### get

import from blockchain to FS location

```
php cli/get.php processor namespace [destination]
```

* `processor`   - path to `kevacoin-cli`
* `namespace`   - hash received from the `put` command
* `destination` - optional FS location, `data/import` by default