
# cart

## create

```php
Kcart::create($authUser);
```

## userCart

```php
Kcart::userCart($authUser);
```

## paid

```php
Kcart::paid($cartId);
```

## coupon

```php
Kcart::coupon($id,$code,$type,$amount);
```

## get

```php 
Kcart::get($cart_id,$authUser);
```


# cart item

## add

```php
KcartItem::add()
```

## remove

```php
KcartItem::remove($cartID,$productID)
```

## remove all

```php
KcartItem::removeAll($cartID)
```

## update

```php
KcartItem::update($cartID,$productID,$quantity)
```

## coupon

```php
KcartItem::haveCoupon($cartID,$productID,$code,$type,$amount)
```

## remove coupon

```php
KcartItem::haveCouponRemove($cartID,$productID)
```
