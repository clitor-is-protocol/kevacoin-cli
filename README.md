# clitor

CLI util to operate with large objects in small blocks size

## drivers

 * [x] [kevacoin](https://github.com/kevacoin-project/kevacoin)

### put

export FS object to blockchain namespace

```
php kevacoin/put.php processor filename [length]
```

* `processor` - path to `kevacoin-cli`
* `filename`  - file path to store in blockchain
* `length`    - optional split size, 3072 bytes [max](https://kevacoin.org/faq.html)

### get

import namespace to FS location

```
php kevacoin/get.php processor namespace [destination]
```

* `processor`   - path to `kevacoin-cli`
* `namespace`   - hash received from the `put` command
* `destination` - optional file system location, `data/import` by default