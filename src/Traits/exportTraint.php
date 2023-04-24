<?php

namespace Toproplus\Export\Traits;


trait exportTraint
{
    /**
     * 导出CSV
     * @param string $file_name 文件名
     * @param array $head_list 表头
     * @param int $data_count 总数
     * @param int $page_limit 每页数量
     * @param \Closure $call_back 获取分页数据的回调函数
     */
    function exportCsv(string $file_name, array $head_list, \Closure $call_back, int $data_count, int $page_limit = 1000)
    {
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name . '.csv"');
        header('Cache-Control: max-age=0');
        //打开PHP文件句柄,php://output 表示直接输出到浏览器
        $fp = fopen('php://output', 'a');
        $head = [];
        //输出Excel列名信息
        foreach ($head_list as $name) {
            //CSV的Excel支持GBK编码，一定要转换，否则乱码
            $head[] = iconv('utf-8', 'gbk', $name);
        }
        //将数据通过fputcsv写到文件句柄
        fputcsv($fp, $head);
        $page_limit = $data_count > $page_limit ? $page_limit : $data_count;
        $page_count = ceil($data_count / $page_limit);
        for ($page = 1; $page <= $page_count; $page++) {
            //逐页取出数据，不浪费内存
            $data_list = call_user_func($call_back, $page, $page_limit);
            if (!$data_list) continue;
            foreach ($data_list as $data) {
                $row = [];
                foreach ($head_list as $key => $name) {
                    $value = (string) $data[$key] ?? '';
                    $row[] = mb_convert_encoding($value, 'gbk', 'utf-8');
//                    $row[] = iconv('utf-8', 'gbk', $value);
                }
                fputcsv($fp, $row);
            }
            //刷新一下输出buffer，防止由于数据过多内存不足
            ob_flush();
            flush();
        }
        exit();
    }

}