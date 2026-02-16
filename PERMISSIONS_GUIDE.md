# دليل نظام الصلاحيات

## نظرة عامة
تم إضافة نظام صلاحيات كامل للمشروع بدون استخدام مكتبات خارجية. النظام يدعم:
- صلاحيات على مستوى المستخدم
- 5 صلاحيات لكل موديول (عرض، إضافة، تعديل، حذف، طباعة)
- مستخدم Admin له جميع الصلاحيات

## الموديولات المدعومة
1. **العملاء (clients)**: view, create, edit, delete, print
2. **المنتجات (products)**: view, create, edit, delete, print
3. **الفئات (categories)**: view, create, edit, delete, print
4. **أوامر العمل (work-orders)**: view, create, edit, delete, print
5. **دفعات الطلبات (order-batches)**: view, create, edit, delete, print
6. **الخصائص (attributes)**: view, create, edit, delete, print
7. **المستخدمين (users)**: view, create, edit, delete

## بيانات الدخول للتجربة
- **البريد الإلكتروني**: admin@admin.com
- **كلمة المرور**: 123456
- **النوع**: مدير (لديه جميع الصلاحيات)

## كيفية الاستخدام

### 1. إضافة مستخدم جديد
1. سجل الدخول كمدير
2. اذهب إلى "المستخدمين" من القائمة الجانبية
3. اضغط "إضافة مستخدم جديد"
4. املأ البيانات واختر الصلاحيات المطلوبة
5. إذا كان المستخدم مدير، فقط ضع علامة على "مدير النظام"

### 2. تعديل صلاحيات مستخدم
1. اذهب إلى قائمة المستخدمين
2. اضغط "تعديل" على المستخدم المطلوب
3. عدل الصلاحيات حسب الحاجة
4. احفظ التعديلات

### 3. التحقق من الصلاحيات في الكود

#### في Controllers:
```php
// التحقق يتم تلقائياً عبر middleware في routes
```

#### في Blade Views:
```blade
@if(auth()->user()->hasPermission('clients.view'))
    <a href="{{ route('clients.index') }}">عرض العملاء</a>
@endif

@permission('clients.create')
    <button>إضافة عميل</button>
@endpermission

@admin
    <p>هذا المحتوى للمدراء فقط</p>
@endadmin
```

#### في PHP:
```php
// التحقق من صلاحية معينة
if (auth()->user()->hasPermission('clients.create')) {
    // الكود هنا
}

// التحقق إذا كان المستخدم مدير
if (auth()->user()->isAdmin()) {
    // الكود هنا
}
```

## الملفات المهمة

### Models
- `app/Models/User.php` - موديل المستخدم مع علاقات الصلاحيات
- `app/Models/Permission.php` - موديل الصلاحيات

### Middleware
- `app/Http/Middleware/CheckPermission.php` - للتحقق من الصلاحيات

### Controllers
- `app/Http/Controllers/UserController.php` - إدارة المستخدمين

### Views
- `resources/views/users/index.blade.php` - قائمة المستخدمين
- `resources/views/users/create.blade.php` - إضافة مستخدم
- `resources/views/users/edit.blade.php` - تعديل مستخدم

### Database
- `database/migrations/*_create_permissions_table.php`
- `database/migrations/*_create_user_permissions_table.php`
- `database/migrations/*_add_is_admin_to_users_table.php`
- `database/seeders/PermissionsSeeder.php` - لإضافة الصلاحيات الأساسية

## إضافة موديول جديد

لإضافة موديول جديد بصلاحياته:

1. أضف الصلاحيات في `PermissionsSeeder.php`:
```php
'new-module' => 'الموديول الجديد',
```

2. شغل الـ seeder:
```bash
php artisan db:seed --class=PermissionsSeeder
```

3. أضف الـ routes مع middleware:
```php
Route::middleware('permission:new-module.view')->group(function () {
    Route::get('new-module', [NewModuleController::class, 'index']);
});
```

4. أضف الرابط في القائمة الجانبية:
```blade
@if(auth()->user()->hasPermission('new-module.view'))
    <a href="{{ route('new-module.index') }}">الموديول الجديد</a>
@endif
```

## ملاحظات مهمة
- المدير (is_admin = true) لديه جميع الصلاحيات تلقائياً
- لا يمكن للمستخدم حذف حسابه الخاص
- القائمة الجانبية تظهر فقط الروابط التي لدى المستخدم صلاحية الوصول إليها
- عند محاولة الوصول لصفحة بدون صلاحية، يظهر خطأ 403
