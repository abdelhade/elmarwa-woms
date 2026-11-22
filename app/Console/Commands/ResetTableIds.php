<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetTableIds extends Command
{
    protected $signature = 'db:reset-ids
                            {table : اسم الجدول}
                            {--force : تجاوز تأكيد الـ production}';

    protected $description = 'إعادة ترقيم الـ IDs من 1 وإعادة تعيين AUTO_INCREMENT';

    public function handle(): int
    {
        $table = $this->argument('table');

        // تحقق إن الجدول موجود
        if (! Schema::hasTable($table)) {
            $this->error("الجدول [{$table}] غير موجود.");
            return self::FAILURE;
        }

        // تأكيد في الـ production
        if (app()->isProduction() && ! $this->option('force')) {
            if (! $this->confirm("⚠️  أنت في بيئة production. هل تريد المتابعة على الجدول [{$table}]؟")) {
                $this->info('تم الإلغاء.');
                return self::SUCCESS;
            }
        }

        $count = DB::table($table)->count();
        $this->info("الجدول: [{$table}] — عدد الصفوف: {$count}");

        if ($count === 0) {
            $this->warn('الجدول فارغ، لا حاجة لإعادة الترقيم.');
            return self::SUCCESS;
        }

        $this->info('جاري إعادة الترقيم...');

        DB::statement('SET @count = 0');
        DB::statement("UPDATE `{$table}` SET `id` = @count := @count + 1 ORDER BY `id`");
        DB::statement("ALTER TABLE `{$table}` AUTO_INCREMENT = 1");

        $this->info("✅ تم إعادة ترقيم [{$table}] بنجاح. الـ IDs الآن من 1 إلى {$count}.");

        return self::SUCCESS;
    }
}
