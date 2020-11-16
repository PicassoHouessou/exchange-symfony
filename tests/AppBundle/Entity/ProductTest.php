<?php


namespace App\Tests\AppBundle\Entity;


use App\AppBundle\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testcomputeTVAFoodProduct()
    {
        $product = new Product('Un produit', Product::FOOD_PRODUCT, 20);

        $this->assertSame(1.1, $product->computeTVA());
    }

    public function testComputeTVADifferentProduct()
    {
        $product = new Product('Un produit', 'mangue', 20);
        $this->assertNotSame(Product::FOOD_PRODUCT, $product->type);
    }
}