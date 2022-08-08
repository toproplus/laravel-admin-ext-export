<?php

namespace Toproplus\Export\Widgets;


use Toproplus\Export\Traits\exportTraint;
use Encore\Admin\Grid\Exporters\AbstractExporter;

class CsvExport extends AbstractExporter
{
    use exportTraint;

    protected $fileName = null;
    protected $debug = false;
    protected $dealColumnList = [];
    protected $columnCallback = null;

    /**
     * 导出
     * @return mixed|void
     */
    public function export()
    {
        $model = $this;
        $grid = $this->grid;
        $columns = $this->getColumns();
        $call_back = function ($page, $limit) use ($model, $grid, $columns) {
            $model->page = $page;
            $grid->paginate($limit);
            $model->setGrid($grid);
            $list = $model->getQuery()->get()->toArray();
            $list = $this->dealColumn($columns, $list);
            if ($this->columnCallback) {
                $list = call_user_func($this->columnCallback, $columns, $list);
            }
            return $list;
        };
        if ($this->debug) {
            dd($columns, call_user_func($call_back, 1, 20));
        }
        $file_name = $this->fileName ? $this->fileName : $this->getTable() . date('YmdHis');
        $data_count = $this->getQuery()->count();
        $this->exportCsv($file_name, $columns, $call_back, $data_count);

    }

    /**
     * 设置文件名
     * @param null $file_name
     */
    public function setFileName($file_name = null)
    {
        if ($file_name) {
            $this->fileName = $file_name;
        }
    }

    /**
     * 设置处理字段值的规则
     * @param $column 字段名
     * @param \Closure $call_back 规则
     */
    public function setColumn($column, \Closure $call_back)
    {
        $this->dealColumnList[$column] = $call_back;
    }

    /**
     * 字段值重新处理 回调函数
     * @param \Closure $call_back
     */
    public function setColumnCallback(\Closure $call_back)
    {
        $this->columnCallback = $call_back;
    }

    /**
     * 处理有共同特征的字段值
     * @param $columns
     * @param $list
     * @return mixed
     */
    public function dealColumn($columns, $list)
    {
        if (!$list) return $list;
        $deal = array_keys($this->dealColumnList);
        foreach ($list as $index => $data) {
            foreach ($columns as $column => $name) {
                switch ($column) {
                    case strpos($column, '.') !== false :
                        $keys = explode('.', $column);
                        $value = $data;
                        for ($i = 0; $i < count($keys); $i ++) {
                            $value = $value[$keys[$i]] ?? [];
                        }
                        if (is_array($value)) {
                            $value = empty($value) ? '' : json_encode($value, JSON_UNESCAPED_UNICODE);
                        }
                        $data[$column] = $value;
                        break;
                }
                if ($this->dealColumnList && in_array($column, $deal)) {
                    $data[$column] = call_user_func_array($this->dealColumnList[$column], [$data[$column], $data]);
                }
            }
            $list[$index] = $data;
        }
        return $list;
    }

    /**
     * 获取选中的导出字段
     * @return array
     */
    public function getColumns()
    {
        $columns = [];
        foreach ($this->grid->getColumns() as $column) {
            $columns[$column->getName()] = $column->getLabel();
        }
        $column_string = request()->get('_columns_', '');
        if (!$column_string) return $columns;
        $column_array = explode(',', $column_string);
        $column_list= [];
        foreach ($columns as $column => $name) {
            if (in_array($column, $column_array)) {
                $column_list[$column] = $name;
            }
        }
        return $column_list;
    }

    /**
     * 打印
     */
    public function dd()
    {
        $this->debug = true;
    }

}