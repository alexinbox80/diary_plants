<?php

namespace Unit\Domain\Service\Csv\Import;

use PHPUnit\Framework\TestCase;
use App\Domain\Service\ModelFactory;
use App\Domain\Service\GroupService;
use App\Domain\Model\Group\CreateGroupModel;
use App\Domain\Service\Csv\Import\GroupImporter;

class GroupImporterTest extends TestCase
{
    private ModelFactory $modelFactory;
    private GroupService $groupService;
    private GroupImporter $importer;

    protected function setUp(): void
    {
        $this->modelFactory = $this->createMock(ModelFactory::class);
        $this->groupService = $this->createMock(GroupService::class);

        $this->importer = new GroupImporter(
            $this->modelFactory,
            $this->groupService
        );
    }

    public function testSupportsReturnsTrueForGroup(): void
    {
        $this->assertTrue($this->importer->supports('group'));
        $this->assertFalse($this->importer->supports('pest'));
    }

    public function testImportSuccessfullyCreatesModel(): void
    {
        // Данные имитируют разные варианты булевых значений из CSV
        $data = [
            'is_active' => '1',      // str2bool('1') -> true
            'title' => 'Цитрусовые',
            'description' => ''      // es('') -> null
        ];

        $expectedArgs = [
            true,           // is_active
            'Цитрусовые',   // title
            null            // description
        ];

        $mockModel = $this->createMock(CreateGroupModel::class);

        // Проверяем вызов фабрики с преобразованными данными
        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(CreateGroupModel::class, ...$expectedArgs)
            ->willReturn($mockModel);

        // Проверяем передачу модели в сервис
        $this->groupService->expects($this->once())
            ->method('create')
            ->with($mockModel);

        $this->importer->import($data);
    }

    public function testImportHandlesInactiveGroup(): void
    {
        $data = [
            'is_active' => 'false', // str2bool('false') -> false
            'title' => 'Архив',
            'description' => 'Старая группа'
        ];

        $this->modelFactory->expects($this->once())
            ->method('makeModel')
            ->with(CreateGroupModel::class, false, 'Архив', 'Старая группа')
            ->willReturn($this->createMock(CreateGroupModel::class));

        $this->importer->import($data);
    }
}
