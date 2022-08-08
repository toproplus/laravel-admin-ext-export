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
$csvExport->setName('会员列表');
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

- 追加导出的字段
```php
// 追加表格中未显示，但列表数据中已存在的字段值
$csvExport->addColumn('goods_spec', '商品规格');
// 追加字段并修改输出的值
$csvExport->addColumn('goods_spec', '商品规格', function($data) {
    return '规格：' . $data['goods_spec'];
});
// 追加到指定字段的后面
// 追加 goods_spec 到 goods_name 的后面
$csvExport->addColumn('goods_spec', '商品规格', 'goods_name');
// 追加到指定字段后，并修改输出值
$csvExport->addColumn('goods_spec', '商品规格', 'goods_name', function($data) {
    return '规格：' . $data['goods_spec'];
});
// 追加二维（或多维）数组下的字段，键用“.”相连
$csvExport->addColumn('posts.comments.user_id', '文章评论人ID');
// 追加不存在的字段并设置输出值
// 追加不存在的字段，一定要设置输出值
$csvExport->addColumn('hahaha', '哈哈哈', function($data) {
    return '哈哈哈' . $data['name'];
});

``` 

- 列表数据重新整理
 ```php
 $csvExport->setList(function ($columns, $list) {
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