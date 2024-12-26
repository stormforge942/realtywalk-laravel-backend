<?php namespace Tests\Repositories;

use App\Models\Builder;
use App\Repositories\BuilderRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BuilderRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BuilderRepository
     */
    protected $builderRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->builderRepo = \App::make(BuilderRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_builder()
    {
        $builder = factory(Builder::class)->make()->toArray();

        $createdBuilder = $this->builderRepo->create($builder);

        $createdBuilder = $createdBuilder->toArray();
        $this->assertArrayHasKey('id', $createdBuilder);
        $this->assertNotNull($createdBuilder['id'], 'Created Builder must have id specified');
        $this->assertNotNull(Builder::find($createdBuilder['id']), 'Builder with given id must be in DB');
        $this->assertModelData($builder, $createdBuilder);
    }

    /**
     * @test read
     */
    public function test_read_builder()
    {
        $builder = factory(Builder::class)->create();

        $dbBuilder = $this->builderRepo->find($builder->id);

        $dbBuilder = $dbBuilder->toArray();
        $this->assertModelData($builder->toArray(), $dbBuilder);
    }

    /**
     * @test update
     */
    public function test_update_builder()
    {
        $builder = factory(Builder::class)->create();
        $fakeBuilder = factory(Builder::class)->make()->toArray();

        $updatedBuilder = $this->builderRepo->update($fakeBuilder, $builder->id);

        $this->assertModelData($fakeBuilder, $updatedBuilder->toArray());
        $dbBuilder = $this->builderRepo->find($builder->id);
        $this->assertModelData($fakeBuilder, $dbBuilder->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_builder()
    {
        $builder = factory(Builder::class)->create();

        $resp = $this->builderRepo->delete($builder->id);

        $this->assertTrue($resp);
        $this->assertNull(Builder::find($builder->id), 'Builder should not exist in DB');
    }
}
