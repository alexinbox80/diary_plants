<?php

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Task;
use App\Domain\Entity\Status;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    private function getProperty($object, $property, $data): mixed
    {
        $reflection = new \ReflectionClass($object);
        $propertyRef = $reflection->getProperty($property);
        $propertyRef->setAccessible(true);
        if ($data) {
            $propertyRef->setValue($object, $data);
        }
        return $propertyRef->getValue($object);
    }

    public function testConstructorInitializesAllProperties(): void
    {
        $status = new Status('L', '#aabbcc');
        $date = new DateTimeImmutable();
        $description = 'Complete the task';

        $task = new Task($status, $date, $description);

        $this->assertSame($status, $task->getStatus());
        $this->assertSame($date, $task->getDate());
        $this->assertSame($description, $task->getDescription());
    }

    public function testChangeFieldsUpdatesAllProperties(): void
    {
        $task = new Task(new Status('L', '#aabbcc'), new DateTimeImmutable());

        $newStatus = new Status('K', '#aabbdd');
        $newDate = (clone $task->getDate())->modify('+1 day');
        $newDescription = 'Updated description';

        $task->changeFields($newStatus, $newDate, $newDescription);

        $this->assertSame($newStatus, $task->getStatus());
        $this->assertSame($newDate, $task->getDate());
        $this->assertSame($newDescription, $task->getDescription());
    }

    public function testGettersReturnCorrectValues(): void
    {
        $task = new Task(new Status('A', '#aabbdd'), new DateTimeImmutable(), 'Do something');

        $this->assertInstanceOf(Status::class, $task->getStatus());
        $this->assertInstanceOf(DateTimeImmutable::class, $task->getDate());
        $this->assertSame('Do something', $task->getDescription());
    }

    public function testToArrayReturnsExpectedArray(): void
    {
        $status =  new Status('K', '#aabbdd');

        $this->getProperty($status, 'id', 1);
        $this->getProperty($status, 'createdAt', new DateTimeImmutable('2025-04-01'));
        $this->getProperty($status, 'updatedAt', new DateTimeImmutable('2025-04-01'));

        $task = new Task(
            status: $status,
            date: new DateTimeImmutable('2025-04-01'),
            description: 'Sample task'
        );

        $this->getProperty($task, 'id', 1);
        $this->getProperty($task, 'createdAt', new DateTimeImmutable());
        $this->getProperty($task, 'updatedAt', new DateTimeImmutable());

        // Применяем трейты CreatedAtTrait и UpdatedAtTrait
        //$task->touch(); // чтобы установить updatedAt

        $array = $task->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('status', $array);
        $this->assertArrayHasKey('date', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('created_at', $array);
        $this->assertArrayHasKey('updated_at', $array);

        $this->assertSame('Sample task', $array['description']);
        $this->assertSame('2025-04-01', $array['date']);
        $this->assertSame([
            'id' => 1,
            'letter' => 'K',
            'color' => '#aabbdd',
            'description' => null,
            'color_description' => null,
            'created_at' => '2025-04-01 00:00:00',
            'updated_at' => '2025-04-01 00:00:00',
        ], $array['status']);
    }

    public function testGetIdThrowsExceptionWhenIdIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $task = new Task(new Status('K', '#aabbdd'), new DateTimeImmutable());
        $task->getId();
    }
}
