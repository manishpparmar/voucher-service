<?php
declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\OrderEntity;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class OrderEntityTest extends KernelTestCase
{
    public function testEntity(): void
    {
        foreach ($this->getTestCases() as $row => $testCase) {
            $actual = $testCase['actual'];
            $order = new OrderEntity();
            self::assertInstanceOf(OrderEntity::class, $order);
            $order->setOrderId($actual['order_id']);
            $order->setCustomerId($actual['customer_id']);

            $expected = $testCase['expected'];
            self::assertEquals($expected['order_id'], $order->getOrderId(), sprintf('Test case "%s" failed asserting the correct order id!', $row));
            self::assertEquals($expected['customer_id'], $order->getCustomerId(), sprintf('Test case "%s" failed asserting the correct customer id!', $row));
        }
    }

    private function getTestCases(): array
    {
        $cases[0] = [
            'actual' => [
                'id' => 1,
                'order_id' => 1,
                'customer_id' => 1
            ],
            'expected' => [
                'exception' => false,
                'id' => 1,
                'order_id' => 1,
                'customer_id' => 1
            ]
        ];

        return $cases;
    }
}
