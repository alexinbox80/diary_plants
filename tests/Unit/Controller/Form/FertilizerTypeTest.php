<?php

namespace Unit\Controller\Form;

use App\Domain\Service\MarkerService;
use App\Controller\Form\FertilizerType;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Form\PreloadedExtension;
use App\Controller\Web\Dashboard\Fertilizer\EditFertilizer\Input\EditFertilizerDTO;

class FertilizerTypeTest extends TypeTestCase
{
    private MockObject|MarkerService $markerService;

    protected function setUp(): void
    {
        // 1. Создаем мок сервиса
        $this->markerService = $this->createMock(MarkerService::class);

        // Настраиваем мок, чтобы он возвращал тестовый список вариантов для ChoiceType
        $this->markerService->method('getChoicesForChoiceType')
            ->willReturn(['Label 1' => 1, 'Label 2' => 2]);

        parent::setUp();
    }

    /**
     * Передаем форму с внедренным моком в механизм тестирования Symfony
     */
    protected function getExtensions(): array
    {
        $type = new FertilizerType($this->markerService);

        return [
            new PreloadedExtension([$type], []),
        ];
    }

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
