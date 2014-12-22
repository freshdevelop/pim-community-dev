<?php

namespace spec\Pim\Bundle\CatalogBundle\Updater\Setter;

use PhpSpec\ObjectBehavior;
use Pim\Bundle\CatalogBundle\Builder\ProductBuilderInterface;
use Pim\Bundle\CatalogBundle\Model\AttributeInterface;
use Pim\Bundle\CatalogBundle\Updater\InvalidArgumentException;
use Pim\Bundle\CatalogBundle\Updater\Setter\AbstractValueSetter;
use Pim\Bundle\CatalogBundle\Validator\AttributeValidatorHelper;
use Prophecy\Argument;

class AbstractValueSetterSpec extends ObjectBehavior
{
    function let(ProductBuilderInterface $productBuilder, AttributeValidatorHelper $attributeValidatorHelper)
    {
        $this->beAnInstanceOf('spec\Pim\Bundle\CatalogBundle\Updater\Setter\ConcreateValueSetter');
        $this->beConstructedWith($productBuilder, $attributeValidatorHelper);
    }

    function it_is_a_setter()
    {
        $this->shouldHaveType('Pim\Bundle\CatalogBundle\Updater\Setter\SetterInterface');
    }

    function it_throws_an_exception_when_locale_is_expected(
        $attributeValidatorHelper,
        AttributeInterface $attribute
    ) {
        $e = new \LogicException('Attribute "attributeCode" expects a locale, none given.');

        $attribute->getCode()->willReturn('attributeCode');
        $attribute->isLocalizable()->willReturn(true);
        $attributeValidatorHelper->validateLocale($attribute, null)->willThrow($e);

        $this->shouldThrow(
            InvalidArgumentException::expectedFromPreviousException($e, 'attributeCode', 'setter', 'concrete')
        )->during('setValue', [[], $attribute, Argument::any(), null, 'ecommerce', 'concrete']);
    }

    function it_throws_an_exception_when_locale_is_not_expected(
        $attributeValidatorHelper,
        AttributeInterface $attribute
    ) {
        $e = new \LogicException('Attribute "attributeCode" does not expect a locale, "en_US" given.');

        $attribute->getCode()->willReturn('attributeCode');
        $attribute->isLocalizable()->willReturn(false);
        $attributeValidatorHelper->validateLocale($attribute, 'en_US')->willThrow($e);

        $this->shouldThrow(
            InvalidArgumentException::expectedFromPreviousException($e, 'attributeCode', 'setter', 'concrete')
        )->during('setValue', [[], $attribute, Argument::any(), 'en_US', 'ecommerce', 'concrete']);
    }

    function it_throws_an_exception_when_locale_is_expected_but_not_activated(
        $attributeValidatorHelper,
        AttributeInterface $attribute
    ) {
        $e = new \LogicException('Attribute "attributeCode" expects an existing and activated locale, "uz-UZ" given.');

        $attribute->getCode()->willReturn('attributeCode');
        $attribute->isLocalizable()->willReturn(true);
        $attributeValidatorHelper->validateLocale($attribute, 'uz-UZ')->willThrow($e);

        $this->shouldThrow(
            InvalidArgumentException::expectedFromPreviousException($e, 'attributeCode', 'setter', 'concrete')
        )->during('setValue', [[], $attribute, Argument::any(), 'uz-UZ', 'ecommerce', 'concrete']);
    }

    function it_throws_an_exception_when_scope_is_expected(
        $attributeValidatorHelper,
        AttributeInterface $attribute
    ) {
        $e = new \LogicException('Attribute "attributeCode" expects a scope, none given.');

        $attribute->getCode()->willReturn('attributeCode');
        $attribute->isLocalizable()->willReturn(false);
        $attribute->isScopable()->willReturn(true);
        $attributeValidatorHelper->validateLocale($attribute, null)->shouldBeCalled();
        $attributeValidatorHelper->validateScope($attribute, null)->willThrow($e);

        $this->shouldThrow(
            InvalidArgumentException::expectedFromPreviousException($e, 'attributeCode', 'setter', 'concrete')
        )->during('setValue', [[], $attribute, Argument::any(), null, null, 'concrete']);
    }

    function it_throws_an_exception_when_scope_is_not_expected(
        $attributeValidatorHelper,
        AttributeInterface $attribute
    ) {
        $e = new \LogicException('Attribute "attributeCode" does not expect a scope, "ecommerce" given.');

        $attribute->getCode()->willReturn('attributeCode');
        $attribute->isLocalizable()->willReturn(false);
        $attribute->isScopable()->willReturn(false);
        $attributeValidatorHelper->validateLocale($attribute, null)->shouldBeCalled();
        $attributeValidatorHelper->validateScope($attribute, 'ecommerce')->willThrow($e);

        $this->shouldThrow(
            InvalidArgumentException::expectedFromPreviousException($e, 'attributeCode', 'setter', 'concrete')
        )->during('setValue', [[], $attribute, Argument::any(), null, 'ecommerce', 'concrete']);
    }

    function it_throws_an_exception_when_scope_is_expected_but_not_existing(
        $attributeValidatorHelper,
        AttributeInterface $attribute
    ) {
        $e = new \LogicException('Attribute "attributeCode" expects an existing scope, "ecommerce" given.');

        $attribute->getCode()->willReturn('attributeCode');
        $attribute->isLocalizable()->willReturn(false);
        $attribute->isScopable()->willReturn(true);
        $attributeValidatorHelper->validateLocale($attribute, null)->shouldBeCalled();
        $attributeValidatorHelper->validateScope($attribute, 'ecommerce')->willThrow($e);

        $this->shouldThrow(
            InvalidArgumentException::expectedFromPreviousException($e, 'attributeCode', 'setter', 'concrete')
        )->during('setValue', [[], $attribute, Argument::any(), null, 'ecommerce', 'concrete']);
    }
}

class ConcreateValueSetter extends AbstractValueSetter
{
    public function setValue(array $products, AttributeInterface $attribute, $data, $locale = null, $scope = null)
    {
        $this->checkLocaleAndScope($attribute, $locale, $scope, 'concrete');
    }
}
