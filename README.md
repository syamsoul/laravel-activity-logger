# Activity Logger for Laravel



[![Latest Version on Packagist](https://img.shields.io/packagist/v/syamsoul/laravel-activity-logger.svg?style=flat-square)](https://packagist.org/packages/syamsoul/laravel-activity-logger)


## Documentation, Installation and Usage Instructions

See the [documentation](https://info.souldoit.com/projects/laravel-activity-logger) for detailed installation and usage instructions.


&nbsp;
&nbsp;
## Introduction

This package can help you to log your application activity easily and neatly. This package is actually specially built for logging activity in Command. But you can also use this package in route's callback / Controller, Middleware or anywhere inside your application.


&nbsp;
&nbsp;
## Requirement

* Laravel 8.x (and above)

This package can be used in Laravel 8.x or higher. If you are using an older version of Laravel, there's might be some problem. If there's any problem, you can [create new issue](https://github.com/syamsoul/laravel-activity-logger/issues) and I will try to fix it as soon as possible.


&nbsp;
&nbsp;
## What You Can Do


``` php
<?php

namespace App\Console\Commands\Order;

use Illuminate\Console\Command;
use SoulDoit\ActivityLogger\Logger;
use DB;

use App\Models\Order;
use App\Models\User;

class AllocateBonusCommand extends Command
{
    protected $signature = 'order:allocate-bonus';

    protected $description = 'Allocate bonus to upline user for each order.';

    public function handle(Logger $logger) // You can inject the dependency into this `handle` method
    {
        $logger->setTrack("ALLOCATE_BONUS_FOR_EACH_ORDER", true, "COMMAND");

        $orders = Order::select('id', 'user_id', 'amount')
        ->where([
            'status' => 1,
            'is_bonus_paid' => 0,
        ])->get();

        foreach($orders as $each_order){
            $upline_user_id = $each_order->user->upline_user_id;

            $bonus_amount = (5/100) * $each_order->amount;

            User::where('id', $upline_user_id)->update([
                'wallet_balance' => DB::raw("`wallet_balance` + $bonus_amount"),
            ]);

            $logger->log("Allocate $bonus_amount to User ID: $upline_user_id");
        }

        return Command::SUCCESS;
    }
}
```

&nbsp;

The output will be:

``` bash
[2022-12-28 16:11:33] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] START COMMAND  
[2022-12-28 16:11:33] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 30 to User ID: 7
[2022-12-28 16:11:33] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 30 to User ID: 8
[2022-12-28 16:11:33] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 20 to User ID: 7
[2022-12-28 16:11:33] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 30 to User ID: 8
[2022-12-28 16:11:33] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 20 to User ID: 7
[2022-12-28 16:11:33] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 30 to User ID: 8
[2022-12-28 16:11:33] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 20 to User ID: 7
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 30 to User ID: 8
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 20 to User ID: 7
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 30 to User ID: 8
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 20 to User ID: 7
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 30 to User ID: 1963
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 30 to User ID: 122
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 300 to User ID: 741
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 200 to User ID: 8
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 300 to User ID: 8
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 200 to User ID: 7
[2022-12-28 16:11:34] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] -- Allocate 30 to User ID: 7
[2022-12-28 16:11:49] production.INFO: [ALLOCATE_BONUS_FOR_EACH_ORDER][REF: 521645] STOP COMMAND (Total Time: 15.5869 secs)
```

&nbsp;
&nbsp;
## Support me

If you find this package helps you, kindly support me by donating some BNB (BSC) to the address below.

```
0x364d8eA5E7a4ce97e89f7b2cb7198d6d5DFe0aCe
```

<img src="https://info.souldoit.com/img/wallet-address-bnb-bsc.png" width="150">


&nbsp;
&nbsp;
## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
