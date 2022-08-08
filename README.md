## laravel-admin 导出工具
laravel-admin extension export

### 安装

拉取 [package](https://packagist.org/packages/toproplus/laravel-admin-ext-export)
```bash
composer require toproplus/laravel-admin-ext-export
```

### 使用

- 直接导出
```php
use Toproplus\Export\Widgets\CsvExport;
...
$grid->exporter(new CsvExport());
```

- 自定义导出文件名
```php
$csvExport = new CsvExport();
$csvExport->setFileName('会员列表');
$grid->exporter($csvExport);
```
- 修改字段输出值
```php
// 修改add_time时间戳为日期格式
$csvExport->setColumn('add_time', function ($value) {
    return $value > 0 ? date('Y-m-d H:i:s', $value) : '';
});
// 如果要同时获取其他字段的值
$csvExport->setColumn('name', function ($name, $data) {
    return $name . '-' . $data['mobile'];
});
```

- 列表数据重新整理
 ```php
 $csvExport->setColumnCallback(function ($columns, $list) {
     foreach ($list as $index => $data) {
         foreach ($columns as $column => $name) {
             switch ($column) {
                 case 'status':
                     $status_list = [0 => '禁用', 1 => '启用'];
                     $data[$column] = $status_list[$data[$column]] ?? '未知';
                     break;
                 case in_array($column, ['add_time', 'create_time', 'last_time']):
                     $data[$column] = $data[$column] > 0 ? date('Y-m-d H:i:s', $data[$column]) : '';
                     break;
             }

         }
         $list[$index] = $data;
     }
     return $list;
 });
 ```
 
 - 打印调试
 ```php
 $csvExport->dd();
 ```