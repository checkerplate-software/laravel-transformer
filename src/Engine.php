<?php

namespace CheckerplateSoftware\LaravelTransformer;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use JsonSerializable;
use Exception;

class Engine implements Arrayable, Jsonable, JsonSerializable
{
    /**
     * The data to transform.
     *
     * @var Model|Collection
     */
    private $data;

    /**
     * The transformer to run.
     *
     * @var AbstractTransformer
     */
    private $transformer;

    /**
     * Create an instance of the transformer engine.
     *
     * @param Model|Collection $data
     * @param AbstractTransformer $transformer
     * @throws Exception
     */
    public function __construct($data, $transformer = null)
    {
        // We only support Models and Eloquent Collections
        if (! is_a($data, Model::class) && ! is_a($data, Collection::class)) {
            throw new Exception('The supplied data is not supported by the transformer engine.');
        }

        if (is_null($transformer)) {
            // Only models have have null transformers
            if (! is_a($data, Model::class)) {
                throw new Exception('You must supply a transformer if transforming anything other than a Model.');
            }

            // Check to see if the supplied model has a default transformer
            if (! array_key_exists(get_class($data), config('transformer.transformers'))) {
                throw new Exception('A default transformer has not be supplied for ' . get_class($data) . '.');
            }

            // Create the transformer
            $transformer = app(config('transformer.transformers')[get_class($data)]);
        }

        // Ensure we were provided a valid transformer
        if (! is_a($transformer, AbstractTransformer::class)) {
            throw new Exception('The supplied transformer is not supported by the transformer engine.');
        }

        $this->data        = $data;
        $this->transformer = $transformer;
    }

    /**
     * Transform the data into a JSON string.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Convert the data into a JSON serializable structure.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Transform the data into an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->run();
    }

    /**
     * This will run the transformer over the data.
     *
     * Situations where calling this method directly might be useful
     * include using a transform within another transformer.
     *
     * @return array
     */
    public function run()
    {
        if (is_a($this->data, Collection::class)) {
            return $this->data->map(function ($model) {
                return $this->transformer->run($model);
            })->toArray();
        }

        return $this->transformer->run($this->data);
    }
}
