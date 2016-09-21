<?php

if (! function_exists('transform')) {
    /**
     * @param \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Collection $data
     * @param \CheckerplateSoftware\LaravelTransformer\AbstractTransformer|null $transformer
     * @return \CheckerplateSoftware\LaravelTransformer\Engine
     */
    function transform($data, $transformer = null)
    {
        return new \CheckerplateSoftware\LaravelTransformer\Engine($data, $transformer);
    }
}