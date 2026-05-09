<?php

namespace Unit\Controller\Form;

use App\Domain\Service\GroupService;
use App\Domain\Service\MarkerService;
use PHPUnit\Framework\Attributes\Test;
use App\Controller\Form\FertilizerType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\PreloadedExtension;
use App\Controller\Web\Dashboard\Fertilizer\EditFertilizer\Input\EditFertilizerDTO;

#[CoversClass(FertilizerType::class)]
class FertilizerTypeTest extends TypeTestCase
{
    private MockObject|MarkerService $markerService;
    private MockObject|GroupService $groupService;
    private MockObject|Security $security;

    protected function setUp(): void
    {
        // Создаем все необходимые моки
        $this->markerService = $this->createMock(MarkerService::class);
        $this->groupService = $this->createMock(GroupService::class);
        $this->security = $this->createMock(Security::class);

        // Настраиваем моки, чтобы форма могла построиться без ошибок
        $this->markerService->method('getChoicesForChoiceType')
            ->willReturn(['Label 1' => 1, 'Label 2' => 2]);

        $this->groupService->method('getChoicesForFormChoiceType')
            ->willReturn(['Group 1' => 1]);

        parent::setUp();
    }

    /**
     * Передаем форму с внедренным моком в механизм тестирования Symfony
     */
    protected function getExtensions(): array
    {
        $type = new FertilizerType(
            $this->security,
            $this->groupService,
            $this->markerService
        );

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    #[Test]
    public function testSubmitValidData(): void
    {
        // 2. Входные данные, которые «пришли» из браузера
        $formData = [
            'markerId' => 1,
            'title' => 'Осмокот',
            'amount' => 500,
            'applicationRate' => '3г/л',
            'manufacturer' => 'Everris',
            'description' => 'Удобрение',
            'comment' => 'Топ',
        ];

        // Объект, в который форма должна записать данные (DTO)
        $model = new EditFertilizerDTO(
            groupId: 1,
            markerId: 0,
            title: '',
            amount: 0,
            applicationRate: '',
            manufacturer: ''
        );

        // 3. Создаем и отправляем форму
        $form = $this->factory->create(FertilizerType::class, $model, [
            'group_id' => 1,
            'is_new' => true
        ]);

        $form->submit($formData);

        // 4. Проверки
        $this->assertTrue($form->isSynchronized()); // Данные трансформировались верно
        $this->assertTrue($form->isValid());

        // Проверяем, что DTO заполнился правильно
        $this->assertEquals(1, $model->markerId);
        $this->assertEquals('Осмокот', $model->title);
        $this->assertEquals('500', $model->amount);

        // Проверяем, что вьюха формы содержит все нужные поля
        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}
