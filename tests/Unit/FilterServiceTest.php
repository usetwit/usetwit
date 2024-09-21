<?php

namespace Tests\Unit;

use App\Exceptions\FilterServiceGetTypeInvalidException;
use App\Services\FilterService;
use Mockery;
use Mockery\LegacyMockInterface;
use Tests\TestCase;
use Illuminate\Contracts\Database\Query\Builder as BuilderContract;
use Illuminate\Database\Eloquent\Builder;

class FilterServiceTest extends TestCase
{
    protected Builder|LegacyMockInterface $builder;
    protected FilterService $filter;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = Mockery::mock(Builder::class);
        $this->filter = new FilterService();

        $this->app->instance(BuilderContract::class, $this->builder);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_sort_method_applies_order_by_correctly(): void
    {
        $this->builder->shouldReceive('orderBy')->with('name', 'asc')->once();
        $this->builder->shouldReceive('orderBy')->with('age', 'desc')->times(2);

        $sort = [
            ['field' => 'name', 'order' => 1],
            ['field' => 'age', 'order' => -1],
            ['field' => 'age', 'order' => 'test'],
        ];

        $this->filter->sort($this->builder, $sort);
    }

    public function test_sort_method_does_not_apply_order_by_when_exceptions_specified(): void
    {
        $this->builder->shouldNotReceive('orderBy');

        $sort = [['field' => 'name', 'order' => 1]];
        $exceptions = ['name'];

        $this->filter->sort($this->builder, $sort, $exceptions);
    }

    public function test_sort_method_correctly_substitutes_column_names(): void
    {
        $this->builder->shouldReceive('orderBy')->with('name.test', 'asc')->once();

        $sort = [['field' => 'name', 'order' => 1]];
        $substitutes = ['name' => 'name.test'];

        $this->filter->sort($this->builder, $sort, [], $substitutes);
    }

    public function test_make_validation_filter_rules_throws_exception_for_invalid_type(): void
    {
        $this->expectException(FilterServiceGetTypeInvalidException::class);

        $this->filter->makeValidationFilterRules('invalid_type', ['name']);
    }

    public function test_get_valid_date_match_modes_returns_correct_modes(): void
    {
        $expected = ['date_equals', 'date_not_equals', 'date_before', 'date_after'];

        $result = $this->filter->getValidDateMatchModes();

        $this->assertEquals($expected, $result);
    }

    public function test_get_valid_string_match_modes_as_string(): void
    {
        $expected = 'contains,starts_with,ends_with,equals,not_equals,gt,gte,lt,lte';

        $result = $this->filter->getValidStringMatchModes(true);

        $this->assertEquals($expected, $result);
    }

    public function test_get_valid_number_match_modes_lowercase_array(): void
    {
        $expected = ['equals', 'not_equals', 'gt', 'gte', 'lt', 'lte'];

        $result = $this->filter->getValidNumberMatchModes(false, true);

        $this->assertEquals($expected, $result);
    }

    public function test_get_match_modes_throws_exception_for_invalid_type(): void
    {
        $this->expectException(FilterServiceGetTypeInvalidException::class);

        $this->filter->getMatchModes('invalid_type');
    }
}
