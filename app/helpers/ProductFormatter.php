<?php
class ProductFormatter
{
  public static function format(array $product)
  {
    return [
      'productId' => $product['product_id'],
      'product' => [
        'productName' => $product['product_name'],
        'productImagePath' => $product['productImagePath'],
        'price' => $product['price'],
        'isSold' => $product['isSold'],
        'date' => $product['datetime']
      ]
    ];
  }
}
