<?php

namespace Bloom\Permission;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

/**
 * 记录从库查询语句
 */
class QueryListener
{
    /**
     * 日志 channel
     *
     * @var string
     */
    protected $channel = 'permission-sdk';

    /**
     * @param QueryExecuted $query
     */
    public function handle(QueryExecuted $query)
    {
        // 只记录当前包用到的数据库连接
        if ($query->connectionName !== 'permission') {
            return;
        }

        if (!config('permission.log_query')) {
            return;
        }

        // 不同的时间消耗，定义不同的日志级别
        if ($query->time >= 10000) {
            $level = Logger::ERROR;
        } elseif ($query->time >= 5000) {
            $level = Logger::WARNING;
        } elseif ($query->time >= 1000) {
            $level = Logger::INFO;
        } else {
            $level = Logger::DEBUG;
        }

        $message = sprintf(
            '[%s] %s [%s ms]',
            $query->connectionName,
            $this->parse($query),
            $query->time
        );

        Log::channel($this->channel)->log(Logger::getLevelName($level), $message, $query->bindings);
    }

    /**
     * sql 语句中的问号替换为具体参数
     *
     * @param QueryExecuted $query
     *
     * @return string
     */
    protected function parse(QueryExecuted $query)
    {
        $sql = str_replace('?', '%s', $query->sql);

        // binding 替换
        $bindings = array_map(function ($binding) {
            if ($binding instanceof Carbon) {
                return '"'.$binding->toDateTimeString().'"';
            }

            if (is_string($binding)) {
                return "\"{$binding}\"";
            }

            return $binding;
        }, $query->bindings);

        return sprintf($sql, ...$bindings);
    }
}
