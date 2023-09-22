<?php

namespace Bloom\Permission;

use Closure;
use Illuminate\Contracts\Pagination\Paginator;

class Response
{
    /**
     * 成功返回
     *
     * @param array               $data
     * @param null|array|callable $transformer
     *
     * @return array
     */
    public function success(array $data, $transformer = null)
    {
        $data = call_user_func($transformer, $data);

        return [
            'success' => true,
            'message' => '',
            'data'    => $data,
        ];
    }

    /**
     * 返回集合
     *
     * @param array               $data
     * @param null|array|callable $transformer
     *
     * @return array
     */
    public function collection(array $data, $transformer = null)
    {
        $result = [];

        foreach ($data as $item) {
            $result[] = call_user_func($transformer, $item);
        }

        return [
            'success' => true,
            'message' => '',
            'data'    => $result,
        ];
    }

    /**
     * 返回数组
     *
     * @param array $data
     *
     * @return array
     */
    public function array(array $data)
    {
        return [
            'success' => true,
            'message' => '',
            'data'    => $data,
        ];
    }

    /**
     * 失败返回
     *
     * @return array
     */
    public function fail()
    {
        return [
            'success' => false,
            'message' => '',
            'data'    => [],
        ];
    }

    /**
     * 单条记录返回
     *
     * @param array               $data
     * @param null|array|callable $transformer
     *
     * @return array
     */
    public function item(array $data, $transformer = null)
    {
        if (is_array($transformer)) {
            $transformer = Closure::fromCallable($transformer);
        }

        return empty($data) ? $this->fail() : $this->success($data, $transformer);
    }

    /**
     * 分页数据返回
     *
     * @param Paginator           $paginator
     * @param null|array|callable $transformer
     *
     * @return array
     */
    public function paginator(Paginator $paginator, $transformer = null)
    {
        if (is_array($transformer)) {
            $transformer = Closure::fromCallable($transformer);
        }

        $items = collect($paginator->items())
            ->map(function ($item) {
                return (array) $item;
            })
            ->toArray();

        if ($transformer) {
            $items = array_map(function ($item) use ($transformer) {
                return call_user_func($transformer, $item);
            }, $items);
        }

        return [
            'data' => $items,
            'meta' => [
                'pagination' => [
                    'count'        => $paginator->count(),
                    'current_page' => $paginator->currentPage(),
                    'per_page'     => $paginator->perPage(),
                    'total'        => $paginator->total(),
                    'total_pages'  => $paginator->lastPage(),
                ],
            ],
        ];
    }
}
