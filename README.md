Starting with ```php 7.4``` I started getting errors.

**Typed property must not be accessed before initialization**

Yii2 allows to create a model without data and the program works
until ```[[validate]]``` is called.

Moreover, we don't receive a validation error, but a php error.

There are the following calls ```$model->$attribute``` when an attribute value is validating and adding an error.
What generates the error.

There are 5 ways:
1) Add getter and setter everywhere - a bunch of methods, not clear
2) Remove types, it's a return to php 7.3 and is not clear
3) Change all properties to ?int $attribute = null. Not clear
4) Change public to private and override __get from Component. This method won't work at all (1)
5) Add ```[['prop1', 'prop2'], InitializedValidator::class]``` before other rules for user land code. Great, this is clear. But you'll have to rewrite many models and projects. I would like this to work like in php<=7.3

**Conclusion**

It is necessary to slightly change the internal ```RequiredValidator``` so that it checks the initialization of the fields itself.
There is a modified version in the ```src``` folder.

(1) - When accessing private fields, the __get magic method will be called.
There we can check that the property is not initialized and return ```null``` in
```[[validateAttribute]]``` and ```[[addError]]``` validator. We will get the required validation error.

BUT! When we call ```[[setAttributes]]```, we will get an attempt to set values in private properties.
And it will either be **property not found**, or you need to change **__set($name, $value)** and all private ones will become public.
Not clear. 

Adding only the ```setName()``` setter will make the fields writeOnly and will cause an error when read.