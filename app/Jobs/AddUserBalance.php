<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AddUserBalance implements ShouldQueue,ShouldBeUnique
{
    use Queueable;

    public $user;
    public $amount;

    /**
     * Create a new job instance.
     */
    public function __construct($user, $amount)
    {
        $this->user = $user;
        $this->amount = $amount;
    }

    /**
     * 任务唯一ID
     */
    public function uniqueId(): string
    {
        return $this->user->id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        sleep(10);

        // 更新用户余额操作
        $this->user->balance += $this->amount;
        $this->user->save();

    }
}
