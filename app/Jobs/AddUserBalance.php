<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddUserBalance implements ShouldQueue,ShouldBeUnique
{
    use Queueable;

    public $userId;
    public $amount;

    /**
     * Create a new job instance.
     */
    public function __construct($userId, $amount)
    {
        $this->userId = $userId;
        $this->amount = $amount;
    }

    /**
     * 任务唯一ID
     */
    public function uniqueId(): string
    {
        return $this->userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::transaction(function () {

                // 使用 lockForUpdate 加锁，确保行锁定
                $user = User::query()
                    ->where('id', $this->userId)
                    ->lockForUpdate()
                    ->firstOrFail();

                sleep(10);

                // 更新用户余额
                $user->balance += $this->amount;
                $user->save();
            });

        }catch (ModelNotFoundException $e) {
            Log::error("未找到用户信息 " . $e->getMessage());
        }  catch (\Throwable $e) {
            report($e);
        }
    }
}
