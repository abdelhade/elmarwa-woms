<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            'clients' => 'العملاء',
            'products' => 'المنتجات',
            'categories' => 'الفئات',
            'work-orders' => 'أوامر العمل',
            'order-batches' => 'دفعات الطلبات',
            'attributes' => 'الخصائص',
            'users' => 'المستخدمين',
        ];

        $actions = [
            'view' => 'عرض',
            'create' => 'إضافة',
            'edit' => 'تعديل',
            'delete' => 'حذف',
            'print' => 'طباعة',
        ];

        $count = 0;
        foreach ($modules as $module => $moduleArabic) {
            foreach ($actions as $action => $actionArabic) {
                // Skip print permission for users module
                if ($module === 'users' && $action === 'print') {
                    continue;
                }

                Permission::firstOrCreate(
                    ['name' => "{$module}.{$action}"],
                    [
                        'display_name' => "{$actionArabic} {$moduleArabic}",
                        'module' => $module,
                    ]
                );
                $count++;
            }
        }
        
        $this->command->info("Created {$count} permissions");
    }
}
